<?php

namespace cbednarski\Spark;

use Symfony\Component\Yaml\Yaml;

class Config
{
    private $data;
    private $base_path = null;

    public static function loadFile($path)
    {
        $data = Yaml::parse($path);
        $config = new static($data);
        $config->base_path = pathinfo($path, PATHINFO_DIRNAME);
        return $config;
    }

    public function __construct($data = array())
    {
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
}