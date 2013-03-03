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
        $this->data = array_merge(static::defaultConfig(), $data);
    }

    public static function defaultConfig()
    {
        return array(
            'pages' => 'src/pages/',
            'assets' => 'src/assets/',
            'layouts' => 'src/layouts/',
            'target' => 'build/target/',
            'cache' => 'build/cache/',
            'locale' => 'locale/',
            'localize' => 'all'
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

    public function getFullPath($variable)
    {
        if (array_key_exists($variable, $this->data)) {
            return $this->getBasePath() . DIRECTORY_SEPARATOR . $this->data[$variable];
        } else {
            return null;
        }
    }
}
