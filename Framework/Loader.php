<?php
/**
 * File Description
 *
 * PHP version 5
 *
 * @category   Framework
 * @package    Framework
 * @subpackage Loader
 * @author     Serhii Tsybulniak
 * @copyright  2011-2015 mindk (http://mindk.com). All rights reserved.
 * @license    http://mindk.com Commercial
 * @link       http://mindk.com
 */

/**
 * Class Description
 *
 * @category   Framework
 * @package    Framework
 * @subpackage Loader
 * @author     Serhii Tsybulniak
 * @copyright  2011-2015 mindk (http://mindk.com). All rights reserved.
 * @license    http://mindk.com
 * @link       http://mindk.com
 */
class Loader
{

    /**
     * An associative array where the key is a namespace prefix and the value
     * is an array of base directories for classes in that namespace.
     *
     * @var array
     */
    protected static $prefixes = array();

    /**
     * Add path to the base directory for a namespace prefix.
     *
     * @param string    $prefix    Namespace prefix
     * @param string    $base_dir  Path to the directory for classes of that namespace
     *
     * return void
     */
    public static function addNamespacePath($prefix, $base_dir)
    {
        $prefix = trim($prefix, '\\').'\\';

        $base_dir = rtrim($base_dir, DIRECTORY_SEPARATOR).'/';

        if (isset(self::$prefixes[$prefix]) === false) {
            self::$prefixes[$prefix] = array();
        }

        array_push(self::$prefixes[$prefix], $base_dir);
    }

    /**
     * Register loader with SPL autoloader stack.
     *
     * return void
     */
    public static function register()
    {
        spl_autoload_register(array('Loader', 'loadClass'));
    }


    /**
     * Load the class file for a given class name.
     *
     * @param   string      $class      Namespace of required class
     *
     * @return string/bool The mapped file name on success or boolean false on failure.
     */
    public static function loadClass($class)
    {
        $prefix = $class;

        while (false !== $pos = strrpos($prefix, '\\')) {

            $prefix = substr($class, 0, $pos + 1);

            $relative_class = substr($class, $pos + 1);

            $mapped_file = self::loadMappedFile($prefix, $relative_class);

            if ($mapped_file) {
                return $mapped_file;
            }

            $prefix = rtrim($prefix, '\\');
        }

        return false;
    }

    /**
     * Load the mapped file for a namespace prefix and relative class.
     *
     * @param $prefix The namespace prefix
     * @param $relative_class The relative class name
     *
     * @return bool|string Boolean false if no mapped file can be loaded, or String the
     * name of the mapped file that was loaded.
     */
    protected static function loadMappedFile($prefix, $relative_class)
    {
        if (isset(self::$prefixes[$prefix]) === false) {
            return false;
        }

        foreach (self::$prefixes[$prefix] as $base_dir) {

            $file = $base_dir
                .str_replace('\\', '/', $relative_class)
                .'.php';

            if (self::requireFile($file)) {
                return $file;
            }
        }

        return false;
    }

    /**
     * If file exists, require it.
     *
     * @param string $file The file to require
     *
     * @return bool True if the file exists, false if not.
     */
    protected static function requireFile($file)
    {
        if (file_exists($file)) {
            require $file;
            return true;
        }
        return false;
    }

}