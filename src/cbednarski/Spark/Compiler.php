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

    private function listTwigFilesInDir($dir)
    {
        $files = array();

        foreach (scandir($dir) as $item) {

            if (is_file($item)) {
                $files[] = $item;
            } elseif (is_dir($item)) {
                $files[] = $this->listTwigFilesInDir($item);
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

    }

    public function watch()
    {

    }

    public function getTargetFilename($source, $target, $filename)
    {

    }

    public function pathDiff($path1, $path2)
    {

    }
}