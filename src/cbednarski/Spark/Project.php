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
                $config->plugins,
                $config->assets,
                $config->assets . '/css/',
                $config->target,
                $config->locale . '/en_US/LC_MESSAGES',
                $config->locale . '/fr_FR/LC_MESSAGES',
            ),
            $path
        );

        // Append some stuff to .gitignore (or create it if it's missing)
        FileUtils::concat($path . '.gitignore', __DIR__ . '/Resources/gitignore');

        // Add some starter files
        copy(__DIR__ . '/Resources/main.css', $path . '/' . $config->assets . 'css/main.css');
        copy(__DIR__ . '/Resources/layout.html.twig', $path . '/' . $config->layouts . 'layout.html.twig');
        copy(__DIR__ . '/Resources/index.html.twig', $path . '/' . $config->pages . 'index.html.twig');
        copy(__DIR__ . '/Resources/sample_plugin.php', $path . '/' . $config->plugins . 'sample_plugin.php');
        copy(__DIR__ . '/Resources/en_US.po', $path . '/' . $config->locale . 'en_US/LC_MESSAGES/messages.po');
        copy(__DIR__ . '/Resources/fr_FR.po', $path . '/' . $config->locale . 'fr_FR/LC_MESSAGES/messages.po');

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
                return $config->localize === 'all' OR in_array($locale, array_keys($config->localize)) OR in_array($locale, $config->localize);
            }
        ));
    }
}
