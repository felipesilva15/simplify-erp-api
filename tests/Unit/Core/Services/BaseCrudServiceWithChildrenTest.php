<?php

namespace Tests\Unit\Core\Services;

use App\Core\DTO\AttributesDTO;
use App\Core\DTO\ServiceResult;
use App\Core\Enums\ActivityActionEnum;
use App\Modules\Geography\Models\City;
use App\Core\Repositories\Interfaces\BaseRepositoryInterface;
use App\Core\Services\ActivityLogService;
use App\Core\Services\BaseCrudService;
use App\Core\Services\Children\BaseChildSync;
use App\Core\Services\Children\ChildRelation;
use App\Core\Services\Children\ChildSyncResult;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class BaseCrudServiceWithChildrenTest extends TestCase
{
    private BaseRepositoryInterface|MockInterface $repositoryMock;
    private ActivityLogService|MockInterface $activityMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repositoryMock = Mockery::mock(BaseRepositoryInterface::class);
        $this->activityMock   = Mockery::mock(ActivityLogService::class);

        SpyChildSync::$calls = [];
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function plainService(): BaseCrudService
    {
        return new class($this->repositoryMock, $this->activityMock) extends BaseCrudService {
            public function __construct(BaseRepositoryInterface $repository, ActivityLogService $activity)
            {
                $this->repository = $repository;
                $this->activity = $activity;
            }
        };
    }

    private function serviceWithChildren(): BaseCrudService
    {
        return new class($this->repositoryMock, $this->activityMock) extends BaseCrudService {
            public function __construct(BaseRepositoryInterface $repository, ActivityLogService $activity)
            {
                $this->repository = $repository;
                $this->activity = $activity;
            }

            protected function childRelations(): array
            {
                return [
                    'children' => new ChildRelation(
                        relation: 'children',
                        model: City::class,
                        sync: SpyChildSync::class,
                    ),
                ];
            }
        };
    }

    private function headerMockExpectingEagerLoad(): Model
    {
        $header = Mockery::mock(Model::class);
        $header->shouldReceive('loadMissing')->with(['children'])->andReturnSelf();

        return $header;
    }

    public function test_resource_without_children_is_untouched(): void
    {
        $data   = ['name' => 'Teste'];
        $entity = Mockery::mock(Model::class);

        $this->repositoryMock->shouldReceive('store')->once()->with($data)->andReturn($entity);
        $this->activityMock->shouldReceive('log')->once()->with($entity, ActivityActionEnum::Created);

        $result = $this->plainService()->store($data);

        $this->assertInstanceOf(ServiceResult::class, $result);
        $this->assertSame($entity, $result->data);
        $this->assertSame([], $result->meta, 'Recursos sem filhos não devem receber meta de children.');
    }

    public function test_store_persists_header_without_children_and_syncs_items(): void
    {
        $header = $this->headerMockExpectingEagerLoad();

        $this->repositoryMock
            ->shouldReceive('store')
            ->once()
            ->with(Mockery::on(fn ($arg): bool => $arg instanceof AttributesDTO && $arg->toArray() === ['name' => 'Teste']))
            ->andReturn($header);

        $this->activityMock->shouldReceive('log')->once()->with($header, ActivityActionEnum::Created);

        $result = $this->serviceWithChildren()->store(new HeaderDTO('Teste', [['name' => 'A']]));

        $this->assertSame($header, $result->data);
        $this->assertSame(
            ['children' => ['children' => ['created' => 1, 'updated' => 0, 'deleted' => 0]]],
            $result->meta
        );
        $this->assertSame([['name' => 'A']], SpyChildSync::$calls);
    }

    public function test_store_skips_sync_when_children_key_is_absent(): void
    {
        $header = $this->headerMockExpectingEagerLoad();

        $this->repositoryMock
            ->shouldReceive('store')
            ->once()
            ->with(Mockery::on(fn ($arg): bool => $arg instanceof AttributesDTO && $arg->toArray() === ['name' => 'Teste']))
            ->andReturn($header);

        $this->activityMock->shouldReceive('log')->once()->with($header, ActivityActionEnum::Created);

        $result = $this->serviceWithChildren()->store(new HeaderDTO('Teste', null));

        $this->assertSame([], $result->meta);
        $this->assertSame([], SpyChildSync::$calls, 'Chave ausente não pode sincronizar itens.');
    }

    public function test_update_skips_sync_when_children_key_is_absent(): void
    {
        $header  = Mockery::mock(Model::class);
        $updated = $this->headerMockExpectingEagerLoad();

        $this->repositoryMock
            ->shouldReceive('update')
            ->once()
            ->with($header, Mockery::on(fn ($arg): bool => $arg instanceof AttributesDTO && $arg->toArray() === ['name' => 'Novo']))
            ->andReturn($updated);

        $this->activityMock->shouldReceive('log')->once()->with($updated, ActivityActionEnum::Updated);

        $result = $this->serviceWithChildren()->update($header, new HeaderDTO('Novo', null));

        $this->assertSame($updated, $result->data);
        $this->assertSame([], $result->meta);
        $this->assertSame([], SpyChildSync::$calls);
    }

    public function test_children_are_eager_loaded_on_reads(): void
    {
        $header = Mockery::mock(Model::class);
        $header->shouldReceive('loadMissing')->twice()->with(['children'])->andReturnSelf();

        $this->assertSame($header, $this->serviceWithChildren()->show($header)->data);
        $this->assertSame($header, $this->serviceWithChildren()->edit($header)->data);
    }

    public function test_load_children_tolerates_null_entity(): void
    {
        $this->repositoryMock->shouldReceive('getById')->once()->with(404)->andReturnNull();

        $this->assertNull($this->serviceWithChildren()->find(404)->data);
    }
}

class HeaderDTO
{
    public function __construct(
        private string $name,
        public ?array $children = null,
    ) { }

    public function toArray(): array
    {
        return ['name' => $this->name, 'children' => $this->children];
    }
}

class SpyChildSync extends BaseChildSync
{
    /** @var array<int, array<string, mixed>> */
    public static array $calls = [];

    public function __construct(
        Model $header,
        ChildRelation $definition,
        ActivityLogService $activity,
    ) {
        $this->header = $header;
        $this->definition = $definition;
        $this->activity = $activity;

        $relation = Mockery::mock(HasMany::class);
        $relation->shouldReceive('getForeignKeyName')->andReturn('header_id');
        $relation->shouldReceive('getRelated')->andReturn(new City);

        $this->relation = $relation;
    }

    protected function currentItems(): array
    {
        return [];
    }

    protected function createItem(array $payload, int $index): Model
    {
        self::$calls[] = $payload;

        return Mockery::mock(Model::class);
    }

    protected function updateItem(Model $current, array $payload, int $index): bool
    {
        self::$calls[] = $payload;

        return true;
    }
}
