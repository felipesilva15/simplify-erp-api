<?php

namespace Tests\Feature\UI;

use Tests\TestCase;

class ScalarDocumentationTest extends TestCase
{
    public function test_scallar_ui_is_accessible() : void
    {
        $response = $this->get('/api-docs');

        $response->assertOk()
                ->assertHeader('Content-Type', 'text/html; charset=UTF-8')
                ->assertSeeHtml('div id="app"');
    }

    public function test_openapi_json_is_valid(): void
    {
        $response = $this->getJson('/docs?api-docs.json');

        $response->assertOk()
                ->assertJsonIsObject()
                ->assertJsonStructure([
                    'openapi',
                    'info',
                    'paths',
                    'components',
                    'tags'
                ]);
    }
}
