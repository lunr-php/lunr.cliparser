<?php

/**
 * This file contains the LunrCliParserParseTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2013 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Shadow\Tests;

use UnexpectedValueException;

/**
 * This class contains test methods for parse() in the LunrCliParser class.
 *
 * @covers Lunr\Shadow\LunrCliParser
 */
class LunrCliParserParseTest extends LunrCliParserTest
{

    /**
     * Test that parsing with an invalid argv throws an exception.
     *
     * @covers Lunr\Shadow\LunrCliParser::parse
     */
    public function testParseArgvWithArgvAsString(): void
    {
        $_SERVER['argv'] = 'script.php';

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Command line arguments are not stored in an array!');

        $value = $this->class->parse();

        $this->assertArrayEmpty($value);
    }

    /**
     * Test that parsing with an invalid argv throws an exception.
     *
     * @covers Lunr\Shadow\LunrCliParser::parse
     */
    public function testParseArgvWithArgvAsAssociativeArray(): void
    {
        $_SERVER['argv'] = [ 1 => 'script.php' ];

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Command line arguments are not stored as a list!');

        $value = $this->class->parse();

        $this->assertArrayEmpty($value);
    }

    /**
     * Test that parsing with an invalid argv throws an exception.
     *
     * @covers Lunr\Shadow\LunrCliParser::parse
     */
    public function testParseArgvWithArgvAsIntArray(): void
    {
        $_SERVER['argv'] = [ 1, 'script.php' ];

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Command line argument 0 is not a string!');

        $value = $this->class->parse();

        $this->assertArrayEmpty($value);
    }

    /**
     * Test that parsing no arguments returns an empty array.
     *
     * @covers Lunr\Shadow\LunrCliParser::parse
     */
    public function testParseArgvWithNoArgumentsReturnsEmptyArray(): void
    {
        $_SERVER['argv'] = [ 'script.php' ];

        $value = $this->class->parse();

        $this->assertArrayEmpty($value);
    }

    /**
     * Test that parsing with incomplete arguments returns an empty array and sets the Error property.
     *
     * @param mixed $param Invalid Parameter
     *
     * @dataProvider invalidParameterProvider
     * @depends      Lunr\Shadow\Tests\LunrCliParserIsOptTest::testIsOptReturnsFalseForInvalidParameter
     * @covers       Lunr\Shadow\LunrCliParser::parse
     */
    public function testParseArgvWithIncompleteArguments($param): void
    {
        $_SERVER['argv'] = [ 'script.php', $param ];

        $this->expectUserWarning('Invalid parameter given: ' . $param);

        $value = $this->class->parse();

        $this->assertArrayEmpty($value);
        $this->assertTrue($this->get_reflection_property_value('error'));
    }

    /**
     * Test parsing valid short parameters.
     *
     * @param string $shortopt Short options string
     * @param array  $params   Array of passed arguments
     * @param array  $ast      Array of expected parsed ast
     *
     * @dataProvider validShortParameterProvider
     * @covers       Lunr\Shadow\LunrCliParser::parse
     */
    public function testParseValidShortParameters($shortopt, $params, $ast): void
    {
        $this->set_reflection_property_value('short', $shortopt);

        $_SERVER['argv'] = $params;

        $value = $this->class->parse();

        $this->assertSame($ast, $value);
    }

    /**
     * Test parsing valid short parameters.
     *
     * @param string $longopt Long options string
     * @param array  $params  Array of passed arguments
     * @param array  $ast     Array of expected parsed ast
     *
     * @dataProvider validLongParameterProvider
     * @covers       Lunr\Shadow\LunrCliParser::parse
     */
    public function testParseValidLongParameters($longopt, $params, $ast): void
    {
        $this->set_reflection_property_value('long', $longopt);

        $_SERVER['argv'] = $params;

        $value = $this->class->parse();

        $this->assertSame($ast, $value);
    }

}

?>
