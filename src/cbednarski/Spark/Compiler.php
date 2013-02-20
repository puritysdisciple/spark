<?php

namespace cbednarski\Spark;

class Compiler
{
    private $source;
    private $target;
    private $cache;
    private $twig;
    private $loader;

    function __construct($source, $target, $cache = null)
    {
        $this->cache = $cache;
        $this->source = $source;
        $this->target = $target;

        $this->loader = new \Twig_Loader_Filesystem($this->source);

        $this->twig = new \Twig_Environment($this->loader, array(
            'auto_reload' => true,
            'autoescape' => false,
            'cache' => $this->cache,
            'debug' => false,
            'optimizations' => -1,
            'strict_variables' => false
        ));
    }

    private function listFilesInDir($dir)
    {
        $files = array();

        foreach (scandir($dir) as $item) {

            if (is_file($item)) {
                $files[] = $item;
            } elseif (is_dir($item)) {
                $files[] = $this->listFilesInDir($item);
            }
        }

        return $files;
    }

    public function compile($source, $target)
    {
        $render = $this->twig->render($source);
        file_put_contents($target, $render);
    }

    public function build()
    {
        foreach ($this->listFilesInDir($this->source) as $file) {
            // Calculate target filename
            $target = self::getTargetFilename($this->source, $this->target, $file);

            // Make sure parent folder for target exists
            $parent_dir = pathinfo($target, PATHINFO_DIRNAME);
            if(!is_dir($parent_dir)) {
                mkdir($parent_dir, 0755, true);
            }

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