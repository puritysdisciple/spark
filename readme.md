# Spark

Spark is a lightweight static site builder written in PHP. It uses Twig for
templating and provides built-in support for localization and asset delivery. Includes built-in deployment to S3 for cheap, reliable hosting.

[![Build Status](https://travis-ci.org/cbednarski/spark.png?branch=master)]
(https://travis-ci.org/cbednarski/spark)

## Requirements

Spark has a few depdencies. If you work with PHP you probably have all of them already. If not, there are only a few things you need to install.

#### git

- [Download git here](http://git-scm.com/downloads) or install with your package manager.

#### PHP 5.3+

You can use [php](http://www.php.net) 5.3 or above. 5.4 or above is recommended.

- Download [PHP for Windows](http://windows.php.net/download/#php-5.4).
- Install PHP on OSX using [homebrew](http://brew.sh/). You might also be able to get away with using Apple's PHP.

#### Composer

[Composer](http://getcomposer.org/) installs additional PHP dependencies for spark. To install on OSX / Linux:

    $ curl -sS https://getcomposer.org/installer | php
    $ sudo mv composer.phar /usr/local/bin/composer # adds composer to path

## Installing Spark

    $ git clone git@gh.riotgames.com:cbednarski/spark.git
    $ cd spark
    $ composer install
    $ sudo ln -s $PWD/bin/spark /usr/local/bin/spark

Spark should now be in your path, which means you can type `spark` at the terminal and see a list of commands.

## Running Spark

Spark is a commandline tool which reads and compiles your templates, incorporating translations as it goes. Your general workflow looks like this:

    $ spark init ~/my_project
    $ cd ~/my_project
    $ spark build

Open `~/my_project/target/index.html` in your browser to see the demo website. When you're done editing stuff you can run `spark deploy` to deploy your site (see below for more details).

### Deployments

Deployments are managed via `spark-deploy.yml`. Since this file may contain secrets, we recommend **not** checking this file into source control.  **NOTE**: all of the config values (key, secret, and bucket) must be specified in order for a deployment to work.

Your `spark-deploy.yml` file should look like this:

    prod:
      aws:
        key: your-secret-key-id
        secret: your-secret-access-key
        bucket: mysite.com
    ci:
      aws:
        key: your-secret-key-id
        secret: your-secret-access-key
        bucket: test.mysite.com

Each deployment (`prod` and `ci`, for example) is named and has its own configuration, so you can deploy to more than one environment. Just specify the name to deploy:

    $ spark deploy prod

#### Deployment from Environment Variables

Spark also supports specifying a deployment using environment variables, which you can use with automated build tools like Jenkins. In this case, only one deployment configuration is supported, and these configs cannot be mixed with a config file. You must specify the following vars to use this deployment scheme:

    SPARK_AWS_KEY
    SPARK_AWS_SECRET
    SPARK_AWS_BUCKET

For example:

    $ SPARK_AWS_KEY=key SPARK_AWS_SECRET=secret SPARK_AWS_BUCKET=bucket spark deploy

### Project Layout

Spark creates the following folders for you:

- `pages` where you put all of your pages
- `assets` to hold things like css, images, javascript, etc. that will not be managed by the localization workflow
- `layouts` to hold shared templates / layouts that won't become pages on their own
- `locale` to hold your localization files
- `target` where spark will generate your static site (this is what you'll upload to your server / S3 bucket)

If you don't like these, you can reconfigure them in `spark.yml`.

### Using Spark as a Library

> I have some custom requirements, can I just use the Spark API?

Sure thing!

```php
use cbednarski\Spark\Config;
use cbednarski\Spark\Compiler;

$config = Config::loadFile($directory . '/spark.yml');
$compiler = new Compiler($config);

# Build one-off templates
$compiler->compiler(
    'template_name.twig',
    'write/to/this/file',
    $twig_template_variables
);

# Build everything
$compiler->build();
```

Almost all spark objects depend on an instance of `cbednarski\Spark\Config`, which sets up a bunch of paths and configuration values for you. However, most spark objects don't depend on anything else at runtime. All you need to do is point is initialize a Config object using your `spark.yml` file and you're good to go.

Need to work with paths? We've got some great filesystem helpers in the
`FileUtils` class.
