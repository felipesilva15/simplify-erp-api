<?php

namespace App\Providers;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Str;
use ReflectionClass;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Illuminate\Support\Collection;

class MorphMapServiceProvider extends ServiceProvider
{
    private const CACHE_KEY = 'morph-map';

    public function boot(): void
    {
        Relation::enforceMorphMap($this->resolveMorphMap());
    }

    protected function resolveMorphMap(): array
    {
        $signature = $this->modelFilesSignature();
        $cached = Cache::get(self::CACHE_KEY);

        if (is_array($cached) && ($cached['signature'] ?? null) === $signature) {
            return $cached['map'];
        }

        $map = $this->scanModels();

        Cache::forever(self::CACHE_KEY, [
            'signature' => $signature,
            'map' => $map,
        ]);

        return $map;
    }

    protected function scanModels(): array
    {
        $entries = $this->modelFiles()
            ->map(fn (SplFileInfo $file) => $this->classFromPath($file))
            ->filter(fn (?string $class) => $class
                && $class !== BaseModel::class
                && class_exists($class)
                && is_subclass_of($class, Model::class)
                && ! (new ReflectionClass($class))->isAbstract());

        $duplicates = $entries->countBy(fn (string $class) => $this->morphAliasFor($class))
            ->filter(fn (int $count) => $count > 1);

        if ($duplicates->isNotEmpty()) {
            throw new \RuntimeException(
                'Morph alias duplicado(s): ' . $duplicates->keys()->implode(', ')
            );
        }

        return $entries
            ->mapWithKeys(fn (string $class) => [$this->morphAliasFor($class) => $class])
            ->all();
    }

    protected function modelFiles(): Collection
    {
        return collect((new Finder())->in(app_path())->files()->name('*.php'))
            ->filter(fn (SplFileInfo $file) => $this->isModelPath($file))
            ->sortBy(fn (SplFileInfo $file) => $file->getRelativePathname());
    }

    protected function modelFilesSignature(): string
    {
        return $this->modelFiles()
            ->map(fn (SplFileInfo $file) => $file->getRelativePathname() . ':' . $file->getMTime())
            ->implode('|');
    }

    protected function morphAliasFor(string $class): string
    {
        return is_subclass_of($class, BaseModel::class)
            ? $class::morphAlias()
            : Str::kebab(class_basename($class));
    }

    protected function isModelPath(SplFileInfo $file): bool
    {
        return str_contains($file->getPathname(), DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR);
    }

    protected function classFromPath(SplFileInfo $file): ?string
    {
        $relative = Str::of($file->getRelativePathname())
            ->replace(['/', '.php'], ['\\', '']);

        $class = 'App\\' . $relative;

        return class_exists($class) ? $class : null;
    }
}
