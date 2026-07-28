<?php

namespace Tests\Unit\Core\Services;

use App\Core\DTO\ServiceResult;
use App\Core\Repositories\Interfaces\BaseRepositoryInterface;
use App\Core\Services\BaseCrudService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class BaseCrudServiceTest extends TestCase
{
    private BaseRepositoryInterface|MockInterface $repositoryMock;
    private BaseCrudService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repositoryMock = Mockery::mock(BaseRepositoryInterface::class);

        $this->service = new class($this->repositoryMock) extends BaseCrudService {
            public function __construct(BaseRepositoryInterface $repository)
            {
                $this->repository = $repository;
            }
        };
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_can_store(): void
    {
        $data   = ['name' => 'Teste'];
        $entity = Mockery::mock(Model::class);

        $this->repositoryMock
            ->shouldReceive('store')
            ->once()
            ->with($data)
            ->andReturn($entity);

        $result = $this->service->store($data);

        $this->assertInstanceOf(ServiceResult::class, $result);
        $this->assertSame($entity, $result->data);
    }

    public function test_can_get_data_for_edit(): void
    {
        $entity = Mockery::mock(Model::class);

        $result = $this->service->edit($entity);

        $this->assertInstanceOf(ServiceResult::class, $result);
        $this->assertSame($entity, $result->data);
        $this->assertTrue($result->meta['editable']);
        $this->assertSame([], $result->meta['warnings']);
    }

    public function test_can_update_an_entity(): void
    {
        $data          = ['name' => 'Atualizado'];
        $entity        = Mockery::mock(Model::class);
        $updatedEntity = Mockery::mock(Model::class);

        $this->repositoryMock
            ->shouldReceive('update')
            ->once()
            ->with($entity, $data)
            ->andReturn($updatedEntity);

        $result = $this->service->update($entity, $data);

        $this->assertInstanceOf(ServiceResult::class, $result);
        $this->assertSame($updatedEntity, $result->data);
    }

    public function test_can_delete_an_entity(): void
    {
        $entity = Mockery::mock(Model::class);

        $this->repositoryMock
            ->shouldReceive('delete')
            ->once()
            ->with($entity)
            ->andReturn(true);

        $result = $this->service->delete($entity);

        $this->assertInstanceOf(ServiceResult::class, $result);
        $this->assertNull($result->data);
        $this->assertTrue($result->meta['deleted']);
    }

    public function test_cannot_delete_an_entity(): void
    {
        $entity = Mockery::mock(Model::class);

        $this->repositoryMock
            ->shouldReceive('delete')
            ->once()
            ->with($entity)
            ->andReturn(false);

        $result = $this->service->delete($entity);

        $this->assertInstanceOf(ServiceResult::class, $result);
        $this->assertNull($result->data);
        $this->assertFalse($result->meta['deleted']);
    }

    public function test_can_list_and_return_paginator(): void
    {
        $filters    = ['status' => 'active'];
        $paginator = new LengthAwarePaginator(
            items:       [Mockery::mock(Model::class)],
            total:       1,
            perPage:     15,
            currentPage: 1,
        );

        $this->repositoryMock
            ->shouldReceive('list')
            ->once()
            ->with($filters)
            ->andReturn($paginator);

        $result = $this->service->list($filters);

        $this->assertInstanceOf(ServiceResult::class, $result);
        $this->assertInstanceOf(LengthAwarePaginator::class, $result->data);
        $this->assertSame($paginator, $result->data);
    }

    public function test_can_list_and_return_empty_paginator(): void
    {
        $paginator = new LengthAwarePaginator([], 0, 15, 1);

        $this->repositoryMock
            ->shouldReceive('list')
            ->once()
            ->with([])
            ->andReturn($paginator);

        $result = $this->service->list();

        $this->assertInstanceOf(ServiceResult::class, $result);
        $this->assertInstanceOf(LengthAwarePaginator::class, $result->data);
    }

    public function test_can_show(): void
    {
        $entity = Mockery::mock(Model::class);

        $result = $this->service->show($entity);

        $this->assertInstanceOf(ServiceResult::class, $result);
        $this->assertSame($entity, $result->data);
    }

    public function test_can_find(): void
    {
        $entity = Mockery::mock(Model::class);
        $result = $this->service->find(1);

        $this->repositoryMock
            ->shouldReceive('getById')
            ->once()
            ->with(1)
            ->andReturn($entity);

        $this->assertInstanceOf(ServiceResult::class, $result);
        $this->assertSame($entity, $result->data);
    }

    public function test_can_get_data_for_lookup(): void
    {
        $params     = ['key' => 'value'];
        $paginator = new LengthAwarePaginator(
            items:       [Mockery::mock(Model::class)],
            total:       1,
            perPage:     15,
            currentPage: 1,
        );

        $this->repositoryMock
            ->shouldReceive('lookup')
            ->once()
            ->with($params)
            ->andReturn($paginator);

        $result = $this->service->lookup($params);

        $this->assertInstanceOf(ServiceResult::class, $result);
        $this->assertInstanceOf(LengthAwarePaginator::class, $result->data);
        $this->assertSame($paginator, $result->data);
    }
}