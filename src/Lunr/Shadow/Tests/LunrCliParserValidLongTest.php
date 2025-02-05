<?php

/**
 * This file contains the LunrCliParserValidLongTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2013 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Shadow\Tests;

/**
 * This class contains test methods for is_valid_long() in the LunrCliParser class.
 *
 * @covers Lunr\Shadow\LunrCliParser
 */
class LunrCliParserValidLongTest extends LunrCliParserTestCase
{

    /**
     * Test that is_valid_long() returns FALSE for an invalid parameter.
     *
     * @param mixed $param Invalid Parameter
     *
     * @dataProvider invalidParameterProvider
     * @covers       Lunr\Shadow\LunrCliParser::is_valid_long
     */
    public function testIsValidLongReturnsFalseForInvalidParameter($param): void
    {
        $method = $this->getReflectionMethod('is_valid_long');

        $this->expectUserWarning('Invalid parameter given: ' . $param);

        $value = $method->invokeArgs($this->class, [ $param, 1 ]);

        $this->assertFalse($value);
    }

    /**
     * Test that is_valid_long() sets error to TRUE for an invalid parameter.
     *
     * @param mixed $param Invalid Parameter
     *
     * @dataProvider invalidParameterProvider
     * @covers       Lunr\Shadow\LunrCliParser::is_valid_long
     */
    public function testIsValidLongSetsErrorTrueForInvalidParameter($param): void
    {
        $method = $this->getReflectionMethod('is_valid_long');

        $this->expectUserWarning('Invalid parameter given: ' . $param);

        $method->invokeArgs($this->class, [ $param, 1 ]);

        $this->assertTrue($this->getReflectionPropertyValue('error'));
    }

    /**
     * Test that is_valid_long() adds a valid parameter to the ast array.
     *
     * @covers Lunr\Shadow\LunrCliParser::is_valid_long
     */
    public function testIsValidLongAddsValidParameterToAst(): void
    {
        $method = $this->getReflectionMethod('is_valid_long');

        $this->setReflectionPropertyValue('long', [ 'first' ]);

        $method->invokeArgs($this->class, [ 'first', 1 ]);

        $value = $this->getReflectionPropertyValue('ast');

        $this->assertArrayHasKey('first', $value);
        $this->assertEquals($value['first'], []);
    }

    /**
     * Test that is_valid_long() returns FALSE for a valid parameter without arguments.
     *
     * @depends Lunr\Shadow\Tests\LunrCliParserCheckArgumentTest::testCheckArgumentReturnsFalseForValidParameterWithoutArgs
     * @covers  Lunr\Shadow\LunrCliParser::is_valid_long
     */
    public function testIsValidLongReturnsFalseForValidParameterWithoutArguments(): void
    {
        $method = $this->getReflectionMethod('is_valid_long');

        $this->setReflectionPropertyValue('long', [ 'first' ]);

        $value = $method->invokeArgs($this->class, [ 'first', 1 ]);

        $this->assertFalse($value);
    }

    /**
     * Test that is_valid_long() returns TRUE for a valid parameter with arguments.
     *
     * @covers Lunr\Shadow\LunrCliParser::is_valid_long
     */
    public function testIsValidLongReturnsTrueForValidParameterWithArguments(): void
    {
        $method = $this->getReflectionMethod('is_valid_long');

        $this->setReflectionPropertyValue('args', [ 'test.php', '--second', 'arg' ]);

        $this->setReflectionPropertyValue('long', [ 'second:' ]);

        $value = $method->invokeArgs($this->class, [ 'second', 1 ]);

        $this->assertTrue($value);
    }

}

?>
