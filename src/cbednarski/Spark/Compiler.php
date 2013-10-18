<?php

namespace cbednarski\Spark;

use cbednarski\Spark\FileUtils;
use cbednarski\Spark\WatchfulFilesystem;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\PoFileLoader;
use Symfony\Component\EventDispatcher\EventDispatcher;

use cbednarski\Spark\Events\CompilerEvent;
use cbednarski\Spark\Events\CompilerCompileEvent;

class Compiler
{
    protected $config;
    protected $twig;
    protected $loader;
    protected $output;
    protected $plugins = array();
    protected $translators = array();
    protected $active_locale;
    protected $parameters = array();
	protected $dispatcher;
    protected $safe_extensions = array(
        'css', 'js', 'json',
        'png', 'gif', 'jpg', 'jpeg', 'svg', 'ico',
        'ttf', 'eot', 'woff', 'otf',
        'swf', 'flv'
    );

    public function __construct(Config $config)
    {
        $this->config = $config;

        $twig_paths = FileUtils::filterExists(
            array(
                $this->config->getPagePath(),
                $this->config->getLayoutPath()
            )
        );

        $this->loader = new WatchfulFilesystem($twig_paths);

        $this->twig = new \Twig_Environment($this->loader, array(
            'auto_reload' => true,
            'autoescape' => false,
            'cache' => false,
            'debug' => false,
            'optimizations' => -1,
            'strict_variables' => false
        ));

		$this->dispatcher = new EventDispatcher();

        // Initialize Localization stuff
        $this->initializeTranslators();
        $this->twig->addFunction($this->getLocalizationFunction());
        $this->loadPluginFiles();
        $this->setActiveLocale($this->config->getDefaultLocale());

        // Initialize twig parameters
        $this->setTwigParameter('assets', FileUtils::pathDiff($this->config->getBasePath(), $this->config->getAssetPath()));
        $this->setTwigParameter('version', Git::getVersion($this->config->getBasePath()));
    }

    protected function initializeTranslators()
    {
        foreach (Project::getActiveLocales($this->config) as $locale) {
            $loader = new PoFileLoader();
            $trans = new Translator($locale);
            $trans->setFallbackLocale('en_US');
            $trans->addLoader('po', $loader);

            $locales_path = FileUtils::listFilesInDir($this->config->getLocalePath() . DIRECTORY_SEPARATOR . $locale);
            foreach ($locales_path as $loc_file) {
                $trans->addResource('po', $loc_file, $locale);
            }

            $this->translators[$locale] = $trans;
        }
    }

    public function getLocalizationFunction()
    {
        $compiler = $this;

        return new \Twig_SimpleFunction('trans', function ($phrase) use ($compiler) {
            return $compiler->getActiveTranslator()->trans($phrase);
        });
    }

    public function setActiveLocale($locale)
    {
        if (in_array($locale, array_keys($this->translators))) {
            $this->active_locale = $locale;
            $this->setTwigParameter('locale', array(
                'standard' => $locale,
                'url_code' => $this->config->getLocaleCodeFromMap($locale)
            ));
        } else {
            $locale_path = FileUtils::pathDiff($this->config->getBasePath(), $this->config->getLocalePath() . DIRECTORY_SEPARATOR . $locale, true);
            throw new \UnexpectedValueException(
                $locale
                 . ' is not available in this project. Make sure it\'s '
                 . 'configured in your spark.yml and that '
                 . $locale_path
                 . ' exists.'
            );
        }
    }

    public function getActiveLocale()
    {
        return $this->active_locale;
    }

    public function getActiveTranslator()
    {
        return $this->translators[$this->active_locale];
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function getOutput()
    {
        return $this->output;
    }

    public function println($message)
    {
        if ($this->output) {
            $this->output->writeln($message);
        }
    }

    public function addTwigExtension($extension)
    {
        try {
            $this->twig->addExtension($extension);
        } catch (Exception $e) {
            throw new Exception("Add Extension Failed: ", 0, $e);
        }
    }

    public function setTwigParameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    public function getTwigParameters()
    {
        return $this->parameters;
    }

    public function unsetTwigParameter($name)
    {
        if (isset($this->parameters[$name])) {
            unset($this->parameters[$name]);

            return true;
        }

        return false;
    }

    public function mergeTwigParameters($params)
    {
        return array_merge($this->parameters, $params);
    }

    public function compile($source, $target, $twig_params = array())
    {
		$event = new CompilerCompileEvent($this);
		$this->dispatcher->dispatch('beforecompile', $event);

		$twig_params = array_merge($twig_params, $event->getParams());

        // Include the global parameters that were set in $this
        $twig_params = $this->mergeTwigParameters($twig_params);

        $render = $this->twig->render($source, $twig_params);
        file_put_contents($target, $render);
    }

    public function copyAsset($source, $target)
    {
        $filename = FileUtils::pathDiff($this->config->getAssetPath(), $source, true);

        $parent_dir = pathinfo($target, PATHINFO_DIRNAME);
        FileUtils::mkdirIfNotExists($parent_dir);

        if (in_array(pathinfo($filename, PATHINFO_EXTENSION), $this->safe_extensions)) {
            $this->println(' Copying assets' . DIRECTORY_SEPARATOR . $filename);
            copy($source, $target);
        } else {
            $this->println(' <comment>Skipping assets' . DIRECTORY_SEPARATOR . $filename . '</comment>');

            return false;
        }

        return true;
    }

    public function copyAssets()
    {
        foreach (FileUtils::listFilesInDir($this->config->getAssetPath()) as $file) {
            $this->copyAsset(
                $file,
                $this->config->getTargetPath() . '/assets/' . FileUtils::pathDiff($this->config->getAssetPath(), $file, true)
            );
        }
    }

    public function buildPage($source, $target, $twig_params = array())
    {
        // Calculate target filename
        $filename = FileUtils::pathDiff($this->config->getPagePath(), $source, true);

        if (FileUtils::matchFilename($filename, $this->config->getIgnoredPaths())) {
            return false;
        }

        $locale_path_short = $this->config->getLocaleCodeFromMap($this->getActiveLocale()) . DIRECTORY_SEPARATOR;

        // Make sure parent folder for target exists
        FileUtils::mkdirIfNotExists(pathinfo($target, PATHINFO_DIRNAME));

        // Compile or copy if it's not a template
        if (pathinfo($source, PATHINFO_EXTENSION) === 'twig') {
            try {
                $this->println(' Building ' . $locale_path_short . $filename);
                $this->compile($filename, $target, $twig_params);
            } catch (\Exception $e) {
                echo 'Error while processing ' . $filename;
                throw $e;
            }
        } else {
            $this->println(' Copying ' . $locale_path_short . $filename);
            copy($source, $target);
        }

        return true;
    }

    public function buildPages()
    {
        $page_path = $this->config->getPagePath();

        foreach (Project::getActiveLocales($this->config) as $locale) {

            $this->setActiveLocale($locale);

            foreach (FileUtils::listFilesInDir($page_path) as $source) {
                $locale_path = $this->config->getTargetPathForLocale($this->getActiveLocale());
                $filename = FileUtils::pathDiff($this->config->getPagePath(), $source, true);

                $target = FileUtils::removeTwigExtension(
                    $locale_path . DIRECTORY_SEPARATOR . $filename
                );

                $this->buildPage($source, $target);
            }
        }
    }

    public function build()
    {
		$event = new CompilerEvent($this);
		$this->dispatcher->dispatch('beforebuild', $event);

        $this->copyAssets();
        $this->buildPages();

		$event = new CompilerEvent($this);
		$this->dispatcher->dispatch('afterbuild', $event);

        //Run custom plugins after build
        $this->runPlugins();
    }

    protected function loadPluginFiles()
    {
        $plugin_files = FileUtils::listFilesInDir($this->config->getPluginPath());
        // $spark is here so plugins can use it, so it's not unused in spite of what your IDE might say
        $spark = $this;
        foreach ($plugin_files as $plugin_file) {
            require_once(realpath($plugin_file));
        }
    }

    public function addPlugin($name, $plugin)
    {
        $this->plugins[$name] = $plugin;
    }

    public function runPlugins()
    {
        foreach ($this->plugins as $plugin) {
            $plugin();
        }
    }

    public function addTwigFunction(\Twig_SimpleFunction $function)
    {
        $this->twig->addFunction($function);
    }

	public function on ($event, $listener, $priority = 0) {
		$this->dispatcher->addListener($event, $listener, $priority);
	}
}
