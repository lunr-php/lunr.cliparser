<?php

/**
 * This file contains the GetoptCliParserParseTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2013 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Shadow\Tests;

/**
 * This class contains test methods for parse() in the GetoptCliParser class.
 *
 * @covers Lunr\Shadow\GetoptCliParser
 */
class GetoptCliParserParseTest extends GetoptCliParserTestCase
{

    /**
     * Test that wrapArgument() replaces a FALSE value with an empty array.
     *
     * @covers Lunr\Shadow\GetoptCliParser::wrapArgument
     */
    public function testWrapArgumentReturnsEmptyArrayForFalse(): void
    {
        $method = $this->getReflectionMethod('wrapArgument');

        $value = $method->invokeArgs($this->class, [ FALSE ]);

        $this->assertArrayEmpty($value);
    }

    /**
     * Test that wrapArgument() replaces a FALSE value with an empty array.
     *
     * @param mixed $cliValue Value to wrap
     *
     * @dataProvider valueProvider
     * @covers       Lunr\Shadow\GetoptCliParser::wrapArgument
     */
    public function testWrapArgumentReturnsValueWrappedInArray($cliValue): void
    {
        $method = $this->getReflectionMethod('wrapArgument');

        $value = $method->invokeArgs($this->class, [ $cliValue ]);

        $this->assertEquals([ $cliValue ], $value);
    }

    /**
     * Test that wrapArgument() does not re-wrap already wrapped arguments (like multiple parameters).
     *
     * @covers Lunr\Shadow\GetoptCliParser::wrapArgument
     */
    public function testWrapArgumentDoesNotRewrapArguments(): void
    {
        $method = $this->getReflectionMethod('wrapArgument');

        $value = $method->invokeArgs($this->class, [ [ 'param1', 'param2' ] ]);

        $this->assertEquals([ 'param1', 'param2' ], $value);
    }

    /**
     * Test that parse() returns an empty array on error.
     *
     * @covers   Lunr\Shadow\GetoptCliParser::parse
     */
    public function testParseReturnsEmptyArrayOnError(): void
    {
        $this->mockFunction('getopt', function () { return FALSE; });

        $value = $this->class->parse();

        $this->assertArrayEmpty($value);
        $this->unmockFunction('getopt');
    }

    /**
     * Test that parse() sets error to TRUE on error.
     *
     * @covers   Lunr\Shadow\GetoptCliParser::parse
     */
    public function testParseSetsErrorTrueOnError(): void
    {
        $this->mockFunction('getopt', function () { return FALSE; });

        $this->class->parse();

        $this->assertTrue($this->getReflectionPropertyValue('error'));
        $this->unmockFunction('getopt');
    }

    /**
     * Test that parse() returns an ast array on success.
     *
     * @covers   Lunr\Shadow\GetoptCliParser::parse
     */
    public function testParseReturnsAstOnSuccess(): void
    {
        $this->mockFunction('getopt', function () { return [ 'a' => FALSE, 'b' => 'arg' ]; });

        $value = $this->class->parse();

        $this->assertIsArray($value);
        $this->assertEquals([ 'a' => [], 'b' => [ 'arg' ] ], $value);
        $this->unmockFunction('getopt');
    }

}

?>
