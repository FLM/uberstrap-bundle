FLMUberstrapBundle
==================

Installation
------------

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

If you already have `kriswallsmith/assetic` or `symfony/assetic-bundle` you need to replace the version with `@dev`.

Add to your `AppKernel.php` file:

    // Uberstrap
    new FLM\UberstrapBundle\FLMUberstrapBundle(),
    new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
    new JMS\AopBundle\JMSAopBundle(),
    new JMS\DiExtraBundle\JMSDiExtraBundle($this),
    new Usu\ScryptPasswordEncoderBundle\UsuScryptPasswordEncoderBundle(),
    new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
    new Braincrafted\Bundle\BootstrapBundle\BraincraftedBootstrapBundle(),

And to the `dev` part:

    // Uberstrap
    $bundles[] = new Hautelook\AliceBundle\HautelookAliceBundle();
    
Add to your `config.yml`:

    imports:
      [...]
      - { resource: @FLMUberstrapBundle/Resources/config/config.yml }
