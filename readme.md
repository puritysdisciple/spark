# Spark

Spark is a lightweight static site builder written in PHP. It uses Twig for
templating and provides built-in support for localization and asset delivery.

[![Build Status](https://travis-ci.org/cbednarski/spark.png?branch=master)]
(https://travis-ci.org/cbednarski/spark)

## Usage

    $ git clone https://github.com/cbednarski/spark.git`
    $ cd spark
    $ composer install

Add `bin/spark` to your path. Then:

    $ spark init my_project
    $ spark build my_project

Open `my_project/target/index.html` in your browser.

#### How do I add `spark` to my path?

The easiest way is to clone the project to your computer and then create a
symlink in a folder that's already in your path. For example, `/usr/local/bin`
is in my path on both OSX and Ubuntu, so I can simply do:

    $ sudo ln -s /path/to/spark/bin/spark /usr/local/bin/spark

## Folders

Spark creates the following folders for you:

- `pages` where you put all of your pages
- `assets` to hold things like css, images, javascript, etc. that will not be managed by the localization workflow
- `layouts` to hold shared templates / layouts that won't become pages on their own
- `locale` to hold your localization files
- `target` where spark will generate your static site (this is what you'll upload to your server / S3 bucket)

If you don't like these, you can reconfigure them in `spark.yml`.

## API

> I have some custom requirements, can I just use the API?

Sure thing!

```php
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
```

Need to work with paths? We've got some great filesystem helpers in the
`FileUtils` class.
