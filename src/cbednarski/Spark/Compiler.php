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
        $page_path = $this->config->getFullPath('pages');
        
        foreach (FileUtils::listFilesInDir($page_path) as $file) {
            // Calculate target filename
            $filename = FileUtils::pathDiff($page_path, $file, true);
            $target = $this->config->getFullPath('target') . $filename;

            if(pathinfo($target, PATHINFO_EXTENSION) === 'twig') {
                $target = substr($target, 0, strlen($target)-5);
            }

            // Make sure parent folder for target exists
            $parent_dir = pathinfo($target, PATHINFO_DIRNAME);
            FileUtils::mkdirIfNotExists($parent_dir);

            // Compile or copy if it's not a template
            if(pathinfo($file, PATHINFO_EXTENSION) === 'twig') {
                $this->compile($filename, $target);
            } else {
                copy($file, $target);
            }
        }
    }

    public function watch()
    {

    }
}