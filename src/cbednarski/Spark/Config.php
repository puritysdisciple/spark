<?php

namespace cbednarski\Spark;

use Symfony\Component\Yaml\Yaml;

class Config
{
    private $data;
    private $base_path = null;

    /**
     * Load configuration data from the specified file
     *
     * @param string $path Read this config file
     * @return self
     * @throws \RuntimeException
     */
    public static function loadFile($path)
    {
        if (!realpath($path)) {
            throw new \RuntimeException('Unable to load configuration file from ' . $path);
        }

        $data = Yaml::parse($path);

        return new static(pathinfo($path, PATHINFO_DIRNAME), $data);
    }

    public function __construct($base_path, $data = array())
    {
        $this->base_path = $base_path;
        $default_config = static::defaultConfig();
        // @TODO replace this with a recursive array_merge that works correctly
        // with numeric-indexed arrays
        $this->data = array_merge($default_config, $data);
        $this->data['localization'] = array_merge($default_config['localization'], $this->data['localization']);
    }

    public static function defaultConfig()
    {
        return array(
            'pages' => 'pages/',
            'assets' => 'assets/',
            'layouts' => 'layouts/',
            'plugins' => 'plugins/',
            'target' => 'target/',
            'localization' => array(
                'path' => 'locale/',
                'format' => 'po',
                'localize' => 'all',
                'default' => 'en_US',
            ),
            'ignore' => array(),
        );
    }

    public function getBasePath()
    {
        return $this->base_path;
    }

    public function setBasePath($path)
    {
        $this->base_path = $path;
    }

    public function getData()
    {
        return $this->data;
    }

    public function __get($variable)
    {
        if (array_key_exists($variable, $this->data)) {
            return $this->data[$variable];
        } else {
            return null;
        }
    }

    public function __set($variable, $value)
    {
        $this->data[$variable] = $value;
    }

    public function getFullPath($path)
    {
        if ($this->getBasePath() === null) {
            throw new \LogicException('Cannot call getFullPath if base path is null');
        }

        return $this->getBasePath() . DIRECTORY_SEPARATOR . rtrim($path, '\\/');
    }

    public function getLocalePath()
    {
        return $this->getFullPath($this->localization['path']);
    }

    public function getTargetPath()
    {
        return $this->getFullPath($this->target);
    }

    /**
     * Given a locale string, returns the FQ path under target where the
     * compiled templates should be placed. For example:
     *
     * en_US -> .../target/en_US   BY DEFAULT, OR
     * en_US -> .../target/en      IF CONFIGURED IN localization -> localize
     *
     * If `localize` is 'all' then paths will be the locale string verbatim. If
     * `localize` is specified and the locale is not present in the list, this
     * function returns false (because that's how a user says "don't build
     * this language right now"). Otherwise, we return whatever is configured
     * in `localize` for the locale that's specified.
     *
     * @param string $locale
     * @return bool|string
     */
    public function getTargetPathForLocale($locale)
    {
        if ($this->localization['localize'] === 'all') {
            return $this->getTargetPath() . DIRECTORY_SEPARATOR . $locale;
        } else {
            if (isset($this->localization['localize'][$locale])) {
                $loc_bit = $this->localization['localize'][$locale];
            } else {
                return false;
            }

            return $this->getTargetPath() . DIRECTORY_SEPARATOR . $loc_bit;
        }
    }

    public function getPagePath()
    {
        return $this->getFullPath($this->pages);
    }

    public function getAssetPath()
    {
        return $this->getFullPath($this->assets);
    }

    public function getPluginPath()
    {
        return $this->getFullPath($this->plugins);
    }

    public function getLayoutPath()
    {
        return $this->getFullPath($this->layouts);
    }

    public function getLocaleFormat()
    {
        return $this->localization['format'];
    }

    public function getIgnoredPaths()
    {
        return $this->ignore;
    }

    public function getDefaultLocale()
    {
        return $this->localization['default'];
    }

}
