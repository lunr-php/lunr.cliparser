<?php

/**
 * This file contains the GetoptCliParserTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2013 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Shadow\Tests;

use Lunr\Halo\LunrBaseTestCase;
use Lunr\Shadow\GetoptCliParser;

/**
 * This class contains common setup routines, providers
 * and shared attributes for testing the GetoptCliParser class.
 *
 * @covers Lunr\Shadow\GetoptCliParser
 */
abstract class GetoptCliParserTestCase extends LunrBaseTestCase
{

    /**
     * Instance of the tested class.
     * @var GetoptCliParser
     */
    protected GetoptCliParser $class;

    /**
     * Test case constructor.
     */
    public function setUp(): void
    {
        $this->class = new GetoptCliParser('ab:c::', [ 'first', 'second:', 'third::' ]);

        parent::baseSetUp($this->class);
    }

    /**
     * Test case destructor.
     */
    public function tearDown(): void
    {
        unset($this->class);

        parent::tearDown();
    }

    /**
     * Unit test data provider for command line values.
     *
     * @return array $values Array of command line argument values.
     */
    public static function valueProvider()
    {
        $values   = [];
        $values[] = [ 'string' ];
        $values[] = [ 1 ];
        $values[] = [ 1.1 ];
        $values[] = [ TRUE ];

        return $values;
    }

}

?>
