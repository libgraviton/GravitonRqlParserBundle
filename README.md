GravitonRqlParserBundle
=======================

[![Build Status](https://travis-ci.org/libgraviton/GravitonRqlParserBundle.svg?branch=develop)](https://travis-ci.org/libgraviton/GravitonRqlParserBundle) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/libgraviton/GravitonRqlParserBundle/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/libgraviton/GravitonRqlParserBundle/?branch=develop) [![Code Coverage](https://scrutinizer-ci.com/g/libgraviton/GravitonRqlParserBundle/badges/coverage.png?b=develop)](https://scrutinizer-ci.com/g/libgraviton/GravitonRqlParserBundle/?branch=develop) [![Latest Stable Version](https://poser.pugx.org/graviton/rql-parser-bundle/v/stable.svg)](https://packagist.org/packages/graviton/rql-parser-bundle) [![Total Downloads](https://poser.pugx.org/graviton/rql-parser-bundle/downloads.svg)](https://packagist.org/packages/graviton/rql-parser-bundle) [![Latest Unstable Version](https://poser.pugx.org/graviton/rql-parser-bundle/v/unstable.svg)](https://packagist.org/packages/graviton/rql-parser-bundle) [![License](https://poser.pugx.org/graviton/rql-parser-bundle/license.svg)](https://packagist.org/packages/graviton/rql-parser-bundle)

Symfony 2 bundle to the graviton/php-rql-parser.

This package adheres to [SemVer](http://semver.org/spec/v2.0.0.html) versioning.

It uses a github version of [git-flow](http://nvie.com/posts/a-successful-git-branching-model/) in which new features and bugfixes must be merged to develop
using a github pull request. It uses the standard git-flow naming conventions with the addition of a 'v' prefix to version tags.

Since the underlying library is under heavy development, this bundle is considered unstable, too.
Please refer to the [library](https://github.com/libgraviton/php-rql-parser) for more information about the [current state](https://github.com/libgraviton/php-rql-parser#current-state).

Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
composer require "graviton/rql-parser-bundle": "dev-master"
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding the following line in the `app/AppKernel.php`
file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Graviton\RqlParserBundle\GravitonRqlParserBundle(),
        );

        // ...
    }

    // ...
}
```

How to use
==========

Despite the existence of unit tests, which are already examples how to use the factory, the following example shows it:

```php
[...]

class foo {
    public function __construct(Factory $rqlFactory) {
        $this->rqlFactory = $rqlFactory;
    }

    public function searchSomething($query) {
    
        $visitor = $this->rqlFactory->create('myVisitor', $query);
        
        // do something with the visitor.
    }
}

[...]

```
