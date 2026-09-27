<?php

namespace App\Core\Services\Children;

use App\Core\Enums\ActivityActionEnum;
use App\Core\Exceptions\BusinessRuleException;
use App\Core\Services\ActivityLogService;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use LogicException;

abstract class BaseChildSync
{
    protected HasOneOrMany $relation;

    public function __construct(
        protected Model $header,
        protected ChildRelation $definition,
        protected ActivityLogService $activity,
    ) {
        $relation = $header->{$definition->relation}();

        if (! $relation instanceof HasOneOrMany) {
            throw new LogicException(sprintf(
                'A relação [%s] de [%s] precisa ser 1:N para ser sincronizada.',
                $definition->relation,
                $header::class
            ));
        }

        $this->relation = $relation;
    }

    final public function sync(array $items): ChildSyncResult
    {
        $payloads = $this->normalizeItems($items);
        $existing = $this->currentItems();

        $this->validatePayload($payloads, $existing);

        $created = 0;
        $updated = 0;
        $keptKeys = [];

        foreach ($payloads as $index => $payload) {
            $current = $this->matchCurrentItem($payload, $existing, $index);

            if ($current === null) {
                $this->createItem($payload, $index);
                $created++;

                continue;
            }

            $keptKeys[(string) $current->getKey()] = true;

            if ($this->updateItem($current, $payload, $index)) {
                $updated++;
            }
        }

        $deleted = 0;

        foreach ($existing as $key => $item) {
            if (isset($keptKeys[(string) $key])) {
                continue;
            }

            if ($this->deleteItem($item)) {
                $deleted++;
            }
        }

        return new ChildSyncResult(created: $created, updated: $updated, deleted: $deleted);
    }

    protected function validateItem(array $payload, int $index, ?Model $current = null): void
    {
        // Sem regra por item: cabe ao módulo sobrescrever.
    }

    protected function validatePayload(array $payloads, array $existing): void
    {
        // Sem regra de conjunto: cabe ao módulo sobrescrever.
    }

    protected function activityDescription(Model $item, ActivityActionEnum $action): ?string
    {
        return null;
    }

    protected function createItem(array $payload, int $index): Model
    {
        $this->validateItem($payload, $index);

        $item = $this->relation->getRelated()->newInstance();
        $item->fill($this->fillablePayload($payload));

        $this->relation->save($item);

        $this->log($item, ActivityActionEnum::Created);

        return $item;
    }

    protected function updateItem(Model $current, array $payload, int $index): bool
    {
        $this->validateItem($payload, $index, $current);

        $wasTrashed = $this->isTrashed($current);

        if ($wasTrashed) {
            $current->restore();
        }

        $current->fill($this->fillablePayload($payload));

        if (! $current->isDirty() && ! $wasTrashed) {
            return false;
        }

        $this->relation->save($current);

        $this->log($current, ActivityActionEnum::Updated);

        return true;
    }

    protected function deleteItem(Model $current): bool
    {
        if ($this->isTrashed($current)) {
            return false;
        }

        if (! $current->delete()) {
            return false;
        }

        $this->log($current, ActivityActionEnum::Deleted);

        return true;
    }

    protected function log(Model $item, ActivityActionEnum $action): void
    {
        $this->activity->log($item, $action, $this->activityDescription($item, $action));
    }

    protected function currentItems(): array
    {
        $query = $this->relation;

        if ($this->usesSoftDeletes()) {
            $query = $query->withTrashed();
        }

        return $query->get()
            ->keyBy(fn (Model $item): string => (string) $item->getKey())
            ->all();
    }

    protected function findCurrentItem(array $payload, array $existing): ?Model
    {
        $id = $payload[$this->identityKey()] ?? null;

        if ($id === null || $id === '') {
            return null;
        }

        return $existing[(string) $id] ?? null;
    }

    protected function matchCurrentItem(array $payload, array $existing, int $index): ?Model
    {
        $id = $payload[$this->identityKey()] ?? null;

        if ($id === null || $id === '') {
            return null;
        }

        $current = $existing[(string) $id] ?? null;

        if ($current === null) {
            throw new BusinessRuleException(
                message: sprintf(
                    'O item [%s] informado em [%s] não pertence a este registro.',
                    $id,
                    $this->definition->relation
                ),
                errors: [
                    $this->itemPath($index, $this->identityKey()) => 'Item informado não pertence a este registro.',
                ]
            );
        }

        return $current;
    }

    protected function normalizeItems(array $items): array
    {
        $payloads = [];

        foreach (array_values($items) as $index => $item) {
            if (is_array($item)) {
                $payloads[$index] = $item;

                continue;
            }

            if ($item instanceof Arrayable) {
                $payloads[$index] = $item->toArray();

                continue;
            }

            if (is_object($item)) {
                $payloads[$index] = get_object_vars($item);

                continue;
            }

            throw new BusinessRuleException(
                message: sprintf(
                    'O item [%s] enviado em [%s] está em formato inválido.',
                    $this->itemPath((int) $index, '*'),
                    $this->definition->relation
                ),
                errors: [
                    $this->itemPath((int) $index, '*') => 'Item esperado no formato de objeto.',
                ]
            );
        }

        return $payloads;
    }

    protected function fillablePayload(array $payload): array
    {
        $child = $this->relation->getRelated();

        return Arr::except($payload, array_values(array_filter([
            $this->identityKey(),
            $this->foreignKey(),
            $child->getCreatedAtColumn(),
            $child->getUpdatedAtColumn(),
        ])));
    }

    protected function itemPath(int $index, string $field): string
    {
        return sprintf('%s.%s.%s', $this->definition->relation, $index, $field);
    }

    protected function identityKey(): string
    {
        return method_exists($this->relation, 'getRelatedKeyName')
            ? $this->relation->getRelatedKeyName()
            : $this->relation->getRelated()->getKeyName();
    }

    protected function foreignKey(): string
    {
        return method_exists($this->relation, 'getForeignKeyName')
            ? $this->relation->getForeignKeyName()
            : $this->relation->getForeignKey();
    }

    protected function isTrashed(Model $item): bool
    {
        return method_exists($item, 'trashed') && $item->trashed();
    }

    protected function usesSoftDeletes(): bool
    {
        return in_array(
            SoftDeletes::class,
            class_uses_recursive($this->relation->getRelated()) ?: [],
            true
        );
    }
}
