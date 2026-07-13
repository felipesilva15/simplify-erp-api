<?php

namespace Tests\Unit\Core\Repositories;

use App\Core\Repositories\Eloquent\BaseRepository;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class BaseRepositoryTest extends TestCase
{
    private Model|MockInterface $modelMock;
    private BaseRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->modelMock = Mockery::mock(Model::class);

        $this->repository = new class($this->modelMock) extends BaseRepository {
            public function __construct(Model $model)
            {
                $this->model = $model;
            }

            protected function getModelClass(): string
            {
                return Model::class;
            }

            // Expõe getLookupColumnsToFilter() para permitir teste da implementação padrão
            public function getLookupColumnsToFilterPublic(): array
            {
                return $this->getLookupColumnsToFilter();
            }
        };
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Cria um LengthAwarePaginator simples para uso nos testes.
     */
    private function makePaginator(int $perPage = 15, int $total = 0): LengthAwarePaginator
    {
        return new LengthAwarePaginator([], $total, $perPage, 1);
    }

    /**
     * Cria um mock de Builder já configurado com paginate()->withQueryString().
     * Não configura where/whereIn — cada teste adiciona o que precisar antes de chamar o método.
     */
    private function makeBuilderMock(LengthAwarePaginator $paginator, int $perPage = 15, int $page = 1): Builder|MockInterface
    {
        $builderMock = Mockery::mock(Builder::class);

        $builderMock->shouldReceive('paginate')
            ->once()
            ->with($perPage, ['*'], 'page', $page)
            ->andReturnSelf();

        $builderMock->shouldReceive('withQueryString')
            ->once()
            ->andReturn($paginator);

        // Permite encadeamento nos testes que adicionam where/whereIn
        $builderMock->shouldReceive('where')->byDefault()->andReturnSelf();
        $builderMock->shouldReceive('whereIn')->byDefault()->andReturnSelf();

        return $builderMock;
    }

    /**
     * Cria um objeto anônimo simples com toArray(), simulando um DTO ou Form Request.
     */
    private function makeDataObject(array $data): object
    {
        return new class($data) {
            public function __construct(private array $data) {}

            public function toArray(): array
            {
                return $this->data;
            }
        };
    }

    public function test_can_get_default_lookup_columns(): void
    {
        $columns = $this->repository->getLookupColumnsToFilterPublic();

        $this->assertSame(['id' => 'int'], $columns);
    }

    public function test_can_list_without_filters(): void
    {
        $paginator   = $this->makePaginator();
        $builderMock = $this->makeBuilderMock($paginator);

        $this->modelMock
            ->shouldReceive('query')
            ->once()
            ->andReturn($builderMock);

        $result = $this->repository->list([]);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertSame($paginator, $result);
    }

    public function test_can_list_with_custom_pagination(): void
    {
        $paginator   = $this->makePaginator(perPage: 5, total: 50);
        $builderMock = $this->makeBuilderMock($paginator, perPage: 5, page: 2);

        $this->modelMock
            ->shouldReceive('query')
            ->once()
            ->andReturn($builderMock);

        $result = $this->repository->list(['per_page' => 5, 'page' => 2]);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    public function test_can_find_entity_by_id(): void
    {
        $entity = Mockery::mock(Model::class);

        $this->modelMock
            ->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($entity);

        $result = $this->repository->getById(1);

        $this->assertSame($entity, $result);
    }

    public function test_cannot_find_entity_with_nonexistent_id(): void
    {
        $this->modelMock
            ->shouldReceive('find')
            ->once()
            ->with(999)
            ->andReturn(null);

        $result = $this->repository->getById(999);

        $this->assertNull($result);
    }

    public function test_can_store_and_return_new_entity(): void
    {
        $data   = $this->makeDataObject(['id' => 1, 'name' => 'Novo']);
        $entity = Mockery::mock(Model::class);

        $this->modelMock
            ->shouldReceive('create')
            ->once()
            ->with(['name' => 'Novo'])
            ->andReturn($entity);

        $result = $this->repository->store($data);

        $this->assertSame($entity, $result);
    }

    public function test_cannot_store_with_id_field_in_payload(): void
    {
        $data = $this->makeDataObject(['id' => 42, 'title' => 'Teste']);
        $receivedPayload = null;

        $this->modelMock
            ->shouldReceive('create')
            ->once()
            ->withArgs(function (array $payload) use (&$receivedPayload) {
                $receivedPayload = $payload;

                return true;
            })
            ->andReturn(Mockery::mock(Model::class));

        $this->repository->store($data);

        $this->assertSame(['title' => 'Teste'], $receivedPayload);
        $this->assertArrayNotHasKey('id', $receivedPayload);
    }

    public function test_can_update_and_return_fresh_entity(): void
    {
        $data          = $this->makeDataObject(['id' => 1, 'name' => 'Atualizado']);
        $entity        = Mockery::mock(Model::class);
        $freshEntity   = Mockery::mock(Model::class);

        $entity->shouldReceive('update')
            ->once()
            ->with(['name' => 'Atualizado']);

        $entity->shouldReceive('fresh')
            ->once()
            ->andReturn($freshEntity);

        $result = $this->repository->update($entity, $data);

        $this->assertSame($freshEntity, $result);
    }

    public function test_cannot_update_with_id_field_in_payload(): void
    {
        $data   = $this->makeDataObject(['id' => 5, 'name' => 'X']);
        $entity = Mockery::mock(Model::class);
        $receivedPayload = null;

        $entity->shouldReceive('update')
            ->once()
            ->withArgs(function (array $payload) use (&$receivedPayload) {
                $receivedPayload = $payload;

                return true;
            });

        $entity->shouldReceive('fresh')->andReturn(Mockery::mock(Model::class));

        $this->repository->update($entity, $data);

        $this->assertSame(['name' => 'X'], $receivedPayload);
        $this->assertArrayNotHasKey('id', $receivedPayload);
    }

    public function test_can_delete_entity(): void
    {
        $entity = Mockery::mock(Model::class);

        $entity->shouldReceive('delete')
            ->once()
            ->andReturn(true);

        $result = $this->repository->delete($entity);

        $this->assertTrue($result);
    }

    public function test_cannot_delete_entity_when_repository_fails(): void
    {
        $entity = Mockery::mock(Model::class);

        $entity->shouldReceive('delete')
            ->once()
            ->andReturn(false);

        $result = $this->repository->delete($entity);

        $this->assertFalse($result);
    }

    public function test_can_lookup_without_params(): void
    {
        $paginator   = $this->makePaginator();
        $builderMock = $this->makeBuilderMock($paginator, perPage: 30);

        $this->modelMock
            ->shouldReceive('query')
            ->once()
            ->andReturn($builderMock);

        $result = $this->repository->lookup([]);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertSame($paginator, $result);
    }

    public function test_can_lookup_with_text_filter(): void
    {
        $paginator   = $this->makePaginator();
        $builderMock = $this->makeBuilderMock($paginator, perPage: 30);
        $nestedBuilderMock = Mockery::mock(Builder::class);

        $builderMock->shouldReceive('where')
            ->once()
            ->withArgs(function ($arg) use ($nestedBuilderMock) {
                if (!$arg instanceof \Closure) {
                    return false;
                }

                $nestedBuilderMock
                    ->shouldReceive('orWhere')
                    ->once()
                    ->with('id', '=', 0)
                    ->andReturnSelf();

                return $arg($nestedBuilderMock) === $nestedBuilderMock;
            })
            ->andReturnSelf();

        $this->modelMock
            ->shouldReceive('query')
            ->once()
            ->andReturn($builderMock);

        $result = $this->repository->lookup(['q' => 'busca']);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    public function test_can_lookup_apply_text_filter_to_configured_columns(): void
    {
        $repository = new class($this->modelMock) extends BaseRepository {
            public function __construct(Model $model)
            {
                $this->model = $model;
            }

            protected function getModelClass(): string
            {
                return Model::class;
            }

            protected function getLookupColumnsToFilter(): array
            {
                return [
                    'name' => 'string',
                    'id' => 'int',
                    'external_code' => 'uuid',
                ];
            }
        };

        $paginator = $this->makePaginator();
        $builderMock = $this->makeBuilderMock($paginator, perPage: 30);
        $nestedBuilderMock = Mockery::mock(Builder::class);

        $builderMock->shouldReceive('where')
            ->once()
            ->withArgs(function ($arg) use ($nestedBuilderMock) {
                if (!$arg instanceof \Closure) {
                    return false;
                }

                $nestedBuilderMock
                    ->shouldReceive('orWhere')
                    ->once()
                    ->ordered()
                    ->with('name', 'like', '%123%')
                    ->andReturnSelf();

                $nestedBuilderMock
                    ->shouldReceive('orWhere')
                    ->once()
                    ->ordered()
                    ->with('id', '=', 123)
                    ->andReturnSelf();

                $nestedBuilderMock
                    ->shouldReceive('orWhere')
                    ->once()
                    ->ordered()
                    ->with('external_code', '=', ' 123 ')
                    ->andReturnSelf();

                return $arg($nestedBuilderMock) === $nestedBuilderMock;
            })
            ->andReturnSelf();

        $this->modelMock
            ->shouldReceive('query')
            ->once()
            ->andReturn($builderMock);

        $result = $repository->lookup(['q' => ' 123 ']);

        $this->assertSame($paginator, $result);
    }

    public function test_can_lookup_filtered_by_keys(): void
    {
        $paginator   = $this->makePaginator();
        $builderMock = $this->makeBuilderMock($paginator, perPage: 30);

        $builderMock->shouldReceive('whereIn')
            ->once()
            ->with('id', [1, 2, 3])
            ->andReturnSelf();

        $this->modelMock
            ->shouldReceive('query')
            ->once()
            ->andReturn($builderMock);

        $result = $this->repository->lookup(['keys' => [1, 2, 3]]);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    public function test_can_lookup_with_custom_pagination(): void
    {
        $paginator   = $this->makePaginator(perPage: 10);
        $builderMock = $this->makeBuilderMock($paginator, perPage: 10, page: 3);

        $this->modelMock
            ->shouldReceive('query')
            ->once()
            ->andReturn($builderMock);

        $result = $this->repository->lookup(['per_page' => 10, 'page' => 3]);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    public function test_can_sync_relation_and_return_fresh_entity(): void
    {
        $relationMock = Mockery::mock(BelongsToMany::class);
        $relationMock->shouldReceive('sync')
            ->once()
            ->with([1, 2, 3]);
 
        $freshEntity = Mockery::mock(Model::class);
 
        // Entidade concreta real (não mock puro) para que method_exists('tags') retorne true.
        // O método tags() é sobrescrito para devolver o mock da relação.
        $entity = new class($relationMock, $freshEntity) extends Model {
            public function __construct(
                private ?BelongsToMany $relationMock = null,
                private ?Model $freshEntity = null,
            ) {
                parent::__construct();
            }
 
            public function tags(): BelongsToMany
            {
                if ($this->relationMock === null) {
                    throw new Exception('Relation mock was not configured.');
                }

                return $this->relationMock;
            }
 
            public function fresh($with = []): ?Model
            {
                if ($this->freshEntity === null) {
                    throw new Exception('Fresh entity mock was not configured.');
                }

                return $this->freshEntity;
            }
        };
 
        $result = $this->repository->sync($entity, 'tags', [1, 2, 3]);
 
        $this->assertSame($freshEntity, $result);
    }

    public function test_cannot_sync_with_nonexistent_relation_method(): void
    {
        $entity = Mockery::mock(Model::class);
 
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/inexistente/');
 
        $this->repository->sync($entity, 'inexistente', []);
    }
}
