<?php

/**
 * This file contains another implementation of a
 * command line argument parser, like getopt.
 *
 * SPDX-FileCopyrightText: Copyright 2009 Heinz Wiesinger, Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2010 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Shadow;

use UnexpectedValueException;

/**
 * Getopt like command line argument parser. However, it
 * does a few things different from getopt. While getopt
 * only allows one argument per command line option, this
 * class allows more than one argument, as well as optional
 * and obligatory arguments mixed
 */
class LunrCliParser implements CliParserInterface
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
     * The arguments passed on command line
     * @var string[]
     */
    private $args;

    /**
     * Checked/Processed arguments
     * @var string[]
     */
    private $checked;

    /**
     * "Abstract Syntax Tree" of the passed arguments
     * @var array<string,mixed[]>
     */
    private $ast;

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
        $this->short   = $shortopts;
        $this->long    = $longopts;
        $this->args    = [];
        $this->checked = [];
        $this->ast     = [];
        $this->error   = FALSE;
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        unset($this->short);
        unset($this->long);
        unset($this->args);
        unset($this->checked);
        unset($this->ast);
        unset($this->error);
    }

    /**
     * Parse command line parameters.
     *
     * @return array<string,mixed[]> Array of parameters and their arguments
     */
    public function parse(): array
    {
        if (!is_array($_SERVER['argv']))
        {
            throw new UnexpectedValueException('Command line arguments are not stored in an array!');
        }

        if (!array_is_list($_SERVER['argv']))
        {
            throw new UnexpectedValueException('Command line arguments are not stored as a list!');
        }

        foreach ($_SERVER['argv'] as $index => $arg)
        {
            if (!is_string($arg))
            {
                throw new UnexpectedValueException('Command line argument ' . $index . ' is not a string!');
            }

            $this->args[$index] = $arg;
        }

        foreach ($this->args as $index => $arg)
        {
            if (!in_array($arg, $this->checked) && $index != 0)
            {
                $this->is_opt($arg, $index, TRUE);
            }
        }

        return $this->ast;
    }

    /**
     * Check whether the parsed command line was valid or not.
     *
     * @return bool TRUE if the command line was invalid, FALSE otherwise
     */
    public function is_invalid_commandline(): bool
    {
        return $this->error;
    }

    /**
     * Check for command line arguments.
     *
     * @param string $opt      The command line argument
     * @param int    $index    The index of the argument within $this->args
     * @param bool   $toplevel Whether we run it from the top or from
     *                         further down in the stack
     *
     * @return bool Success or Failure
     */
    private function is_opt(string $opt, int $index, bool $toplevel = FALSE): bool
    {
        array_push($this->checked, $opt);

        if (isset($opt[0]) && $opt[0] == '-')
        {
            if (strlen($opt) < 1)
            {
                return $this->is_valid_short($opt, $index);
            }

            $param = substr($opt, 1);

            if (isset($param[0]) && $param[0] != '-')
            {
                return $this->is_valid_short($param, $index);
            }

            if (strlen($param) > 1)
            {
                return $this->is_valid_long(substr($param, 1), $index);
            }
            else
            {
                return $this->is_valid_long($opt, $index);
            }
        }
        elseif ($toplevel)
        {
            trigger_error('Superfluous argument: ' . $opt, E_USER_NOTICE);
        }

        return FALSE;
    }

    /**
     * Check whether the given argument is a valid short option.
     *
     * @param string $opt   The command line argument
     * @param int    $index The index of the argument within $this->args
     *
     * @return bool Success or Failure
     */
    private function is_valid_short(string $opt, int $index): bool
    {
        $pos = strpos($this->short, $opt);

        if ($pos === FALSE)
        {
            trigger_error('Invalid parameter given: ' . $opt, E_USER_WARNING);
            $this->error = TRUE;
            return FALSE;
        }

        $this->ast[$opt] = [];

        return $this->check_argument($opt, $index, $pos, $this->short);
    }

    /**
     * Check whether the given argument is a valid long option.
     *
     * @param string $opt   The command line argument
     * @param int    $index The index of the argument within $this->args
     *
     * @return bool Success or Failure
     */
    private function is_valid_long(string $opt, int $index): bool
    {
        $match = FALSE;
        $args  = '';

        foreach ($this->long as $key => $arg)
        {
            if ($opt == substr($arg, 0, strlen($opt)))
            {
                if (strlen($arg) == strlen($opt))
                {
                    $match = TRUE;
                    $args  = $key;
                }
                elseif ($arg[strlen($opt)] == ':' || $arg[strlen($opt)] == ';')
                {
                    $match = TRUE;
                    $args  = $key;
                }
            }
        }

        if ($match === FALSE)
        {
            trigger_error('Invalid parameter given: ' . $opt, E_USER_WARNING);
            $this->error = TRUE;
            return FALSE;
        }

        $this->ast[$opt] = [];

        return $this->check_argument($opt, $index, strlen($opt) - 1, $this->long[$args]);
    }

    /**
     * Check whether the given string is a valid argument.
     *
     * @param string $opt   The command line argument
     * @param int    $index The index of the argument within $this->args
     * @param int    $pos   Index of the last option character within the
     *                      longopts or shortopts String
     * @param string $a     The option the argument belongs too
     *
     * @return bool Success or Failure
     */
    private function check_argument(string $opt, int $index, int $pos, string $a): bool
    {
        $next = $index + 1;

        if ($pos + 1 < strlen($a))
        {
            if (!in_array($a[$pos + 1], [ ':', ';' ]))
            {
                return FALSE;
            }

            $type = $a[$pos + 1] == ':' ? ':' : ';';

            if (count($this->args) > $next && strlen($this->args[$next]) != 0)
            {
                if (!$this->is_opt($this->args[$next], $next) && $this->args[$next][0] != '-')
                {
                    array_push($this->ast[$opt], $this->args[$next]);

                    if ($pos + 2 >= strlen($a))
                    {
                        return TRUE;
                    }

                    if (($type == ':' && $a[$pos + 2] == ':') || $a[$pos + 2] == ';')
                    {
                        return $this->check_argument($opt, $next, $pos + 1, $a);
                    }
                    else
                    {
                        return TRUE;
                    }
                }
                elseif ($type == ':')
                {
                    trigger_error('Missing argument for -' . $opt, E_USER_WARNING);
                    $this->error = TRUE;
                }
            }
            elseif ($type == ':')
            {
                trigger_error('Missing argument for -' . $opt, E_USER_WARNING);
                $this->error = TRUE;
            }
        }
        elseif (count($this->args) > $next && !strpos($a, $opt))
        {
            trigger_error('Superfluous argument: ' . $this->args[$next], E_USER_NOTICE);
            return TRUE;
        }

        return FALSE;
    }

}

?>
