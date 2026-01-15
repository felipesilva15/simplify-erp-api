<?php

namespace Tests\Unit\Core\Helpers;

use App\Core\Helpers\StringHelpers;
use Error;
use Illuminate\Support\Facades\Date;
use PHPUnit\Framework\TestCase;
use stdClass;

class StringHelpersTest extends TestCase
{
    public function test_can_convert_bool_to_string_literal(): void
    {
        $convertedFalse = StringHelpers::toStringLiteral(false);
        $convertedTrue = StringHelpers::toStringLiteral(true);

        $this->assertEquals('false', $convertedFalse);
        $this->assertIsString($convertedFalse);
        $this->assertEquals('true', $convertedTrue);
        $this->assertIsString($convertedTrue);
    }

    public function test_can_convert_int_to_string_literal(): void
    {
        $convertedValue = StringHelpers::toStringLiteral(100);

        $this->assertEquals('100', $convertedValue);
        $this->assertIsString($convertedValue);
    }

    public function test_can_convert_float_to_string_literal(): void
    {
        $convertedValue = StringHelpers::toStringLiteral(100.1);

        $this->assertEquals('100.1', $convertedValue);
        $this->assertIsString($convertedValue);
    }

    public function test_can_convert_null_to_string_literal(): void
    {
        $convertedValue = StringHelpers::toStringLiteral(null);

        $this->assertEquals('null', $convertedValue);
        $this->assertIsString($convertedValue);
    }

    public function test_can_convert_text_to_string_literal(): void
    {
        $convertedValue = StringHelpers::toStringLiteral('abc');

        $this->assertEquals("'abc'", $convertedValue);
        $this->assertIsString($convertedValue);
    }

    public function test_exception_is_thrown_when_convert_object_to_string_literal(): void
    {
        $this->expectException(Error::class);    
        StringHelpers::toStringLiteral(new stdClass());
    }
}
