<?php

/**
 * This file contains the LunrCliParserCheckObligatoryArgumentTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2013 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Shadow\Tests;

/**
 * This class contains test methods for check_arguments() in the LunrCliParser class.
 *
 * @covers Lunr\Shadow\LunrCliParser
 */
class LunrCliParserCheckObligatoryArgumentTest extends LunrCliParserTestCase
{

    /**
     * Test that check_argument() returns TRUE for a valid parameter with one argument.
     *
     * @covers Lunr\Shadow\LunrCliParser::check_argument
     */
    public function testCheckArgumentReturnsTrueForValidParameterWithOneArg(): void
    {
        $this->setReflectionPropertyValue('args', [ 'test.php', '-b', 'arg' ]);

        $this->setReflectionPropertyValue('ast', [ 'b' => [] ]);

        $method = $this->getReflectionMethod('check_argument');

        $value = $method->invokeArgs($this->class, [ 'b', 1, 0, 'b:' ]);

        $this->assertTrue($value);
    }

    /**
     * Test that check_argument() returns TRUE for a valid parameter with one argument.
     *
     * @covers Lunr\Shadow\LunrCliParser::check_argument
     */
    public function testCheckArgumentReturnsTrueForValidParameterWithTwoArgs(): void
    {
        $this->setReflectionPropertyValue('args', [ 'test.php', '-e', 'arg1', 'arg2' ]);

        $this->setReflectionPropertyValue('ast', [ 'e' => [] ]);

        $method = $this->getReflectionMethod('check_argument');

        $value = $method->invokeArgs($this->class, [ 'e', 1, 0, 'e::' ]);

        $this->assertTrue($value);
    }

    /**
     * Test that check_argument() appends first argument to ast.
     *
     * @covers Lunr\Shadow\LunrCliParser::check_argument
     */
    public function testCheckArgumentAppendsFirstArgumentToAst(): void
    {
        $this->setReflectionPropertyValue('args', [ 'test.php', '-b', 'arg' ]);

        $this->setReflectionPropertyValue('ast', [ 'b' => [] ]);

        $method = $this->getReflectionMethod('check_argument');

        $method->invokeArgs($this->class, [ 'b', 1, 0, 'b:' ]);

        $value = $this->getReflectionPropertyValue('ast');

        $this->assertCount(1, $value['b']);
        $this->assertEquals([ 'arg' ], $value['b']);
    }

    /**
     * Test that check_argument() appends first argument to ast.
     *
     * @covers Lunr\Shadow\LunrCliParser::check_argument
     */
    public function testCheckArgumentAppendsSecondArgumentToAst(): void
    {
        $this->setReflectionPropertyValue('args', [ 'test.php', '-e', 'arg1', 'arg2' ]);

        $this->setReflectionPropertyValue('ast', [ 'e' => [] ]);

        $method = $this->getReflectionMethod('check_argument');

        $method->invokeArgs($this->class, [ 'e', 1, 0, 'e::' ]);

        $value = $this->getReflectionPropertyValue('ast');

        $this->assertCount(2, $value['e']);
        $this->assertEquals([ 'arg1', 'arg2' ], $value['e']);
    }

    /**
     * Test that check_argument() returns FALSE when the argument is missing.
     *
     * @covers Lunr\Shadow\LunrCliParser::check_argument
     */
    public function testCheckArgumentReturnsFalseForArgumentMissing(): void
    {
        $this->setReflectionPropertyValue('args', [ 'test.php', '-b' ]);

        $this->setReflectionPropertyValue('ast', [ 'b' => [] ]);

        $method = $this->getReflectionMethod('check_argument');

        $this->expectUserWarning('Missing argument for -b');

        $value = $method->invokeArgs($this->class, [ 'b', 1, 0, 'b:' ]);

        $this->assertFalse($value);
    }

    /**
     * Test that check_argument() returns FALSE when the argument is missing.
     *
     * @covers Lunr\Shadow\LunrCliParser::check_argument
     */
    public function testCheckArgumentReturnsFalseForArgumentMissingWithAnotherParameterAfter(): void
    {
        $this->setReflectionPropertyValue('args', [ 'test.php', '-b', '-c' ]);

        $this->setReflectionPropertyValue('ast', [ 'b' => [] ]);

        $method = $this->getReflectionMethod('check_argument');

        $this->expectUserWarning('Missing argument for -b');

        $value = $method->invokeArgs($this->class, [ 'b', 1, 0, 'b:' ]);

        $this->assertFalse($value);
    }

}

?>
