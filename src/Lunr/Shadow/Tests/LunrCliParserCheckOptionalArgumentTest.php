<?php

/**
 * This file contains the LunrCliParserCheckOptionalArgumentTest class.
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
class LunrCliParserCheckOptionalArgumentTest extends LunrCliParserTestCase
{

    /**
     * Test that check_argument() returns TRUE for a valid parameter with one argument.
     *
     * @covers Lunr\Shadow\LunrCliParser::check_argument
     */
    public function testCheckArgumentReturnsTrueForValidParameterWithOneArg(): void
    {
        $this->setReflectionPropertyValue('args', [ 'test.php', '-c', 'arg' ]);

        $this->setReflectionPropertyValue('ast', [ 'c' => [] ]);

        $method = $this->getReflectionMethod('check_argument');

        $value = $method->invokeArgs($this->class, [ 'c', 1, 0, 'c;' ]);

        $this->assertTrue($value);
    }

    /**
     * Test that check_argument() returns TRUE for a valid parameter with one argument.
     *
     * @covers Lunr\Shadow\LunrCliParser::check_argument
     */
    public function testCheckArgumentReturnsTrueForValidParameterWithTwoArgs(): void
    {
        $this->setReflectionPropertyValue('args', [ 'test.php', '-f', 'arg1', 'arg2' ]);

        $this->setReflectionPropertyValue('ast', [ 'f' => [] ]);

        $method = $this->getReflectionMethod('check_argument');

        $value = $method->invokeArgs($this->class, [ 'f', 1, 0, 'f;;' ]);

        $this->assertTrue($value);
    }

    /**
     * Test that check_argument() appends first argument to ast.
     *
     * @covers Lunr\Shadow\LunrCliParser::check_argument
     */
    public function testCheckArgumentAppendsFirstArgumentToAst(): void
    {
        $this->setReflectionPropertyValue('args', [ 'test.php', '-c', 'arg' ]);

        $this->setReflectionPropertyValue('ast', [ 'c' => [] ]);

        $method = $this->getReflectionMethod('check_argument');

        $method->invokeArgs($this->class, [ 'c', 1, 0, 'c;' ]);

        $value = $this->getReflectionPropertyValue('ast');

        $this->assertCount(1, $value['c']);
        $this->assertEquals([ 'arg' ], $value['c']);
    }

    /**
     * Test that check_argument() appends first argument to ast.
     *
     * @covers Lunr\Shadow\LunrCliParser::check_argument
     */
    public function testCheckArgumentAppendsSecondArgumentToAst(): void
    {
        $this->setReflectionPropertyValue('args', [ 'test.php', '-f', 'arg1', 'arg2' ]);

        $this->setReflectionPropertyValue('ast', [ 'f' => [] ]);

        $method = $this->getReflectionMethod('check_argument');

        $method->invokeArgs($this->class, [ 'f', 1, 0, 'f;;' ]);

        $value = $this->getReflectionPropertyValue('ast');

        $this->assertCount(2, $value['f']);
        $this->assertEquals([ 'arg1', 'arg2' ], $value['f']);
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

        $value = $method->invokeArgs($this->class, [ 'b', 1, 0, 'b;' ]);

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

        $value = $method->invokeArgs($this->class, [ 'b', 1, 0, 'b;' ]);

        $this->assertFalse($value);
    }

}

?>
