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
            mkdir($path, $mode, $recursive);
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

    public static function listFilesInDir($path)
    {
        $files = array();

        if (!file_exists($path)) {
            // Even if the folder is missing we'll return an empty array so the API
            // is consistent and we can foreach without checking the type.
            return $files;
        }

        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($it as $file) {
            if (in_array($file->getBasename(), array('.', '..'))) {
                continue;
            } elseif ($file->isFile()) {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    public static function concat($target, $append)
    {
        $new = false;
        if (!is_file($target)) {
            touch($target);
            $new = true;
        }

        $file = fopen($target, 'a');
        $add = fopen($append, 'r');

        if (!$new) {
            fwrite($file, PHP_EOL);
        }
        fwrite($file, fread($add, filesize($append)));

        fclose($file);
        fclose($add);
    }

    /**
     * Recursive delete
     *
     * Thanks to http://stackoverflow.com/a/4490706/317916
     *
     * @param  string $path Delete everything under this path
     * @return int    Number of files deleted
     */
    public static function recursiveDelete($path)
    {
        $counter = 0;

        if (!file_exists($path)) {
            return $counter;
        }

        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($it as $file) {
            if (in_array($file->getBasename(), array('.', '..'))) {
                continue;
            } elseif ($file->isDir()) {
                // Directories don't hold data so we won't count these
                rmdir($file->getPathname());
            } elseif ($file->isFile() || $file->isLink()) {
                if (unlink($file->getPathname())) {
                    $counter++;
                }
            }
        }

        rmdir($path);

        return $counter;
    }

    public static function pathDiff($outer, $inner, $suppress_leading_slash = false)
    {
        $outer_len = strlen($outer);

        if (substr($inner, 0, $outer_len) === $outer) {
            $diff = substr($inner, $outer_len);

            if ($suppress_leading_slash) {
                $diff = ltrim($diff, '/\\');
            }

            return $diff;
        }

        return '';
    }

    public static function filterExists($paths)
    {
        $temp = array_filter($paths, function($path) {
            return file_exists($path);
        });

        sort($temp);

        return $temp;
    }

    public static function removeTwigExtension($filename)
    {
        if (pathinfo($filename, PATHINFO_EXTENSION) === 'twig') {
            $filename = substr($filename, 0, strlen($filename) - 5);

            # If there is no extension remaining after we remove .twig we'll
            # default to .html
            if (pathinfo($filename, PATHINFO_EXTENSION) === '') {
                $filename = $filename . '.html';
            }
        }

        return $filename;
    }

    /**
     * Check the filename against one or more regexps to see whether it matches
     *
     * @param string $filename
     * @param string|array $matches
     * @return bool
     */
    public static function matchFilename($filename, $matches)
    {
        if (!is_array($matches)) {
            $matches = array($matches);
        }

        foreach ($matches as $regexp) {
            if($regexp === null) {
                continue;
            }

            if (preg_match("#$regexp#", $filename)) {
                return true;
            }
        }

        return false;
    }
}
