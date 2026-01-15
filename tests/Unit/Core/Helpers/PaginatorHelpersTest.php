<?php

namespace Tests\Unit\Core\Helpers;

use App\Core\DTO\PaginatorInfo;
use App\Core\DTO\PaginatorLinks;
use App\Core\DTO\PaginatorMeta;
use App\Core\Helpers\PaginatorHelpers;
use App\Modules\Security\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;

class PaginatorHelpersTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_info_from_paginator(): void
    {
        User::factory()->count(12)->create();

        $paginator = User::paginate(perPage: 3, page: 1);

        $paginatorInfo = PaginatorHelpers::getInfoFromPaginator($paginator);

        $this->assertInstanceOf(PaginatorInfo::class, $paginatorInfo);
        $this->assertObjectHasProperty('links', $paginatorInfo);
        $this->assertObjectHasProperty('meta', $paginatorInfo);
        $this->assertNotNull($paginatorInfo->links);
        $this->assertNotNull($paginatorInfo->meta);
        $this->assertInstanceOf(PaginatorLinks::class, $paginatorInfo->links);
        $this->assertInstanceOf(PaginatorMeta::class, $paginatorInfo->meta);
    }
}
