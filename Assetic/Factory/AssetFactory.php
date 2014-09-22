<?php

namespace FLM\UberstrapBundle\Assetic\Factory;

use Assetic\Asset\AssetCollectionInterface;
use Assetic\Asset\AssetInterface;
use Assetic\Asset\AssetReference;
use Assetic\Factory\AssetFactory as BaseAssetFactory;
use Assetic\Filter\DependencyExtractorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Commented out faulty code that lead to filters not working at all
 */
class AssetFactory extends BaseAssetFactory
{
    private $kernel;
    private $container;
    private $parameterBag;

    /**
     * Constructor.
     *
     * @param KernelInterface       $kernel       The kernel is used to parse bundle notation
     * @param ContainerInterface    $container    The container is used to load the managers lazily, thus avoiding a circular dependency
     * @param ParameterBagInterface $parameterBag The container parameter bag
     * @param string                $baseDir      The base directory for relative inputs
     * @param Boolean               $debug        The current debug mode
     */
    public function __construct(KernelInterface $kernel, ContainerInterface $container, ParameterBagInterface $parameterBag, $baseDir, $debug = false)
    {
        $this->kernel = $kernel;
        $this->container = $container;
        $this->parameterBag = $parameterBag;

        parent::__construct($baseDir, $debug);
    }

    /**
     * Adds support for bundle notation file and glob assets and parameter placeholders.
     *
     * FIXME: This is a naive implementation of globs in that it doesn't
     * attempt to support bundle inheritance within the glob pattern itself.
     */
    protected function parseInput($input, array $options = array())
    {
        $input = $this->parameterBag->resolveValue($input);

        // expand bundle notation
        if ('@' == $input[0] && false !== strpos($input, '/')) {
            // use the bundle path as this asset's root
            $bundle = substr($input, 1);
            if (false !== $pos = strpos($bundle, '/')) {
                $bundle = substr($bundle, 0, $pos);
            }
            $options['root'] = array($this->kernel->getBundle($bundle)->getPath());

            // canonicalize the input
            if (false !== $pos = strpos($input, '*')) {
                // locateResource() does not support globs so we provide a naive implementation here
                list($before, $after) = explode('*', $input, 2);
                $input = $this->kernel->locateResource($before).'*'.$after;
            } else {
                $input = $this->kernel->locateResource($input);
            }
        }

        return parent::parseInput($input, $options);
    }

    protected function createAssetReference($name)
    {
        if (!$this->getAssetManager()) {
            $this->setAssetManager($this->container->get('assetic.asset_manager'));
        }

        return parent::createAssetReference($name);
    }

    protected function getFilter($name)
    {
        if (!$this->getFilterManager()) {
            $this->setFilterManager($this->container->get('assetic.filter_manager'));
        }

        return parent::getFilter($name);
    }

    public function getLastModified(AssetInterface $asset)
    {
        $mtime = 0;

        if ($asset instanceof AssetReference) {
            $refObj = new \ReflectionObject($asset);
            if ($refObj->hasProperty('name') && $refObj->hasProperty('am')) {
                $refProp = $refObj->getProperty('name');
                $refProp->setAccessible(true);
                $name = $refProp->getValue($asset);
                $refProp = $refObj->getProperty('am');
                $refProp->setAccessible(true);
                $am = $refProp->getValue($asset);

                $asset = $am->get($name);
            }
        }

        foreach ($asset instanceof AssetCollectionInterface ? $asset : array($asset) as $leaf) {
            $mtime = max($mtime, $leaf->getLastModified());

            if (!$filters = $leaf->getFilters()) {
                continue;
            }

            $prevFilters = array();
            foreach ($filters as $filter) {
                $prevFilters[] = $filter;

                if (!$filter instanceof DependencyExtractorInterface) {
                    continue;
                }

                // extract children from leaf after running all preceeding filters
                $clone = clone $leaf;

                $clone->clearFilters();
                foreach (array_slice($prevFilters, 0, -1) as $prevFilter) {
                    $clone->ensureFilter($prevFilter);
                }
                $clone->load();

                foreach ($filter->getChildren($this, $clone->getContent(), $clone->getSourceDirectory()) as $child) {
                    $mtime = max($mtime, $this->getLastModified($child));
                }
            }
        }

        return $mtime;
    }
}
