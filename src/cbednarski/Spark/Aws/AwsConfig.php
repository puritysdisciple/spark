<?php

namespace cbednarski\Spark\Aws;

use \cbednarski\Spark\DeployConfig;

class AwsConfig extends DeployConfig
{
    protected $key;
    protected $secret;
    protected $bucket;

    /**
     * @param $array
     * @return bool
     */
    protected function loadFromArray(Array $array)
    {
        foreach (array('key', 'secret', 'bucket') as $field) {
            if (!empty($array[$field])) {
                $method = 'set' . $field;
                $this->$method($array[$field]);
            } else {
                // If one of the configuration values is missing
                // then we'll return early to avoid a partial config
                // @TODO make this type of configuration problem
                // @TODO noisier so the user can fix it
                return false;
            }
        }

        return true;
    }

    public function loadNamedConfig($environment)
    {
        $env = $this->getDeployByName($environment);

        if (isset($env['aws'])) {
            return $this->loadFromArray($env['aws']);
        }

        return false;
    }

    public function loadFromEnvVars()
    {
        return $this->loadFromArray(array(
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
