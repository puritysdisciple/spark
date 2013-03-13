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
                $config->getLayoutPath(),
                $config->getPagePath(),
                $config->getPluginPath(),
                $config->getAssetPath(),
                $config->getAssetPath() . '/css/',
                $config->getTargetPath(),
                $config->getLocalePath() . '/en_US/',
                $config->getLocalePath() . '/fr_FR/',
            )
        );

        // Append some stuff to .gitignore (or create it if it's missing)
        FileUtils::concat($path . '.gitignore', __DIR__ . '/Resources/gitignore');

        // Add some starter files
        copy(__DIR__ . '/Resources/main.css', $config->getAssetPath() . '/css/main.css');
        copy(__DIR__ . '/Resources/layout.html.twig', $config->getLayoutPath() . '/layout.html.twig');
        copy(__DIR__ . '/Resources/index.html.twig', $config->getPagePath() . '/index.html.twig');
        copy(__DIR__ . '/Resources/en_US.po', $config->getLocalePath() . '/en_US/messages.po');
        copy(__DIR__ . '/Resources/fr_FR.po', $config->getLocalePath() . '/fr_FR/messages.po');
        copy(__DIR__ . '/Resources/sample_plugin.php', $config->getPluginPath() . '/sample_plugin.php');

        return $config;
    }

    public static function getAvailableLocales(Config $config)
    {
        $locales = array();
        $iterator = new \DirectoryIterator($config->getFullPath('locale'));

        foreach ($iterator as $locale) {
            if (!in_array($locale->getFilename(), array('.', '..'))) {
                $locales[] = $locale->getFilename();
            }
        }

        sort($locales);

        return $locales;
    }

    public static function getActiveLocales(Config $config)
    {
        // Technically we could optimize the runtime for the 'all' case but it's a lot
        // more code and the performance trade off is negligible so I'm going to skip it.
        return array_values(array_filter(
            self::getAvailableLocales($config),
            function ($locale) use ($config) {
                return $config->localization['localize'] === 'all' OR in_array($locale, array_keys($config->localization['localize'])) OR in_array($locale, $config->localization['localize']);
            }
        ));
    }
}
