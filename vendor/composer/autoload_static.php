<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit3e885bccb5fb065ec12bee9a7e24a76a
{
    public static $prefixLengthsPsr4 = array (
        'D' => 
        array (
            'Damavand\\TorkamanBooksGallery\\' => 30,
        ),
        'C' => 
        array (
            'Carbon_Fields\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Damavand\\TorkamanBooksGallery\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'Carbon_Fields\\' => 
        array (
            0 => __DIR__ . '/..' . '/htmlburger/carbon-fields/core',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit3e885bccb5fb065ec12bee9a7e24a76a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit3e885bccb5fb065ec12bee9a7e24a76a::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit3e885bccb5fb065ec12bee9a7e24a76a::$classMap;

        }, null, ClassLoader::class);
    }
}
