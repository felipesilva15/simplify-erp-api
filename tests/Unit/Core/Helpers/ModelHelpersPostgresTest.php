<?php

namespace Tests\Unit\Core\Helpers;

use App\Core\Helpers\ModelHelpers;
use App\Modules\Partner\Models\Partner;
use App\Modules\Security\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\DB;

class ModelHelpersPostgresTest extends TestCase
{
    use RefreshDatabase;

    private const PGSQL_MOCK_CONNECTION = 'pgsql-mock';

    protected function setUp(): void
    {
        parent::setUp();

        config(['database.connections.'.self::PGSQL_MOCK_CONNECTION => [
            'driver' => 'pgsql',
            'host' => '127.0.0.1',
            'database' => 'laravel',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
        ]]);
    }

    protected function tearDown(): void
    {
        DB::purge(self::PGSQL_MOCK_CONNECTION);
        parent::tearDown();
    }

    private function pgsqlBuilderFor(string $modelClass): \Illuminate\Database\Eloquent\Builder
    {
        return $modelClass::on(self::PGSQL_MOCK_CONNECTION);
    }

    public function test_detects_pgsql_driver_from_connection(): void
    {
        $this->assertTrue(ModelHelpers::isPgsql($this->pgsqlBuilderFor(User::class)));
        $this->assertFalse(ModelHelpers::isPgsql(User::query()));
    }

    public function test_string_like_filter_is_case_and_unaccent_insensitive_on_pgsql(): void
    {
        $builder = ModelHelpers::setFiltersOnQuery($this->pgsqlBuilderFor(User::class), [
            'name' => ['like' => 'Felipe'],
        ]);

        $wheres = $builder->getQuery()->wheres;
        $this->assertCount(1, $wheres);
        $this->assertSame('raw', $wheres[0]['type']);
        $this->assertSame('and', $wheres[0]['boolean']);
        $this->assertStringContainsString('unaccent(LOWER("name"))', $wheres[0]['sql']);
        $this->assertStringContainsString("LIKE '%' || unaccent(LOWER(?)) || '%'", $wheres[0]['sql']);
        $this->assertSame(['Felipe'], $builder->getQuery()->bindings['where']);
    }

    public function test_string_eq_filter_is_normalized_on_pgsql(): void
    {
        $builder = ModelHelpers::setFiltersOnQuery($this->pgsqlBuilderFor(User::class), [
            'name' => ['eq' => 'Felipe'],
        ]);

        $wheres = $builder->getQuery()->wheres;
        $this->assertStringContainsString('unaccent(LOWER("name")) = unaccent(LOWER(?))', $wheres[0]['sql']);
        $this->assertSame(['Felipe'], $builder->getQuery()->bindings['where']);
    }

    public function test_string_ne_filter_is_normalized_on_pgsql(): void
    {
        $builder = ModelHelpers::setFiltersOnQuery($this->pgsqlBuilderFor(User::class), [
            'name' => ['ne' => 'Felipe'],
        ]);

        $this->assertStringContainsString(
            'unaccent(LOWER("name")) <> unaccent(LOWER(?))',
            $builder->getQuery()->wheres[0]['sql']
        );
    }

    public function test_masked_string_like_filter_strips_mask_on_pgsql(): void
    {
        $builder = ModelHelpers::setFiltersOnQuery($this->pgsqlBuilderFor(Partner::class), [
            'document_number' => ['like' => '123.456'],
        ], [], ['masked_columns' => ['document_number']]);

        $wheres = $builder->getQuery()->wheres;
        $this->assertStringContainsString(
            'unaccent(LOWER(REGEXP_REPLACE("document_number", \'[^[:alnum:]]\', \'\', \'g\')))',
            $wheres[0]['sql']
        );
        $this->assertStringContainsString(
            "LIKE '%' || unaccent(LOWER(REGEXP_REPLACE(?, '[^[:alnum:]]', '', 'g'))) || '%'",
            $wheres[0]['sql']
        );
        $this->assertSame(['123.456'], $builder->getQuery()->bindings['where']);
    }

    public function test_masked_string_eq_filter_strips_mask_on_pgsql(): void
    {
        $builder = ModelHelpers::setFiltersOnQuery($this->pgsqlBuilderFor(Partner::class), [
            'document_number' => ['eq' => '123.456.789-00'],
        ], [], ['masked_columns' => ['document_number']]);

        $this->assertStringContainsString(
            'unaccent(LOWER(REGEXP_REPLACE("document_number", \'[^[:alnum:]]\', \'\', \'g\'))) = unaccent(LOWER(REGEXP_REPLACE(?, \'[^[:alnum:]]\', \'\', \'g\')))',
            $builder->getQuery()->wheres[0]['sql']
        );
        $this->assertSame(['123.456.789-00'], $builder->getQuery()->bindings['where']);
    }

    public function test_non_normalized_column_keeps_default_where_behavior_on_pgsql(): void
    {
        $builder = ModelHelpers::setFiltersOnQuery($this->pgsqlBuilderFor(User::class), [
            'name' => ['like' => 'Felipe'],
        ], [], ['not_normalized_columns' => ['name']]);

        $wheres = $builder->getQuery()->wheres;
        $this->assertSame('Basic', $wheres[0]['type']);
        $this->assertSame('name', $wheres[0]['column']);
        $this->assertSame('like', $wheres[0]['operator']);
        $this->assertSame('%Felipe%', $wheres[0]['value']);
    }

    public function test_add_string_search_to_where_supports_or_boolean(): void
    {
        $builder = $this->pgsqlBuilderFor(User::class);

        ModelHelpers::addStringSearchToWhere($builder, 'name', 'like', 'Felipe', false, 'or');

        $wheres = $builder->getQuery()->wheres;
        $this->assertSame('or', $wheres[0]['boolean']);
        $this->assertStringContainsString("LIKE '%' || unaccent(LOWER(?)) || '%'", $wheres[0]['sql']);
        $this->assertSame(['Felipe'], $builder->getQuery()->bindings['where']);
    }

    public function test_add_string_search_to_where_supports_masked_value(): void
    {
        $builder = $this->pgsqlBuilderFor(Partner::class);

        ModelHelpers::addStringSearchToWhere($builder, 'document_number', 'like', '(11) 99999-9999', true);

        $this->assertStringContainsString(
            "LIKE '%' || unaccent(LOWER(REGEXP_REPLACE(?, '[^[:alnum:]]', '', 'g'))) || '%'",
            $builder->getQuery()->wheres[0]['sql']
        );
        $this->assertSame(['(11) 99999-9999'], $builder->getQuery()->bindings['where']);
    }
}