<?php

namespace Joseki\LeanMapper;

class Utils
{

    /**
     * camelCase -> camel_case
     * @param  string
     * @return string
     */
    public static function camelToUnderscore($s)
    {
        $s = preg_replace('#(.)(?=[A-Z])#', '$1_', $s);
        $s = strtolower($s);
        $s = rawurlencode($s);
        return $s;
    }



    /**
     * camel_case -> camelCase
     * @param  string
     * @return string
     */
    public static function underscoreToCamel($s)
    {
        $s = strtolower($s);
        $s = preg_replace('#_(?=[a-z])#', ' ', $s);
        $s = substr(ucwords('x' . $s), 1);
        $s = str_replace(' ', '', $s);
        return $s;
    }



    /**
     * Trim database schema from table
     * schema.table => table
     *
     * @param string $table
     * @return string
     */
    public static function trimTableSchema($table)
    {
        $parts = explode('.', $table);
        $table = array_pop($parts);

        return $table;
    }



    /**
     * Trims namespace part from fully qualified class name
     * Handles table prefixes from extended namespaces
     * App\Entity\User => User
     *
     * @param $class
     * @return string
     */
    public static function trimNamespace($class)
    {
        $class = ltrim($class, '\\');
        $namespaces = explode('\\', $class);
        return end($namespaces);
    }



    /**
     * Returns namespace of a given class
     * App\Entity\User => App\Entity
     *
     * @param $class
     * @return string
     */
    public static function extractNamespace($class)
    {
        $class = ltrim($class, '\\');
        $namespaces = explode('\\', $class);
        array_pop($namespaces);
        return implode('\\', $namespaces);
    }
}
