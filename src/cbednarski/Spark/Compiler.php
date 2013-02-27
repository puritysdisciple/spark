<?php

namespace cbednarski\Spark;

use cbednarski\Spark\FileUtils;

class Compiler
{
    private $cache;
    private $twig;
    private $loader;

    function __construct(Config $config)
    {
        $this->config = $config;

        $twig_paths = FileUtils::filterExists(array(
            $this->config->getFullPath('pages'),
            $this->config->getFullPath('layouts')
        ));

        $this->loader = new \Twig_Loader_Filesystem($twig_paths);

        $this->twig = new \Twig_Environment($this->loader, array(
            'auto_reload' => true,
            'autoescape' => false,
            'cache' => $this->cache,
            'debug' => false,
            'optimizations' => -1,
            'strict_variables' => false
        ));
    }

    public function compile($source, $target, $params = array())
    {
        $render = $this->twig->render($source, $params);
        file_put_contents($target, $render);
    }

    public function build()
    {
        foreach (FileUtils::listFilesInDir($this->config->getFullPath('pages')) as $file) {
            // Calculate target filename
            $target = self::getTargetFilename($this->config->pages, $this->config->target, $file);

            // Make sure parent folder for target exists
            $parent_dir = pathinfo($target, PATHINFO_DIRNAME);
            FileUtils::mkdirIfNotExists($parent_dir);

            // Compile or copy if it's not a template
            if(pathinfo($file, PATHINFO_EXTENSION) === 'twig') {
                $this->compile($file, $target);
            } else {
                copy($file, $target);
            }
        }
    }

    public function watch()
    {

    }

    public static function getTargetFilename($source, $target, $filename)
    {
        if(strstr($filename, $source) !== false) {
            $filename = substr($filename, strlen($source));
        }

        if(pathinfo($filename, PATHINFO_EXTENSION) === 'twig') {
            $filename = substr($filename, 0, strlen($filename) - 5);
        }

        return $target . $filename;
    }
}