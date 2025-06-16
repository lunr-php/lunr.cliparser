<?php

/**
 * This file contains a getopt-based command line argument parser.
 *
 * SPDX-FileCopyrightText: Copyright 2013 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Shadow;

/**
 * Getopt command line argument parser.
 *
 * @phpstan-import-type CliParameters from CliParserInterface
 */
class GetoptCliParser implements CliParserInterface
{

    /**
     * String defining all possible short options (1 character)
     * @var string
     */
    private $short;

    /**
     * Array containing all possible long options
     * @var array<string,string>
     */
    private $long;

    /**
     * Whether there has been a parse error or not
     * @var bool
     */
    private $error;

    /**
     * Constructor.
     *
     * @param string               $shortopts List of supported short arguments
     * @param array<string,string> $longopts  List of supported long arguments (optional)
     */
    public function __construct(string $shortopts, array $longopts = [])
    {
        $this->short = $shortopts;
        $this->long  = $longopts;
        $this->error = FALSE;
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        unset($this->short);
        unset($this->long);
        unset($this->error);
    }

    /**
     * Parse command line arguments.
     *
     * @return CliParameters The ast of the parsed arguments
     */
    public function parse(): array
    {
        $raw = getopt($this->short, $this->long);

        if ($raw === FALSE)
        {
            $this->error = TRUE;
            return [];
        }

        return array_map([ $this, 'wrapArgument' ], $raw);
    }

    /**
     * Parse error information.
     *
     * @deprecated Use isInvalidCommandline() instead
     *
     * @return bool Whether there was a parse error or not
     */
    public function is_invalid_commandline(): bool
    {
        return $this->isInvalidCommandline();
    }

    /**
     * Parse error information.
     *
     * @return bool Whether there was a parse error or not
     */
    public function isInvalidCommandline(): bool
    {
        return $this->error;
    }

    /**
     * Wrap parsed command line arguments in a unified format.
     *
     * @param bool|string|list<bool>|list<string> $value Parsed command line argument
     *
     * @return list<bool>|list<string> Wrapped argument
     */
    protected function wrapArgument(bool|string|array $value): array
    {
        if ($value === FALSE)
        {
            return [];
        }

        if (is_array($value))
        {
            return $value;
        }

        return [ $value ];
    }

}

?>
