<?php

namespace Tests\Unit\Core\Helpers;

use App\Core\DTO\PaginatorInfo;
use App\Core\DTO\PaginatorLinks;
use App\Core\DTO\PaginatorMeta;
use App\Core\Enums\SqlOrderDirectionEnum;
use App\Core\Helpers\ModelHelpers;
use App\Core\Helpers\PaginatorHelpers;
use App\Modules\Security\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;

class ModelHelpersTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_columns_of_a_table(): void
    {
        $columns = ModelHelpers::getColumnsFromTable('users');

        $this->assertIsArray($columns);
        $this->assertNotEmpty($columns);
        $this->assertArrayHasKey('name', $columns[0]);
        $this->assertArrayHasKey('type', $columns[0]);
    }
    
    public function test_cannot_get_columns_of_a_non_existent_table(): void
    {
        $columns = ModelHelpers::getColumnsFromTable('foobar');

        $this->assertIsArray($columns);
        $this->assertEmpty($columns);
    }

    public function test_can_set_filters_on_query(): void {
        $filters = [
            'id' => '1',
            'name' => 'Felipe'
        ];
        $builder = User::query();

        $builder = ModelHelpers::setFiltersOnQuery($builder, $filters);
        
        $this->assertCount(2, $builder->getQuery()->wheres);
        $this->assertEquals('id', $builder->getQuery()->wheres[0]['column']);
        $this->assertEquals('name', $builder->getQuery()->wheres[1]['column']);
        $this->assertStringStartsWith('%', $builder->getQuery()->wheres[1]['value']);
        $this->assertStringEndsWith('%', $builder->getQuery()->wheres[1]['value']);
    }

    public function test_can_set_filters_on_query_only_for_some_columns(): void {
        $filters = [
            'id' => '1',
            'name' => 'Felipe'
        ];
        $builder = User::query();

        $builder = ModelHelpers::setFiltersOnQuery($builder, $filters, ['name']);
        
        $this->assertCount(1, $builder->getQuery()->wheres);
        $this->assertEquals('name', $builder->getQuery()->wheres[0]['column']);
    }

    public function test_cannot_set_filters_on_query_for_non_existent_column(): void {
        $filters = [
            'foo' => 'bar'
        ];
        $builder = User::query();

        $builder = ModelHelpers::setFiltersOnQuery($builder, $filters);
        
        $this->assertEmpty($builder->getQuery()->wheres);
    }

    public function test_can_set_sorts_on_query(): void {
        $sortBy = [
            'id',
            'name'
        ];
        $sortDir = [
            SqlOrderDirectionEnum::Descending->value,
            SqlOrderDirectionEnum::Ascending->value
        ];
        $builder = User::query();

        $builder = ModelHelpers::setSortsOnQuery($builder, $sortBy, $sortDir);
        
        $this->assertCount(2, $builder->getQuery()->orders);
        $this->assertEquals('id', $builder->getQuery()->orders[0]['column']);
        $this->assertEquals('name', $builder->getQuery()->orders[1]['column']);
    }

    public function test_cannot_set_sorts_on_query_for_non_existent_column(): void {
        $sortBy = [
            'foo'
        ];
        $sortDir = [
            SqlOrderDirectionEnum::Descending->value
        ];
        $builder = User::query();

        $builder = ModelHelpers::setSortsOnQuery($builder, $sortBy, $sortDir);
        
        $this->assertEmpty($builder->getQuery()->orders);
    }

    public function test_cannot_set_sorts_on_query_for_non_existent_direction(): void {
        $sortBy = [
            'id'
        ];
        $sortDir = [
            'foo'
        ];
        $builder = User::query();

        $builder = ModelHelpers::setSortsOnQuery($builder, $sortBy, $sortDir);
        
        $this->assertEmpty($builder->getQuery()->orders);
    }

    public function test_cannot_set_sorts_on_query_without_direction(): void {
        $sortBy = [
            'id'
        ];
        $sortDir = [];
        $builder = User::query();

        $builder = ModelHelpers::setSortsOnQuery($builder, $sortBy, $sortDir);
        
        $this->assertEmpty($builder->getQuery()->orders);
    }
}
