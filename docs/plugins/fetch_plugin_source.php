<?php

$spark->addTwigFunction(new Twig_SimpleFunction('fetch_plugin_source', function($file) {
    return highlight_file(__DIR__ . '/'. $file, true);
}));
