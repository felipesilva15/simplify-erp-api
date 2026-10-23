<?php

namespace Tests\Feature\Core;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RouteMacroTest extends TestCase
{
    public function test_crud_resource_registers_camel_case_route_names(): void
    {
        $this->assertTrue(Route::has('productCategories.index'));
        $this->assertTrue(Route::has('productCategories.show'));
        $this->assertTrue(Route::has('productCategories.activityLogs'));

        $this->assertFalse(Route::has('product-categories.index'));
        $this->assertFalse(Route::has('product-categories.activityLogs'));
    }

    public function test_crud_resource_keeps_kebab_case_uri(): void
    {
        $route = collect(Route::getRoutes()->getRoutes())
            ->first(fn ($route) => $route->getName() === 'productCategories.index');

        $this->assertNotNull($route);
        $this->assertSame('api/catalog/product-categories', $route->uri());
    }
}
