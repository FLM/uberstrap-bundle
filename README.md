# FLMUberstrapBundle

## Contents

- [JMSDiExtraBundle](https://github.com/schmittjoh/JMSDiExtraBundle)
- [JMSSecurityExtraBundle](https://github.com/schmittjoh/JMSSecurityExtraBundle)
- [BraincraftedBootstrapBundle](https://github.com/braincrafted/bootstrap-bundle)
- [HautelookAliceBundle](https://github.com/hautelook/AliceBundle)
- [UsuScryptPasswordEncoderBundle](https://github.com/andreausu/UsuScryptPasswordEncoderBundle)
- [StofDoctrineExtensionsBundle](https://github.com/stof/StofDoctrineExtensionsBundle)
- [PhpCssEmbed](https://github.com/krichprollsch/phpCssEmbed) (phpcssembed filter for Assetic)
- [Select2](https://github.com/ivaynberg/select2)
- [jQuery](https://github.com/components/jquery)
- [Bootstrap](https://github.com/components/bootstrap)
- [FOSUserBundle](https://github.com/FriendsOfSymfony/FOSUserBundle)
- [KnpMenuBundle](https://github.com/KnpLabs/KnpMenuBundle)
- [CssMin](https://code.google.com/p/cssmin/) (via [natxet/CssMin](https://github.com/natxet/CssMin))
- [minify](https://code.google.com/p/minify/) (via [mrclay/minify](https://github.com/mrclay/minify))
- [LiipFunctionalTestBundle](https://github.com/liip/LiipFunctionalTestBundle)

## Pre-configured for my needs

Base configuration is assumed to be the latest stable [Symfony2 Standard](https://github.com/symfony/symfony-standard) defaults.

### Doctrine

- Use `doctrine.orm.naming_strategy.underscore` naming strategy for better-looking column names (`my_property` instead of `myProperty`)

### Assetic

- Assumed to be configured with `use_controller: true` in `dev` environment.
- Overrides `AsseticController::render` method to use proper cache headers.
- Overrides `AssetFactory` class to fix [kriswallsmith/assetic #532](https://github.com/kriswallsmith/assetic/issues/532).
- Enables `CacheBustingWorker` which changes the URL of each Assetic resource when one of its source files are modified.
- Enables `phpcssembed` filter which stores small images as data uri strings in the generated CSS files.
- Enables `less` filter for files matching `*.less`
- Configured assets for Bootstrap, jQuery, and Select2.
- Enables `cssmin` filter which minifies CSS using the CssMin library
- Enables `jsmin` filter which minifies JS using the minify library

### Twig

- Enables streaming of templates with a custom `TemplateListener` (not in `dev` or `test` environments, and not for sub-requests, and only when using the `@Template` annotation)
- Enables the Bootstrap form theme
- Includes simple pre-configured HTML5 base template with `flush` after head, header, main, and footer
- Includes partial template for subrequests which only displays the `main_content` or `main` block. Example:
```
{{ render(controller('AcmeDemoBundle:Issue:create', { _partial: true })) }}
```
- Includes `form_bootstrap` macro for displaying a form with a single line of Twig. Example:
```
{{ flm_uberstrap.form_bootstrap(form, { style: 'horizontal', col_size: 'lg', label_col: '4', widget_col: '5' }) }}
```

### FOSUserBundle

- Simplest possible User entity
- Bootstrap adapted login/register views
- HTTP Authentication and in_memory users for test env

### Testing

- Configured to use SQLite in `test` environment (`app/cache/test/db.db3`)
- Uses `LiipFunctionalTestBundle` to effectively roll back the test database after each test
- Automatically loads fixtures specified by the `flm_uberstrap.fixtures` parameter (defaults to `app/Resources/fixtures.yml`, can also be an array)
- If you need a set of fixtures for only one test, send the `fixtures` setting to the `Client`, these will be loaded in addition to the other fixtures (see above):
```
$client = static::createClient(array(), array('fixtures' => array('test_fixtures.yml')));
```

## TODO

- Turn into distribution instead of bundle

## Installation

This assumes you have a [Symfony2 Standard](https://github.com/symfony/symfony-standard) project already set up, with `minimum-stability: stable`.

### Prerequisites

- Node.js (Node.js paths are configured for Ubuntu by default)
- Less (`npm install -g less`)

### composer.json

We're not on Packagist so you have to add this repo manually first:

    "repositories": [
      {
        "type": "vcs",
        "url": "http://github.com/FLM/uberstrap-bundle"
      }
    ]

Also we need some unstable packages, and Composer won't let us force `@dev` so you need to allow it explicitly yourself for some packages:

    "flm/uberstrap-bundle": "@dev",
    "kriswallsmith/assetic": "@dev",
    "symfony/assetic-bundle": "@dev",
    "usu/scrypt-password-encoder-bundle": "@dev",
    "stof/doctrine-extensions-bundle": "@dev"

You most probably already have `kriswallsmith/assetic` and `symfony/assetic-bundle` added. If so, you need to replace the version with `@dev` for now.

Also we need to add the `BraincraftedBootstrapBundle` install command:

    "scripts": {
        "post-install-cmd": [
            [...]
            "Braincrafted\\Bundle\\BootstrapBundle\\Composer\\ScriptHandler::install"
        ],
        "post-update-cmd": [
            [...]
            "Braincrafted\\Bundle\\BootstrapBundle\\Composer\\ScriptHandler::install"
        ]
    }

### AppKernel.php

Add to your `AppKernel.php` file:

    // Uberstrap
    new FLM\UberstrapBundle\FLMUberstrapBundle(),
    new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
    new JMS\AopBundle\JMSAopBundle(),
    new JMS\DiExtraBundle\JMSDiExtraBundle($this),
    new Usu\ScryptPasswordEncoderBundle\UsuScryptPasswordEncoderBundle(),
    new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
    new Braincrafted\Bundle\BootstrapBundle\BraincraftedBootstrapBundle(),
    new FOS\UserBundle\FOSUserBundle(),
    new Knp\Bundle\MenuBundle\KnpMenuBundle(),

And to the `test` part:

    // Uberstrap
    $bundles[] = new Hautelook\AliceBundle\HautelookAliceBundle();
    $bundles[] = new Liip\FunctionalTestBundle\LiipFunctionalTestBundle();

### config.yml

Add to your `config.yml`:

    imports:
      [...]
      - { resource: @FLMUberstrapBundle/Resources/config/config.yml }

### config_test.yml

Add to your `config_test.yml`:

    imports:
      [...]
      - { resource: @FLMUberstrapBundle/Resources/config/config_test.yml }

### security.yml

Remove the `security` part of your `security.yml` and add this to the top:

    imports:
      - { resource: @FLMUberstrapBundle/Resources/config/security.yml }

### routing.yml

Add to your `routing.yml`:

    flm_uberstrap:
        resource: @FLMUberstrapBundle/Resources/config/routing.yml

### Enable Bootstrap

Run this command to generate the Bootstrap files in your `app/Resources/less` directory:

    app/console braincrafted:bootstrap:generate

The generated `variables.less` file is meant for you to modify, you don't want to look like every other Bootstrap site.
