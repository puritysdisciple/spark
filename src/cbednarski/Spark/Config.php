<?php

namespace cbednarski\Spark;

use Symfony\Component\Yaml\Yaml;

class Config
{
    private $data;
    private $base_path = null;

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

    public function getLocaleFormat()
    {
        return $this->localization['format'];
    }

}
