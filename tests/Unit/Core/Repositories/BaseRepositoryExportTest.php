<?php

namespace Tests\Unit\Core\Repositories;

use App\Core\Repositories\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;

class ExportableCountry extends Model
{
    protected $table = 'countries';

    protected $guarded = [];
}

class ExportableCountryRepository extends BaseRepository
{
    protected function getModelClass(): string
    {
        return ExportableCountry::class;
    }

    protected function getListColumnsToFilter(): array
    {
        return ['id', 'iso_code', 'name'];
    }
}

class BaseRepositoryExportTest extends TestCase
{
    private ExportableCountryRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new ExportableCountryRepository();
    }

    private function makeCountries(): array
    {
        return [
            ExportableCountry::create(['iso_code' => 'BR', 'name' => 'Brasil']),
            ExportableCountry::create(['iso_code' => 'US', 'name' => 'Estados Unidos']),
            ExportableCountry::create(['iso_code' => 'DE', 'name' => 'Alemanha']),
        ];
    }

    private function queryOrders(\Illuminate\Database\Eloquent\Builder $query): array
    {
        return $query->getQuery()->orders ?? [];
    }

    public function test_get_export_query_returns_a_builder_without_pagination(): void
    {
        $this->makeCountries();

        $query = $this->repository->getExportQuery();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $query);
        $this->assertNotInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $query);
        $this->assertCount(3, $query->get());
    }

    public function test_get_export_query_returns_all_records_and_orders_by_primary_key_desc_by_default(): void
    {
        $this->makeCountries();

        $query = $this->repository->getExportQuery();

        $this->assertSame(
            ['DE', 'US', 'BR'],
            $query->get()->pluck('iso_code')->all()
        );
        $this->assertSame([
            ['column' => 'id', 'direction' => 'desc'],
        ], $this->queryOrders($query));
    }

    public function test_get_export_query_keeps_primary_key_as_tiebreaker_when_sorts_are_informed(): void
    {
        $this->makeCountries();

        $query = $this->repository->getExportQuery(['sorts' => 'name']);

        $this->assertSame(
            ['DE', 'BR', 'US'],
            $query->get()->pluck('iso_code')->all()
        );
        $this->assertSame([
            ['column' => 'name', 'direction' => 'asc'],
            ['column' => 'id', 'direction' => 'asc'],
        ], $this->queryOrders($query));
    }

    public function test_get_export_query_applies_filters_like_list(): void
    {
        $this->makeCountries();

        $filters = ['id' => ['eq' => 2]];

        $exportQuery = $this->repository->getExportQuery(['filters' => $filters]);
        $listResult = $this->repository->list(['filters' => $filters]);

        $this->assertSame([2], $exportQuery->pluck('id')->all());
        $this->assertSame([2], $listResult->pluck('id')->all());
    }

    public function test_get_export_query_applies_like_filter_like_list(): void
    {
        $this->makeCountries();

        $filters = ['name' => ['like' => 'il']];

        $exportQuery = $this->repository->getExportQuery(['filters' => $filters]);
        $listResult = $this->repository->list(['filters' => $filters]);

        $this->assertSame(['Brasil'], $exportQuery->pluck('name')->all());
        $this->assertSame(['Brasil'], $listResult->pluck('name')->all());
    }

    public function test_get_export_query_applies_sorts(): void
    {
        $this->makeCountries();

        $query = $this->repository->getExportQuery(['sorts' => '-id']);

        $this->assertSame([3, 2, 1], $query->pluck('id')->all());
    }

    public function test_get_export_query_applies_search_like_list(): void
    {
        $this->makeCountries();

        $params = ['q' => 'il'];

        $this->assertSame(
            ['Brasil'],
            $this->repository->getExportQuery($params)->pluck('name')->all()
        );
        $this->assertSame(
            ['Brasil'],
            $this->repository->list($params)->pluck('name')->all()
        );
    }

    public function test_get_export_query_ignores_blank_search(): void
    {
        $this->makeCountries();

        $this->assertSame(3, $this->repository->getExportQuery(['q' => '   '])->count());
    }
}