<?php

namespace cbednarski\Spark;

use Symfony\Component\Yaml\Yaml;

class Config
{
    private $data;

    public static function loadFile($path)
    {
        $data = Yaml::parse($path);
        return new static($data);
    }

    public function __construct($data = null)
    {
        $this->data = array_merge(static::defaultConfig(), $data);
    }

    public static function defaultConfig()
    {
        return array(
            'source' => 'src/',
            'target' => 'build/target/',
            'cache' => 'build/cache/',
            'locale' => 'locale/',
            'localize' => 'all'
        );
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