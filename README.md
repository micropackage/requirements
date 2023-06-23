# Requirements

[![BracketSpace Micropackage](https://img.shields.io/badge/BracketSpace-Micropackage-brightgreen)](https://bracketspace.com)
[![Latest Stable Version](https://poser.pugx.org/micropackage/requirements/v/stable)](https://packagist.org/packages/micropackage/requirements)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/micropackage/requirements.svg)](https://packagist.org/packages/micropackage/requirements)
[![Total Downloads](https://poser.pugx.org/micropackage/requirements/downloads)](https://packagist.org/packages/micropackage/requirements)
[![License](https://poser.pugx.org/micropackage/requirements/license)](https://packagist.org/packages/micropackage/requirements)

<p align="center">
    <img src="https://bracketspace.com/extras/micropackage/micropackage-small.png" alt="Micropackage logo"/>
</p>

## 🧬 About Requirements

This micropackage allows you to test environment requirements to run your plugin.

It can test:

- PHP version
- PHP Extensions
- SSL state
- WordPress version
- Active plugins
- Current theme
- [DocHooks](https://github.com/micropackage/dochooks) support

But it's open for any other custom check.

## 💾 Installation

``` bash
composer require micropackage/requirements
```

## 🕹 Usage

## Basic usage

In the plugin main file:

```php
<?php
/*
Plugin Name: My Test Plugin
Version: 1.0.0
*/

// Composer autoload.
require_once __DIR__ . '/vendor/autoload.php' ;

$requirements = new \Micropackage\Requirements\Requirements( 'My Test Plugin', array(
	'php'                => '7.0',
	'php_extensions'     => array( 'soap' ),
	'wp'                 => '5.3',
	'dochooks'           => true,
	'ssl'                => true,
	'plugins'            => array(
		array( 'file' => 'akismet/akismet.php', 'name' => 'Akismet', 'version' => '3.0' ),
		array( 'file' => 'hello-dolly/hello.php', 'name' => 'Hello Dolly', 'version' => '1.5' )
	),
	'theme'              => array(
		'slug' => 'twentysixteen',
		'name' => 'Twenty Sixteen'
	),
) );

/**
 * Run all the checks and check if requirements has been satisfied.
 * If not - display the admin notice and exit from the file.
 */
if ( ! $requirements->satisfied() ) {
	$requirements->print_notice();
	return;
}

// ... plugin runtime.
```

## Advanced usage

You can also define your own custom checks.

```php
class CustomCheck extends \Micropackage\Requirements\Abstracts\Checker {

	/**
	 * Checker name
	 *
	 * @var string
	 */
	protected $name = 'custom-check';

	/**
	 * Checks if the requirement is met
	 *
	 * @param  string $value Requirement.
	 * @return void
	 */
	public function check( $value ) {

		// Do your check here and if it fails, add the error.
		if ( 'something' === $value ) {
			$this->add_error( 'You need something!' );
		}

	}

}

$requirements = new \Micropackage\Requirements\Requirements( 'My Test Plugin', array(
	'custom-check' => 'something else',
) );

$requirements->register_checker( 'CustomCheck' );

$is_good = $requirements->satisfied();
```

## 📦 About the Micropackage project

Micropackages - as the name suggests - are micro packages with a tiny bit of reusable code, helpful particularly in WordPress development.

The aim is to have multiple packages which can be put together to create something bigger by defining only the structure.

Micropackages are maintained by [BracketSpace](https://bracketspace.com).

## 📖 Changelog

[See the changelog file](./CHANGELOG.md).

## 📃 License

This software is released under MIT license. See the [LICENSE](./LICENSE) file for more information.
