<?php

namespace cbednarski\Spark\Aws;

use \Symfony\Component\Yaml\Yaml;

class AwsConfig
{
    protected $key;
    protected $secret;
    protected $bucket;

    /**
     * @param $array
     * @return bool|AwsConfig
     */
    public static function createFromArray($array)
    {
        $aws = new self;

        foreach (array('key', 'secret', 'bucket') as $field) {
            if (!empty($array[$field])) {
                $method = 'set' . $field;
                $aws->$method($array[$field]);
            } else {
                // If one of the configuration values is missing
                // then we'll return early to avoid a partial config
                // @TODO make this type of configuration problem
                // @TODO noisier so the user can fix it
                return false;
            }
        }

        return $aws;
    }

    public static function loadFromFile($path, $environment)
    {
        if (is_readable($path)) {
            $obj = Yaml::parse(file_get_contents($path));

            if (!empty($obj[$environment]['aws'])) {
                return static::createFromArray($obj[$environment]['aws']);
            }
        }

        return false;
    }

    public static function loadFromEnv()
    {
        return self::createFromArray(array(
                'key' => getenv('SPARK_AWS_KEY'),
                'secret' => getenv('SPARK_AWS_SECRET'),
                'bucket' => getenv('SPARK_AWS_BUCKET')
            ));
    }

    /**
     * @param mixed $bucket
     */
    public function setBucket($bucket)
    {
        $this->bucket = $bucket;
    }

    /**
     * @return mixed
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * @param mixed $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param mixed $secret
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;
    }

    /**
     * @return mixed
     */
    public function getSecret()
    {
        return $this->secret;
    }


}