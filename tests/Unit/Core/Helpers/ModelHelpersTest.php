<?php

namespace Tests\Unit\Core\Helpers;

use App\Core\DTO\PaginatorInfo;
use App\Core\DTO\PaginatorLinks;
use App\Core\DTO\PaginatorMeta;
use App\Core\Enums\SqlOrderDirectionEnum;
use App\Core\Helpers\ModelHelpers;
use App\Core\Helpers\PaginatorHelpers;
use App\Modules\Security\Models\User;
use Carbon\Carbon;
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
            'id' => [
                'eq' => 1
            ],
            'name' => [
                'like' => 'Felipe'
            ]
        ];
        $builder = User::query();

        $builder = ModelHelpers::setFiltersOnQuery($builder, $filters);
        
        $this->assertCount(2, $builder->getQuery()->wheres);
        $this->assertEquals('id', $builder->getQuery()->wheres[0]['column']);
        $this->assertEquals('name', $builder->getQuery()->wheres[1]['column']);
        $this->assertStringStartsWith('%', $builder->getQuery()->wheres[1]['value']);
        $this->assertStringEndsWith('%', $builder->getQuery()->wheres[1]['value']);
    }

    public function test_can_set_filters_on_query_for_date_column(): void {
        $filters = [
            'created_at' => [
                'lte' => Carbon::now()->toISOString()
            ]
        ];
        $builder = User::query();

        $builder = ModelHelpers::setFiltersOnQuery($builder, $filters);
        
        $this->assertCount(1, $builder->getQuery()->wheres);
        $this->assertEquals('created_at', $builder->getQuery()->wheres[0]['column']);
    }

    public function test_can_set_filters_on_query_only_for_some_columns(): void {
        $filters = [
            'id' => [
                'eq' => 1
            ],
            'name' => [
                'like' => 'Felipe'
            ]
        ];
        $builder = User::query();

        $builder = ModelHelpers::setFiltersOnQuery($builder, $filters, ['name']);
        
        $this->assertCount(1, $builder->getQuery()->wheres);
        $this->assertEquals('name', $builder->getQuery()->wheres[0]['column']);
    }

    public function test_cannot_set_filters_on_query_for_non_existent_column(): void {
        $filters = [
            'foo' => [
                'eq' => 'bar'
            ]
        ];
        $builder = User::query();

        $builder = ModelHelpers::setFiltersOnQuery($builder, $filters);
        
        $this->assertEmpty($builder->getQuery()->wheres);
    }

    public function test_cannot_set_filters_on_query_for_non_existent_operator(): void {
        $filters = [
            'id' => [
                'foo' => 0
            ]
        ];
        $builder = User::query();

        $builder = ModelHelpers::setFiltersOnQuery($builder, $filters);
        
        $this->assertEmpty($builder->getQuery()->wheres);
    }

    public function test_can_set_sorts_on_query(): void {
        $sorts = '-id,name';
        $builder = User::query();

        $builder = ModelHelpers::setSortsOnQuery($builder, $sorts);
        
        $this->assertCount(2, $builder->getQuery()->orders);
        $this->assertEquals('id', $builder->getQuery()->orders[0]['column']);
        $this->assertEquals('name', $builder->getQuery()->orders[1]['column']);
    }

    public function test_cannot_set_sorts_on_query_for_non_existent_column(): void {
        $sorts = 'foo';
        $builder = User::query();

        $builder = ModelHelpers::setSortsOnQuery($builder, $sorts);
        
        $this->assertEmpty($builder->getQuery()->orders);
    }

    public function test_cannot_set_sorts_on_query_for_empty_sorts(): void {
        $builder = User::query();

        $builder = ModelHelpers::setSortsOnQuery($builder, '');
        
        $this->assertEmpty($builder->getQuery()->orders);
    }

    public function test_default_sorts_is_primary_key_descending(): void {
        $this->assertSame('-id', ModelHelpers::DEFAULT_SORTS);
    }

    public function test_can_set_search_on_query_grouping_columns_with_or(): void {
        $builder = User::query();

        $builder = ModelHelpers::setSearchOnQuery($builder, 'FELIPE', ['id', 'name', 'email']);

        $wheres = $builder->getQuery()->wheres;

        $this->assertCount(1, $wheres);
        $this->assertSame('Nested', $wheres[0]['type']);
        $this->assertCount(3, $wheres[0]['query']->wheres);

        $this->assertSame('id', $wheres[0]['query']->wheres[0]['column']);
        $this->assertSame('=', $wheres[0]['query']->wheres[0]['operator']);
        $this->assertSame(0, $wheres[0]['query']->wheres[0]['value']);

        $this->assertSame('name', $wheres[0]['query']->wheres[1]['column']);
        $this->assertSame('like', $wheres[0]['query']->wheres[1]['operator']);
        $this->assertSame('%FELIPE%', $wheres[0]['query']->wheres[1]['value']);

        $this->assertSame('email', $wheres[0]['query']->wheres[2]['column']);
        $this->assertSame('like', $wheres[0]['query']->wheres[2]['operator']);
    }

    public function test_set_search_on_query_trims_the_term(): void {
        $builder = User::query();

        $builder = ModelHelpers::setSearchOnQuery($builder, '   Felipe   ', ['name']);

        $this->assertSame('%Felipe%', $builder->getQuery()->wheres[0]['query']->wheres[0]['value']);
    }

    public function test_set_search_on_query_casts_integer_columns(): void {
        $builder = User::query();

        $builder = ModelHelpers::setSearchOnQuery($builder, '7', ['id']);

        $nested = $builder->getQuery()->wheres[0]['query']->wheres[0];

        $this->assertSame('id', $nested['column']);
        $this->assertSame('=', $nested['operator']);
        $this->assertSame(7, $nested['value']);
    }

    public function test_cannot_set_search_on_query_for_empty_term(): void {
        $builder = User::query();

        $builder = ModelHelpers::setSearchOnQuery($builder, '   ', ['id', 'name']);

        $this->assertEmpty($builder->getQuery()->wheres);
    }

    public function test_cannot_set_search_on_query_without_columns(): void {
        $builder = User::query();

        $builder = ModelHelpers::setSearchOnQuery($builder, 'Felipe', []);

        $this->assertEmpty($builder->getQuery()->wheres);
    }

    public function test_set_search_on_query_ignores_non_existent_columns(): void {
        $builder = User::query();

        $builder = ModelHelpers::setSearchOnQuery($builder, 'Felipe', ['coluna_inexistente', 'name']);

        $nested = $builder->getQuery()->wheres[0]['query']->wheres;

        $this->assertCount(1, $nested);
        $this->assertSame('name', $nested[0]['column']);
    }

    public function test_set_search_on_query_skips_all_columns_when_none_exists(): void {
        $builder = User::query();

        $builder = ModelHelpers::setSearchOnQuery($builder, 'Felipe', ['coluna_inexistente']);

        $this->assertEmpty($builder->getQuery()->wheres);
    }

    public function test_set_search_on_query_accepts_the_legacy_column_map(): void {
        $builder = User::query();

        $builder = ModelHelpers::setSearchOnQuery($builder, 'Felipe', ['name' => 'string', 'id' => 'int']);

        $nested = $builder->getQuery()->wheres[0]['query']->wheres;

        $this->assertCount(2, $nested);
        $this->assertSame('name', $nested[0]['column']);
        $this->assertSame('id', $nested[1]['column']);
    }

    public function test_set_search_on_query_ignores_date_columns_when_term_is_not_a_date(): void {
        $builder = User::query();

        $builder = ModelHelpers::setSearchOnQuery($builder, 'Felipe', ['created_at', 'name']);

        $nested = $builder->getQuery()->wheres[0]['query']->wheres;

        $this->assertCount(1, $nested);
        $this->assertSame('name', $nested[0]['column']);
    }

    public function test_set_search_on_query_compares_date_columns_when_term_is_a_date(): void {
        $builder = User::query();

        $builder = ModelHelpers::setSearchOnQuery($builder, '2026-01-01', ['created_at']);

        $nested = $builder->getQuery()->wheres[0]['query']->wheres[0];

        $this->assertSame('created_at', $nested['column']);
        $this->assertSame('=', $nested['operator']);
        $this->assertInstanceOf(Carbon::class, $nested['value']);
    }
}
