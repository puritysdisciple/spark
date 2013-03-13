<?php
namespace cbednarski\Spark;

class WatchfulFilesystem extends \Twig_Loader_Filesystem
{
    public function getCacheKey($name)
    {
        return $this->findTemplate($name) . filemtime($this->findTemplate($name));
    }

    public function isFresh($name, $time)
    {
        return false;
    }
}
