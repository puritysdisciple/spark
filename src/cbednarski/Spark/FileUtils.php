<?php

namespace cbednarski\Spark;

class FileUtils
{
    public static function dirIsEmpty($path)
    {
        if (!is_dir($path)) {
            return false;
        }

        #  scandir will always return . and .. which we don't care about
        if (count(scandir($path)) > 2) {
            return false;
        }
        return true;
    }

    public static function mkdirIfNotExists($path, $mode = 0755, $recursive = true)
    {
        if (!file_exists($path)) {
            mkdir($path, $mode, true);
        }
    }

    public static function mkdirs($folders, $path = '')
    {
        foreach ($folders as $folder) {
            self::mkdirIfNotExists($path . $folder);
        }
    }

    public static function existsAndIsReadable($path)
    {
        return file_exists($path) && is_readable($path);
    }

    public static function listFilesInDir($dir, $recursive = true)
    {
        $files = array();

        foreach (scandir($dir) as $item) {
            if($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $item;

            if (is_file($path)) {
                $files[] = $path;
            } elseif (is_dir($path)) {
                if ($recursive) {
                    $files = array_merge(static::listFilesInDir($path, true));
                } else {
                    $files = $path;
                }
            }
        }

        return $files;
    }

    public static function concat($primary, $addend)
    {
        $new = false;
        if (!is_file($primary)) {
            touch($primary);
            $new = true;
        }

        $file = fopen($primary, 'a');
        $add = fopen($addend, 'r');

        if(!$new) {
            fwrite($file, PHP_EOL);
        }
        fwrite($file, fread($add, filesize($addend)));

        fclose($file);
        fclose($add);
    }

    // Thanks to http://stackoverflow.com/a/4490706/317916
    public static function recursiveDelete($path)
    {
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($it as $file) {
            if (in_array($file->getBasename(), array('.', '..'))) {
                continue;
            } elseif ($file->isDir()) {
                rmdir($file->getPathname());
            } elseif ($file->isFile() || $file->isLink()) {
                unlink($file->getPathname());
            }
        }

        rmdir($path);
    }
}