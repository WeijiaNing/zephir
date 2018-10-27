<?php

/**
 * This file is part of the Zephir.
 *
 * (c) Zephir Team <team@zephir-lang.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zephir;

/**
 * Checks if currently running under MS Windows.
 *
 * @return bool
 */
function is_windows()
{
    return 'WIN' === \strtoupper(\substr(PHP_OS, 0, 3));
}

/**
 * Checks if currently running under macOs.
 *
 * @return bool
 */
function is_macos()
{
    return 'DARWIN' === \strtoupper(\substr(PHP_OS, 0, 6));
}

/**
 * Checks if currently running under BSD based OS.
 *
 * @link   https://en.wikipedia.org/wiki/List_of_BSD_operating_systems
 * @return bool
 */
function is_bsd()
{
    return false !== \stristr(\strtolower(PHP_OS), 'bsd');
}

/**
 * Attempts to remove recursively the directory with all subdirectories and files.
 *
 * A E_WARNING level error will be generated on failure.
 *
 * @param  string $path
 * @return void
 */
function unlink_recursive($path)
{
    if (\is_dir($path)) {
        $objects = \array_diff(\scandir($path), ['.', '..']);

        foreach ($objects as $object) {
            if (\is_dir("{$path}/{$object}")) {
                unlink_recursive("{$path}/{$object}");
            } else {
                \unlink("{$path}/{$object}");
            }
        }

        \rmdir($path);
    }
}

/**
 * Camelize a string.
 *
 * @param  string $string
 * @return string
 */
function camelize($string)
{
    return \str_replace(' ', '', \ucwords(\str_replace('_', ' ', $string)));
}


/**
 * Prepares a class name to be used as a C-string.
 *
 * @param  string $className
 * @return string
 */
function escape_class($className)
{
    return \str_replace('\\', '\\\\', $className);
}

/**
 * Prepares a string to be used as a C-string.
 *
 * @param  string $string
 * @return string
 */
function add_slashes($string)
{
    $newstr = "";
    $after = null;
    $length = \strlen($string);

    for ($i = 0; $i < $length; $i++) {
        $ch = \substr($string, $i, 1);
        if ($i != ($length -1)) {
            $after = \substr($string, $i + 1, 1);
        } else {
            $after = null;
        }

        switch ($ch) {
            case '"':
                $newstr .= "\\" . '"';
                break;
            case "\n":
                $newstr .= "\\" . 'n';
                break;
            case "\t":
                $newstr .= "\\" . 't';
                break;
            case "\r":
                $newstr .= "\\" . 'r';
                break;
            case "\v":
                $newstr .= "\\" . 'v';
                break;
            case '\\':
                switch ($after) {
                    case "n":
                    case "v":
                    case "t":
                    case "r":
                    case '"':
                    case "\\":
                        $newstr .= $ch . $after;
                        $i++;
                        break;
                    default:
                        $newstr .= "\\\\";
                        break;
                }
                break;
            default:
                $newstr .= $ch;
        }
    }
    return $newstr;
}

/**
 * Checks if current PHP is thread safe.
 *
 * @return bool
 */
function is_zts()
{
    if (\defined('PHP_ZTS') && PHP_ZTS == 1) {
        return true;
    }

    \ob_start();
    \phpinfo(INFO_GENERAL);

    return (bool) \preg_match('/Thread\s*Safety\s*enabled/i', \strip_tags(\ob_get_clean()));
}

/**
 * Resolves Windows release folder.
 *
 * @return string
 */
function windows_release_dir()
{
    if (is_zts()) {
        if (PHP_INT_SIZE === 4) {
            // 32-bit version of PHP
            return "ext\\Release_TS";
        } elseif (PHP_INT_SIZE === 8) {
            // 64-bit version of PHP
            return "ext\\x64\\Release_TS";
        } else {
            // fallback
            return "ext\\Release_TS";
        }
    } else {
        if (PHP_INT_SIZE === 4) {
            // 32-bit version of PHP
            return "ext\\Release";
        } elseif (PHP_INT_SIZE === 8) {
            // 64-bit version of PHP
            return "ext\\x64\\Release";
        } else {
            // fallback
            return "ext\\Release";
        }
    }
}