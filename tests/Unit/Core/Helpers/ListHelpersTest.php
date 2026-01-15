<?php

namespace Tests\Unit\Core\Helpers;

use App\Core\Helpers\ListHelpers;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class ListHelpersTest extends TestCase
{

    public function test_group_properties_of_an_array(): void
    {
        $array = [
            [
                'id' => 1,
                'name' => 'tag one'
            ],
            [
                'id' => 2,
                'name' => 'tag two'
            ]
        ];

        $grouped = ListHelpers::groupListByProperty($array, 'id');

        $this->assertIsArray($grouped);
        $this->assertCount(2, $grouped);
        $this->assertEquals([1, 2], $grouped);
    }

    public function test_group_properties_of_an_nexted_array(): void
    {
        $array = [
            [
                'title' => 'test',
                'tag' => [
                    'id' => 1,
                    'name' => 'tag one'
                ],
            ],
            [
                'title' => 'test',
                'tag' => [
                    'id' => 2,
                    'name' => 'tag two'
                ],
            ],
        ];

        $grouped = ListHelpers::groupListByProperty($array, 'tag.id');

        $this->assertIsArray($grouped);
        $this->assertCount(2, $grouped);
        $this->assertEquals([1, 2], $grouped);
    }

    public function test_remove_null_properties_from_simple_array(): void
    {
        $product = [
            'name' => 'Computer',
            'price' => 1999.9,
            'description' => null
        ];

        $productCleaned = ListHelpers::removeNullProperties($product);

        $this->assertIsArray($productCleaned);
        $this->assertArrayHasKey('name', $productCleaned);
        $this->assertArrayHasKey('price', $productCleaned);
        $this->assertArrayNotHasKey('description', $productCleaned);
    }

    public function test_remove_null_properties_from_array_with_subitems(): void
    {
        $order = [
            'date' => Carbon::now(),
            'total' => 2599.7,
            'notes' => null,
            'items' => [
                [
                    'name' => 'Computer',
                    'price' => 1999.9,
                    'description' => null
                ],
                [
                    'name' => 'Smartphone',
                    'price' => 599.8,
                    'description' => 'Last gen smartphone.'
                ]
            ]
        ];

        $orderCleaned = ListHelpers::removeNullProperties($order);

        $this->assertIsArray($orderCleaned);
        $this->assertArrayHasKey('date', $orderCleaned);
        $this->assertArrayHasKey('total', $orderCleaned);
        $this->assertArrayNotHasKey('notes', $orderCleaned);
        $this->assertArrayNotHasKey('description', $orderCleaned['items'][0]);
        $this->assertArrayHasKey('description', $orderCleaned['items'][1]);
    }
}
