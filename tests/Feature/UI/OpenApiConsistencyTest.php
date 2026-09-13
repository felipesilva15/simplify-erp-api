<?php

namespace Tests\Feature\UI;

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use Tests\TestCase;

class OpenApiConsistencyTest extends TestCase
{
    private const DOCUMENTABLE_METHODS = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

    /**
     * Rotas registradas que não fazem parte do contrato da API e, portanto,
     * não devem ser exigidas como endpoints documentados.
     */
    private const EXCLUDED_ROUTES = [
        '/api/documentation',
        '/api/oauth2-callback',
        '/api/test',
    ];

    private function documentedOperations(): array {
        $response = $this->getJson('/docs?api-docs.json');

        $response->assertOk()
                 ->assertJsonIsObject();

        $operations = [];

        foreach ($response->json('paths') as $path => $methods) {
            foreach ($methods as $method => $operation) {
                if (! in_array(strtoupper($method), self::DOCUMENTABLE_METHODS, true)) {
                    continue;
                }

                $operations[] = [
                    'path' => $path,
                    'method' => strtoupper($method),
                ];
            }
        }

        return $operations;
    }

    private function apiRoutes(): array {
        return collect(RouteFacade::getRoutes()->getRoutes())
            ->map(fn (Route $route) => [
                'path' => '/' . trim($route->uri(), '/'),
                'methods' => array_map('strtoupper', $route->methods()),
            ])
            ->filter(fn (array $route) => str_starts_with($route['path'], '/api/'))
            ->values()
            ->all();
    }

    private function normalizePath(string $path): string {
        return rtrim($path, '/');
    }

    private function methodsMatch(string $documentedMethod, array $routeMethods): bool {
        $inDocumented = $documentedMethod;
        $normalized = $routeMethods;

        if (in_array($inDocumented, $normalized, true)) {
            return true;
        }

        return ($inDocumented === 'PUT' && in_array('PATCH', $normalized, true))
            || ($inDocumented === 'PATCH' && in_array('PUT', $normalized, true));
    }

    public function test_every_documented_operation_has_a_matching_route(): void
    {
        $routes = $this->apiRoutes();
        $normalizedRoutes = collect($routes)
            ->map(fn (array $route) => [
                'path' => $this->normalizePath($route['path']),
                'methods' => $route['methods'],
            ]);

        $outliers = collect($this->documentedOperations())
            ->filter(
                fn (array $operation) => ! $normalizedRoutes->contains(
                    fn (array $route) => $route['path'] === $this->normalizePath($operation['path'])
                        && $this->methodsMatch($operation['method'], $route['methods'])
                )
            )
            ->map(fn (array $operation) => "{$operation['method']} {$operation['path']}")
            ->values();

        $this->assertSame(
            [],
            $outliers->all(),
            'Endpoints documentados no OpenAPI que não existem como rota registrada: '.PHP_EOL.implode(PHP_EOL, $outliers->all())
        );
    }

    public function test_every_api_route_is_documented(): void
    {
        $specOperations = $this->documentedOperations();
        $excluded = self::EXCLUDED_ROUTES;

        $outliers = collect($this->apiRoutes())
            ->filter(fn (array $route) => ! in_array($route['path'], $excluded, true))
            ->filter(fn (array $route) => ! str_ends_with($route['path'], '/create'))
            ->filter(
                fn (array $route) => ! collect($specOperations)->contains(
                    fn (array $operation) => $this->normalizePath($operation['path']) === $this->normalizePath($route['path'])
                        && $this->methodsMatch($operation['method'], $route['methods'])
                )
            )
            ->map(fn (array $route) => implode('|', $route['methods']) . ' ' . $route['path'])
            ->values();

        $this->assertSame(
            [],
            $outliers->all(),
            'Rotas registradas que não estão documentadas no OpenAPI: '.PHP_EOL.implode(PHP_EOL, $outliers->all())
        );
    }
}