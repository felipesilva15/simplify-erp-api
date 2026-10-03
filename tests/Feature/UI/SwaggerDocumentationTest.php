<?php

namespace Tests\Feature\UI;

use Tests\TestCase;

class SwaggerDocumentationTest extends TestCase
{
    public function test_swagger_ui_is_accessible() : void
    {
        $response = $this->get('/api/documentation');

        $response->assertOk()
                ->assertHeader('Content-Type', 'text/html; charset=UTF-8')
                ->assertSeeHtml('<div id="swagger-ui"></div>');
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
