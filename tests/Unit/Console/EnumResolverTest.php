<?php

namespace Tests\Unit\Console;

use App\Console\Support\EnumResolver;
use Tests\TestCase;

class EnumResolverTest extends TestCase
{
    public function test_resolves_existing_enums_by_column_and_context(): void
    {
        $resolver = new EnumResolver('Finance', 'PaymentIntent');

        $resolved = $resolver->resolveForColumns([
            'payment_type',
            'applicable_type',
            'tax_type',
            'payment_deadline',
            'name',
        ]);

        $this->assertSame('PaymentTypeEnum', $resolved['payment_type']['schema']);
        $this->assertSame('FinanceApplicabilityEnum', $resolved['applicable_type']['schema']);
        $this->assertSame('TaxTypeEnum', $resolved['tax_type']['schema']);
        $this->assertNull($resolved['payment_deadline']);
        $this->assertNull($resolved['name']);
    }

    public function test_resolves_entity_and_module_specific_enums(): void
    {
        $catalog = new EnumResolver('Catalog', 'Product');

        $resolved = $catalog->resolveForColumns(['type', 'item_type_code', 'sku']);

        $this->assertSame('ProductTypeEnum', $resolved['type']['schema']);
        $this->assertSame('ItemTypeCodeEnum', $resolved['item_type_code']['schema']);
        $this->assertNull($resolved['sku']);

        $measurementUnit = new EnumResolver('Catalog', 'MeasurementUnit');

        $resolved = $measurementUnit->resolveForColumns(['category', 'conversion_factor']);

        $this->assertSame('MeasurementUnitCategoryEnum', $resolved['category']['schema']);
        $this->assertNull($resolved['conversion_factor']);
    }
}
