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