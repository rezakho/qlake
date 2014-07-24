<?php

namespace Framework\Support;

class Autoload
{
    protected static $aliases = [
        'App' => 'Framework\Iwan\App',
        'View' => 'Framework\Iwan\View',
        //'ClearException' => 'Framework\Exception\ClearException',
        'Route' => 'Framework\Iwan\Route',
        'Config' => 'Framework\Iwan\Config',
        'Cache' => 'Framework\Iwan\Cache',
        'DB' => 'Framework\Iwan\DB',
        'Html' => 'Framework\Iwan\HtmlBuilder',
    ];
    /**
     * Array of directories for searching classes.
     * 
     * @var array
     */
    protected static $directories = [];

    /**
     * Indicates if autoload function has registered.
     * 
     * @var bool
     */
    protected static $registered = false;

    /**
     * Search and Include a Class by class name like Framework\Router\Route. 
     * 
     * @param string $class Like Framework\Router\Route
     * @return bool
     */
    public static function load($class)
    {
        $file = static::normalizePath($class);

        if (file_exists($path = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.$file))
        {
            require $path;

            return true;
        }

        foreach (static::$directories as $directory)
        {
            if (file_exists($path = $directory.DIRECTORY_SEPARATOR.$file))
            {
                require $path;

                return true;
            }
        }

        if (static::$aliases[$class])
        {
            class_alias(static::$aliases[$class], $class);

            return true;
        }
        
        return false;
    }

    /**
     * Normalize Class name
     * 
     * @param string $class 
     * @return string
     */
    public static function normalizePath($class)
    {
        if ($class[0] == '\\') $class = substr($class, 1);

        return str_replace(['\\', '_'], DIRECTORY_SEPARATOR, $class).'.php';
        return $class . '.php';
    }

    /**
     * Register a SPL autoload function.
     * 
     * @return void
     */
    public static function register()
    {
        if ( ! static::$registered)
        {
            static::$registered = spl_autoload_register([__CLASS__, 'load']);
        }
    }

    /**
     * Add some directory to search scope for loading classes.
     * 
     * @param array $directories 
     * @return void
     */
    public static function addDirectories(array $directories)
    {
        static::$directories = array_merge(static::$directories, (array) $directories);

        static::$directories = array_unique(static::$directories);
    }

    /**
     * Remove directory from directory list. If $directories parameter be null, 
     * all directories will be removed.
     * 
     * @param string|null $directories 
     * @return void
     */
    public static function removeDirectories($directories = null)
    {
        if (is_null($directories))
        {
            static::$directories = [];
        }
        else
        {
            $directories = (array) $directories;

            static::$directories = array_filter(static::$directories, function($directory) use ($directories)
            {
                return ( ! in_array($directory, $directories));
            });
        }
    }

    /**
     * Get array of all directories.
     * 
     * @return array
     */
    public static function getDirectories()
    {
        return static::$directories;
    }

}
