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
        FileUtils::mkdirs(array(
            'src/layouts',
            'src/pages',
            'src/assets',
            'build/target',
            'build/cache',
            'locale/en_US/LC_MESSAGES'
        ), $path);

        // Add .gitignore file
        FileUtils::concat($path . '.gitignore', __DIR__ . '/Resources/gitignore');

        // Add spark.yml
        copy(__DIR__ . '/Resources/spark.yml', $path . 'spark.yml');
    }
}