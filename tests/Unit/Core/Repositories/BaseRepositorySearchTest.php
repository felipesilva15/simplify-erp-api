<?php

namespace Tests\Unit\Core\Repositories;

use App\Core\Helpers\ModelHelpers;
use App\Core\Repositories\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;

/**
 * Exercita a inteligência de listagem (`q`, `filters`, `sorts`) sobre o schema real,
 * em SQLite, complementando os testes com mock de BaseRepositoryTest.
 */
class SearchableCountry extends Model
{
    protected $table = 'countries';

    protected $guarded = [];
}

class SearchableCountryRepository extends BaseRepository
{
    protected function getModelClass(): string
    {
        return SearchableCountry::class;
    }

    protected function getListColumnsToFilter(): array
    {
        return ['id', 'iso_code', 'name'];
    }

    protected function getLookupColumnsToFilter(): array
    {
        return ['iso_code', 'name'];
    }
}

class BaseRepositorySearchTest extends TestCase
{
    private SearchableCountryRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new SearchableCountryRepository;
    }

    private function makeCountries(): void
    {
        SearchableCountry::create(['iso_code' => 'BR', 'name' => 'Brasil']);
        SearchableCountry::create(['iso_code' => 'US', 'name' => 'Estados Unidos']);
        SearchableCountry::create(['iso_code' => 'DE', 'name' => 'Alemanha']);
    }

    public function test_default_sort_is_primary_key_descending(): void
    {
        $this->assertSame(ModelHelpers::DEFAULT_SORTS, '-id');

        $this->makeCountries();

        $this->assertSame([3, 2, 1], $this->repository->list()->pluck('id')->all());
        $this->assertSame([3, 2, 1], $this->repository->getExportQuery()->pluck('id')->all());
        $this->assertSame([3, 2, 1], $this->repository->lookup()->pluck('id')->all());
    }

    public function test_informed_sorts_override_the_default_on_every_listing(): void
    {
        $this->makeCountries();

        $this->assertSame([1, 2, 3], $this->repository->list(['sorts' => 'id'])->pluck('id')->all());
        $this->assertSame([1, 2, 3], $this->repository->getExportQuery(['sorts' => 'id'])->pluck('id')->all());
        $this->assertSame([1, 2, 3], $this->repository->lookup(['sorts' => 'id'])->pluck('id')->all());
    }

    public function test_list_searches_across_configured_columns(): void
    {
        $this->makeCountries();

        $this->assertSame(['BR'], $this->repository->list(['q' => 'ras'])->pluck('iso_code')->all());
        $this->assertSame(['BR'], $this->repository->list(['q' => 'BR'])->pluck('iso_code')->all());
        $this->assertSame([2], $this->repository->list(['q' => '2'])->pluck('id')->all());
    }

    public function test_get_export_query_searches_like_list(): void
    {
        $this->makeCountries();

        $exportQuery = $this->repository->getExportQuery(['q' => 'Unidos']);
        $listResult = $this->repository->list(['q' => 'Unidos']);

        $this->assertSame(['Estados Unidos'], $exportQuery->pluck('name')->all());
        $this->assertSame(['Estados Unidos'], $listResult->pluck('name')->all());
    }

    public function test_lookup_searches_only_its_own_configured_columns(): void
    {
        $this->makeCountries();

        $this->assertSame(['DE'], $this->repository->lookup(['q' => 'Alemanha'])->pluck('iso_code')->all());
        $this->assertSame([], $this->repository->lookup(['q' => 'zzzz'])->pluck('iso_code')->all());
    }

    public function test_search_ignores_columns_absent_from_the_schema(): void
    {
        $repository = new class extends BaseRepository
        {
            protected function getModelClass(): string
            {
                return SearchableCountry::class;
            }

            protected function getListColumnsToFilter(): array
            {
                return ['coluna_inexistente'];
            }
        };

        $this->makeCountries();

        $this->assertCount(3, $repository->list(['q' => 'Brasil']));
    }

    public function test_blank_search_is_ignored(): void
    {
        $this->makeCountries();

        $this->assertCount(3, $this->repository->list(['q' => '   ']));
        $this->assertCount(3, $this->repository->lookup(['q' => '']));
        $this->assertSame(3, $this->repository->getExportQuery(['q' => '  '])->count());
    }

    public function test_search_combines_with_filters(): void
    {
        $this->makeCountries();

        $params = [
            'q' => 'a',
            'filters' => ['id' => ['gte' => 2]],
        ];

        $this->assertSame([3, 2], $this->repository->list($params)->pluck('id')->all());
        $this->assertSame([3, 2], $this->repository->getExportQuery($params)->pluck('id')->all());
    }

    public function test_lookup_search_combines_with_keys(): void
    {
        $this->makeCountries();

        $result = $this->repository->lookup(['q' => 'a', 'keys' => [1, 2]]);

        $this->assertSame([2, 1], $result->pluck('id')->all());
    }
}
