<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitdc3888b9e866234953f70e7205b4cb12
{
    public static $prefixLengthsPsr4 = array (
        'B' => 
        array (
            'Bramus\\Ansi\\' => 12,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Bramus\\Ansi\\' => 
        array (
            0 => __DIR__ . '/..' . '/bramus/ansi-php/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitdc3888b9e866234953f70e7205b4cb12::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitdc3888b9e866234953f70e7205b4cb12::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
