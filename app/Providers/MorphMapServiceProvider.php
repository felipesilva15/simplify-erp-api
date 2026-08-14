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

class MorphMapServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $map = Cache::rememberForever('morph-map', fn () => $this->scanModels());

        Relation::enforceMorphMap($map);
    }

    protected function scanModels(): array
    {
        $entries = collect((new Finder())->in(app_path())->files()->name('*.php'))
            ->filter(fn (SplFileInfo $file) => $this->isModelPath($file))
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
