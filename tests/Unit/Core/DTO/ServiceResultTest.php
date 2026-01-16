<?php

namespace Tests\Unit\Core\DTO;

use App\Core\DTO\ServiceResult;
use PHPUnit\Framework\TestCase;

class ServiceResultTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_has_warnings_return_correct_value(): void
    {
        $serviceResultWithWarnings = new ServiceResult(
            data: [
                'id' => 1,
                'name' => 'Felipe'
            ],
            warnings: ['Este registro não está ativo.']
        );

        $this->assertTrue($serviceResultWithWarnings->hasWarnings());

        $serviceResultWithoutWarnings = new ServiceResult(
            data: [
                'id' => 1,
                'name' => 'Felipe'
            ]
        );
        
        $this->assertFalse($serviceResultWithoutWarnings->hasWarnings());
    }
}
