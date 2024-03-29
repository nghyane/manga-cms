<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitdfa6bdffffc890a8699e0739fa35995b
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInitdfa6bdffffc890a8699e0739fa35995b', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitdfa6bdffffc890a8699e0739fa35995b', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitdfa6bdffffc890a8699e0739fa35995b::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
