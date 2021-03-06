# Spark

Spark is a lightweight static site builder written in PHP. It uses Twig for
templating and provides built-in support for localization and asset delivery. Includes built-in deployment to S3 for cheap, reliable hosting.

Spark is used at [Riot Games](http://www.riotgames.com/) to build and deploy our [game info website](http://gameinfo.na.leagueoflegends.com/en/game-info/) and various [promotional](http://promo.leagueoflegends.com/tw/spirit-guard-udyr/) [pages](http://promo.leagueoflegends.com/fr/spirit-guard-udyr/) in over 10 languages. Using Spark, a one-person team can deploy a scalable, secure website in front of tens of millions of viewers in only a few days.

[![Build Status](https://travis-ci.org/cbednarski/spark.png?branch=master)]
(https://travis-ci.org/cbednarski/spark)

[Read the docs here.](http://spark.dev.riotgames.com/)

## Requirements

Spark has a few dependencies. If you work with PHP you probably have all of them already. If not, there are only a few things you need to install.

##### git

- [Download git here](http://git-scm.com/downloads) or install with your package manager.

##### PHP 5.3+

You can use [php](http://www.php.net) 5.3 or above. 5.4 or above is recommended.

- Download [PHP for Windows](http://windows.php.net/download/#php-5.4).
- On OSX install PHP using [homebrew](http://brew.sh/). You might also be able to get away with using Apple's built-in PHP.

##### Composer

[Composer](http://getcomposer.org/) installs additional PHP dependencies for spark. To install on OSX / Linux:

    $ curl -sS https://getcomposer.org/installer | php
    $ sudo mv composer.phar /usr/local/bin/composer # adds composer to path

## Installing Spark

Once the dependencies are satisfied, install spark:

    $ git clone git@gh.riotgames.com:cbednarski/spark.git
    $ cd spark
    $ composer install
    $ sudo ln -s $PWD/bin/spark /usr/local/bin/spark

Spark should now be in your path, which means you can type `spark` at the terminal and see a list of commands.

## Running Spark

Spark is a commandline tool which manages a spark project -- a directory containing a `spark.yml` file. Type `spark` at the terminal to see a list of commands. A basic workflow looks like this:

    $ spark init ~/my_project
    $ cd ~/my_project
    $ spark build
    < edit stuff >
    $ spark build
    $ cd target && php -S localhost:8000

Now you can open http://localhost:8000/en_US in your browser to see the English version and http://localhost:8000/fr_FR for the French version.

### Project Layout

When you run `spark init`, the following folders are created for you:

- `pages` where you put all of your site's pages live
- `assets` to hold things like css, images, javascript, etc. that will not be managed by the localization workflow
- `layouts` to hold shared templates / layouts that won't become pages on their own
- `locale` to hold your localization files
- `target` where spark will generate your static site (this is what you'll upload to your server / S3 bucket)

You can reconfigure these paths in `spark.yml` if you need a particular folder layout.

### Editing a Project

Spark uses [Twig templating](http://twig.sensiolabs.org/documentation), which are similar to Django templates, and include a lot of cool stuff like template inheritance, includes, loops, and conditionals. Since Spark is built with PHP and [Symfony components](http://symfony.com/components) you can write custom PHP to do just about anything you want, like incorporate data from a database or rest API.

### Deployments

Deployments are managed via `spark-deploy.yml`. Since this file may contain secrets, we recommend **not** checking this file into source control.  **NOTE**: *all* of the config values (key, secret, and bucket) must be specified in order for a deployment to work.

Your `spark-deploy.yml` file should look something like this:

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

Each deployment is named and has its own configuration, so you can deploy to more than one environment. The deployments above are called `prod` and `ci`, but you can be creative. Just specify the name to deploy:

    $ spark deploy prod

#### Deployment from Environment Variables

Spark also supports specifying a deployment using environment variables, which you can use with automated build tools like Jenkins. In this case, only one deployment configuration is supported, and these configs cannot be mixed with a config file. You must specify the following environment variables to use this deployment scheme:

    SPARK_AWS_KEY
    SPARK_AWS_SECRET
    SPARK_AWS_BUCKET

For example:

    $ SPARK_AWS_KEY=key SPARK_AWS_SECRET=secret SPARK_AWS_BUCKET=bucket spark deploy

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

## Contributing and Customizing

Spark follows the PSR-2 coding style. You can keep your code in check using [Fabien Potencier's PHP-CS-Fixer](https://github.com/fabpot/PHP-CS-Fixer). Unit tests are run using [PHPUnit](http://phpunit.de/manual/current/en/writing-tests-for-phpunit.html). After running `composer install` you can run tests with the builtin phpunit by running `vendor/phpunit/phpunit/phpunit.php` from the project root.

Almost all spark objects depend on an instance of `cbednarski\Spark\Config`, which sets up a bunch of paths and configuration values for you. However, most spark objects don't depend on anything else at runtime. All you need to do is point is initialize a Config object using your `spark.yml` file and you're good to go.

To orient yourself with the code, start by looking at the compiler and config tests under `tests/cbednarski/Spark/CompilerTest.php` and `test/cbednarski/Spark/ConfigTest.php`. These two classes are where the bulk of the work is done. The CLI, where command execution happens, lives under `bin/spark`.

If you need to with filesystem paths we've got some great filesystem helpers in the
`FileUtils` class.

If you have any questions about Spark, or want to add a feature, contact [Chris Bednarski](mailto:cbednarski@riotgames.com).
