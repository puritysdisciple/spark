<?php

namespace cbednarski\Spark;

/**
 * Class FilesystemWatcher
 *
 * http://www.php.net/manual/en/ref.inotify.php
 *
 * @package cbednarski\Spark
 */
class FilesystemWatcher
{
    private $watcher = false;
    private $watchlist = array();

    public function __construct()
    {
        if (function_exists('\inotify_init')) {
            $this->watcher = \inotify_init();
        }
    }

    public function __destruct()
    {

    }

    public function add($file)
    {
        if (!in_array($file, array_keys($this->watchlist))) {
            $this->watchlist[$file] = inotify_add_watch();
        }
    }

    public function remove($file)
    {
        if ($this->watcher) {
            \inotify_rm_watch($this->watcher);
        }
    }

    public function removeAll()
    {
        foreach ($this->watchlist as $file) {
            $this->remove($file);
        }
    }

    public function registerCallback(\Closure $callback)
    {

    }

    public function watch()
    {

    }
}