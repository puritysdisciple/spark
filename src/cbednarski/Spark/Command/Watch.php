<?php

namespace cbednarski\Spark\Command;

use cbednarski\Spark\Config;
use cbednarski\Spark\Compiler;
use cbednarski\Spark\FileUtils;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Watch extends Command
{
    protected $compiler;
    protected $config;
    protected $last_files;

    protected function configure()
    {
        $this->setName('watch');
        $this->addArgument(
            'directory',
            InputArgument::OPTIONAL,
            'spark project folder (defaults to the current directory if not specified)'
        );
    }

    protected function watchPages ($dir, $alias)
    {
        if (!array_key_exists($alias, $this->last_files)) {
            $this->last_files[$alias] = array();
        }

        // Get the updated list of files
        $current_files = FileUtils::getFileModifyTimes($dir);

        // Compare our arrays and determine what has changed
        $modified = array_diff_assoc($current_files, $this->last_files[$alias]);
        $deleted = array_diff_key($this->last_files[$alias], $current_files);

        // Re-render any updated or new files
        foreach ($modified as $file => $mtime) {
            $filename = FileUtils::pathDiff($dir, $file, true);
            $target = FileUtils::removeTwigExtension($this->config->getTargetPath() . DIRECTORY_SEPARATOR . $filename);

            if (file_exists($target)) {
                unlink($target);
            }

            $this->compiler->buildFile($file, $dir);
        }

        // Do it all again
        $this->last_files[$alias] = $current_files;

        // Release some memory
        unset($current_files);
        unset($modified);
        unset($deleted);
    }

    protected function watchAssets ($dir, $alias)
    {
        if (!array_key_exists($alias, $this->last_files)) {
            $this->last_files[$alias] = array();
        }

        // Get the updated list of files
        $current_files = FileUtils::getFileModifyTimes($dir);

        // Compare our arrays and determine what has changed
        $modified = array_diff_assoc($current_files, $this->last_files[$alias]);
        $deleted = array_diff_key($this->last_files[$alias], $current_files);

        // Re-render any updated or new files
        foreach ($modified as $file => $mtime) {
            $filename = FileUtils::pathDiff($dir, $file, true);
            $target = $this->config->getTargetPath() . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . $filename;
            $parent_dir = pathinfo($target, PATHINFO_DIRNAME);

            FileUtils::mkdirIfNotExists($parent_dir);

            if (file_exists($target)) {
                unlink($target);
            }

            $this->compiler->println(' Copying '.$file);
            copy($file, $target);
        }

        // Do it all again
        $this->last_files[$alias] = $current_files;

        // Release some memory
        unset($current_files);
        unset($modified);
        unset($deleted);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $directory = $input->getArgument('directory');

        if (!$directory) {
            $directory = getcwd();
        }

        $this->config = Config::loadFile($directory . '/spark.yml');
        $this->compiler = new Compiler($this->config);
        $this->compiler->setOutput($output);

        $this->last_files = array();

        $pagePath = $this->config->getPagePath();
        $assetPath = $this->config->getAssetPath();

        // Start watching… I'm so sorry for this…
        while (true) {
            $this->watchPages($pagePath, 'pages');
            $this->watchAssets($assetPath, 'assets');

            // Pause to breathe
            sleep(1);
        }
    }

}
