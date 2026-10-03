<?php

namespace App\Core\Services;

use L5Swagger\ConfigFactory;
use L5Swagger\Generator;
use L5Swagger\GeneratorFactory;
use L5Swagger\SecurityDefinitions;
use OpenApi\Analysers\AttributeAnnotationFactory;
use OpenApi\Analysers\DocBlockAnnotationFactory;
use OpenApi\Analysers\ReflectionAnalyser;

/**
 * Restores docblock annotation support for l5-swagger 11.
 *
 * l5-swagger 11 forces `ReflectionAnalyser([AttributeAnnotationFactory])` whenever
 * `scanOptions.analyser` is empty, which silently drops every `@OA\*` docblock and
 * produces an empty spec. `doctrine/annotations` is required for the docblock parser
 * to be enabled at all. Both `l5-swagger:generate` and the UI controller resolve this
 * factory from the container, and l5-swagger's own factory hardcodes `new Generator(...)`,
 * so overriding it is the only way to inject the analyser.
 *
 * Docblocks keep working here, but they are deprecated in swagger-php 6.11 and removed
 * in 8.0: migrating to PHP attributes is the forward-compatible path.
 */
class SwaggerGeneratorFactory extends GeneratorFactory
{
    public function __construct(private readonly ConfigFactory $configFactory)
    {
    }

    public function make(string $documentation): Generator
    {
        $config = $this->configFactory->documentationConfig($documentation);

        $paths = $config['paths'];
        $scanOptions = $config['scanOptions'] ?? [];
        $constants = $config['constants'] ?? [];
        $yamlCopyRequired = $config['generate_yaml_copy'] ?? false;

        $secSchemesConfig = $config['securityDefinitions']['securitySchemes'] ?? [];
        $secConfig = $config['securityDefinitions']['security'] ?? [];

        $security = new SecurityDefinitions($secSchemesConfig, $secConfig);

        if (empty($scanOptions['analyser'])) {
            $scanOptions['analyser'] = new ReflectionAnalyser([
                new AttributeAnnotationFactory(),
                new DocBlockAnnotationFactory(),
            ]);
        }

        return new Generator(
            $paths,
            $constants,
            $yamlCopyRequired,
            $security,
            $scanOptions
        );
    }
}