<?php

namespace App\Modules\Catalog\Services;

use App\Core\DTO\ServiceResult;
use App\Core\Enums\ProductApplicabilityEnum;
use App\Core\Enums\ProductTypeEnum;
use App\Core\Exceptions\BusinessRuleException;
use App\Core\Exceptions\NotFoundHttpException;
use App\Core\Services\ActivityLogService;
use App\Core\Services\BaseCrudService;
use App\Modules\Catalog\Enums\ItemTypeCodeEnum;
use App\Modules\Catalog\Models\Product;
use App\Modules\Catalog\Models\ProductCategory;
use App\Modules\Catalog\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class ProductService extends BaseCrudService
{
    public function __construct(ProductRepositoryInterface $repository, ActivityLogService $activity)
    {
        $this->repository = $repository;
        $this->activity = $activity;
    }

    public function storeFor(ProductTypeEnum $type, mixed $data): ServiceResult
    {
        $data = $this->prepareForType($data, $type);

        return $this->store($data);
    }

    public function updateFor(ProductTypeEnum $type, Product $entity, mixed $data): ServiceResult
    {
        $this->assertType($entity, $type);

        $data = $this->prepareForType($data, $type);

        return $this->update($entity, $data);
    }

    public function showFor(ProductTypeEnum $type, Product $entity): ServiceResult
    {
        $this->assertType($entity, $type);

        return $this->show($entity);
    }

    public function editFor(ProductTypeEnum $type, Product $entity): ServiceResult
    {
        $this->assertType($entity, $type);

        return $this->edit($entity);
    }

    public function deleteFor(ProductTypeEnum $type, Product $entity): ServiceResult
    {
        $this->assertType($entity, $type);

        return $this->delete($entity);
    }

    public function listFor(ProductTypeEnum $type, array $params = []): ServiceResult
    {
        return $this->list($this->paramsForType($params, $type));
    }

    public function lookupFor(ProductTypeEnum $type, array $params = []): ServiceResult
    {
        return $this->lookup($this->paramsForType($params, $type));
    }

    public function exportQueryFor(ProductTypeEnum $type, array $params = []): Builder
    {
        return $this->exportQuery($this->paramsForType($params, $type));
    }

    /**
     * Força o tipo da fachada e valida os campos que dependem dele. O cliente
     * nunca informa `type`: ele é sempre sobrescrito aqui.
     */
    private function prepareForType(mixed $data, ProductTypeEnum $type): mixed
    {
        $this->assertItemTypeCode($data, $type);
        $this->assertCategoryApplicability($data, $type);

        if (is_array($data)) {
            $data['type'] = $type->value;

            return $data;
        }

        if (is_object($data)) {
            if (property_exists($data, 'type')) {
                $data->type = $type->value;
            }

            if (property_exists($data, 'fieldsToUse') && is_array($data->fieldsToUse)) {
                $data->fieldsToUse[] = 'type';
                $data->fieldsToUse = array_values(array_unique($data->fieldsToUse));
            }
        }

        return $data;
    }

    private function paramsForType(array $params, ProductTypeEnum $type): array
    {
        $params['forcedFilters'] = ['type' => $type->value];

        return $params;
    }

    private function assertType(Product $entity, ProductTypeEnum $type): void
    {
        if ((string) $entity->type !== $type->value) {
            throw new NotFoundHttpException;
        }
    }

    private function assertItemTypeCode(mixed $data, ProductTypeEnum $type): void
    {
        $code = $this->dataValue($data, 'item_type_code');

        if ($code === null || $code === '') {
            return;
        }

        $code = $code instanceof ItemTypeCodeEnum ? $code->value : (string) $code;

        if (! in_array($code, ItemTypeCodeEnum::valuesFor($type), true)) {
            throw new BusinessRuleException(
                message: 'O código de item informado não é válido para esta fachada.',
                errors: ['item_type_code' => ['O código de item informado não é válido para esta fachada.']]
            );
        }
    }

    private function assertCategoryApplicability(mixed $data, ProductTypeEnum $type): void
    {
        $categoryId = $this->dataValue($data, 'product_category_id');

        if (empty($categoryId)) {
            return;
        }

        $category = ProductCategory::find((int) $categoryId);

        if ($category === null) {
            return;
        }

        $allowed = $type === ProductTypeEnum::Service
            ? [ProductApplicabilityEnum::Service, ProductApplicabilityEnum::Both]
            : [ProductApplicabilityEnum::Product, ProductApplicabilityEnum::Both];

        if (! in_array($category->applicability, $allowed, true)) {
            throw new BusinessRuleException(
                message: 'A categoria informada não é aplicável a esta fachada.',
                errors: ['product_category_id' => ['A categoria informada não é aplicável a esta fachada.']]
            );
        }
    }
}
