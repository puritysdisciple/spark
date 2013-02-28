# Spark

Spark is a lightweight static site builder written in PHP. It uses Twig for templating and provides built-in support for localization and asset delivery.

[![Build Status](https://travis-ci.org/cbednarski/spark.png?branch=master)](https://travis-ci.org/cbednarski/spark)

## Usage

	$ git clone https://github.com/cbednarski/spark.git`
	$ cd spark
	$ composer install

Add `bin/spark` to your path. Then:

	$ spark init my_project
	$ spark build my_project

Open `my_project/build/target/index.html` in your browser.

## API

> I have some custom requirements, can I just use the API?

Sure thing!

    use cbednarski\Spark\Config;
    use cbednarski\Spark\Compiler;

    $config = Config::loadFile($directory . '/spark.yml');
    $compiler = new Compiler($config);

    # Do one-off templates
    $compiler->compiler(
        'template_name.twig',
        'write/to/this/file',
        $twig_template_variables
    );

    # Build everything
    $compiler->build();

Need to work with paths? We've got some great filesystem helpers in the `FileUtils` class.
