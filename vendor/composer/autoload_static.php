<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1c5cc9ea25261fbf6e1f9ae4e4c3c6d5
{
    public static $prefixLengthsPsr4 = array (
        'B' => 
        array (
            'Badcow\\DNS\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Badcow\\DNS\\' => 
        array (
            0 => __DIR__ . '/..' . '/badcow/dns/lib',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit1c5cc9ea25261fbf6e1f9ae4e4c3c6d5::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit1c5cc9ea25261fbf6e1f9ae4e4c3c6d5::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit1c5cc9ea25261fbf6e1f9ae4e4c3c6d5::$classMap;

        }, null, ClassLoader::class);
    }
}