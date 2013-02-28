<?php

namespace cbednarski\Spark;

class Project
{
    public static function init($directory)
    {
        if (!$directory) {
            $directory = getcwd();
        }

        FileUtils::mkdirIfNotExists($directory);

        $path = realpath($directory) . '/';

        // Create folders
        FileUtils::mkdirs(
            array(
                'src/layouts',
                'src/pages',
                'src/assets',
                'build/target',
                'build/cache',
                'locale/en_US/LC_MESSAGES'
            ),
            $path
        );

        // Append some stuff to .gitignore (or create it if it's missing)
        FileUtils::concat($path . '.gitignore', __DIR__ . '/Resources/gitignore');

        // Add some starter files
        copy(__DIR__ . '/Resources/spark.yml', $path . 'spark.yml');
        copy(__DIR__ . '/Resources/layout.html.twig', $path . '/src/layouts/layout.html.twig');
        copy(__DIR__ . '/Resources/index.html.twig', $path . '/src/pages/index.html.twig');
    }
}
