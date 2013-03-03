<?php

namespace cbednarski\Spark;

class Project
{
    public static function init($directory)
    {
        if (!$directory) {
            throw new \InvalidArgumentException('Directory is required');
        }

        FileUtils::mkdirIfNotExists($directory);

        $path = realpath($directory) . '/';

        $yml_path = $path . 'spark.yml';
        copy(__DIR__ . '/Resources/spark.yml', $yml_path);
        $config = Config::loadFile($yml_path);

        // Create folders
        FileUtils::mkdirs(
            array(
                $config->layouts,
                $config->pages,
                $config->assets,
                $config->assets . '/css/',
                $config->target,
                $config->locale . '/en_US/LC_MESSAGES'
            ),
            $path
        );

        // Append some stuff to .gitignore (or create it if it's missing)
        FileUtils::concat($path . '.gitignore', __DIR__ . '/Resources/gitignore');

        // Add some starter files
        copy(__DIR__ . '/Resources/main.css', $path . '/' . $config->assets . 'css/main.css');
        copy(__DIR__ . '/Resources/layout.html.twig', $path . '/' . $config->layouts . 'layout.html.twig');
        copy(__DIR__ . '/Resources/index.html.twig', $path . '/' . $config->pages . 'index.html.twig');
    }
}
