<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function testThatTrueIsTrue(): void
    {
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertTrue(true);
    }
}
