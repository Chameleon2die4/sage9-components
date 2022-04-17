<?php /** @noinspection PhpUnused */

namespace Chameleon2die4\Components;

class Utils
{
    /**
     * Is File PHP
     *
     * Determine if the file is a PHP file
     * @return boolean
     */
    public static function isFilePhp(string $filename)
    {
        return (pathinfo($filename, PATHINFO_EXTENSION) == 'php');
    }

    /**
     * Does File Contain
     *
     * Determine if the file contains a string
     * @return boolean
     */
    public static function doesFileContain(string $filename, string $str)
    {
        return strpos(file_get_contents($filename), $str) !== false;
    }

    /**
     * Is Array Indexed
     *
     * Determine if the array is indexed
     * @return boolean
     */
    public static function isArrayIndexed(array $array)
    {
        return array_keys($array) === range(0, count($array) - 1);
    }

    /**
     * Does String Contain Markup
     *
     * Determine if the string contains markup
     * @return boolean
     */
    public static function doesStringContainMarkup(string $str)
    {
        return (is_string($str) && $str !== strip_tags($str));
    }

    /**
     * Convert To Snake Case
     *
     * Convert camel case to snake case for data variables
     * @return string
     */
    public static function convertToSnakeCase(string $str)
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $str));
    }

    /**
     * Convert To Kebab Case
     *
     * Convert camel case to kebab case for templates
     * @return string
     */
    public static function convertToKebabCase(string $str)
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $str));
    }

    /**
     * Convert To Snake Case
     *
     * Converts kebab case to snake case for data variables
     * @return string
     */
    public static function convertKebabCaseToSnakeCase(string $str)
    {
        return strtolower(str_replace('-', '_', $str));
    }

    public static function convertToPascalCase(string $str) {
        $words = preg_split('/[-_]/', $str);
        $words = array_map('ucfirst', $words);

        return implode('', $words);
    }

}