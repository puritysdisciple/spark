<?php

namespace cbednarski\Spark;

use Symfony\Component\Yaml\Yaml;

class DeployConfig
{
    protected $config;
    protected $raw = array();

    public function __construct(Config $config, $filename = 'spark-deploy.yml')
    {
        $this->config = $config;
        $this->readFile($filename);
    }

    protected function readFile($filename)
    {
        $path = FileUtils::softRealpath($this->config->getBasePath() . DIRECTORY_SEPARATOR . $filename);

        if (is_readable($path)) {
            $this->raw = Yaml::parse(file_get_contents($path));
        } else {
            throw new \RuntimeException('Unable to read from deployments file: ' . $path);
        }
    }

    public function getDeployments()
    {
        return array_keys($this->raw);
    }

    public function getDeployByName($name)
    {
        if (isset($this->raw[$name])) {
            return $this->raw[$name];
        }

        return false;
    }
}
