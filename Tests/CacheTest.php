<?php

    /**
     * PHY/CacheBundle
     * LICENSE
     * This source file is subject to the Open Software License (OSL 3.0)
     * that is bundled with this package in the file LICENSE.txt.
     * It is also available through the world-wide-web at this URL:
     * http://opensource.org/licenses/osl-3.0.php
     * If you did not receive a copy of the license and are unable to
     * obtain it through the world-wide-web, please send an email
     * to john@jo.mu so I can send you a copy immediately.
     */

    namespace PHY\CacheBundle\Tests;

    use PHY\CacheBundle\DependencyInjection\PHYCacheExtension;
    use PHY\CacheBundle\Cache;
    use PHY\CacheBundle\Cache\Local;
    use Symfony\Component\DependencyInjection\ContainerBuilder;
    use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
    use Symfony\Component\DependencyInjection\Compiler\ResolveDefinitionTemplatesPass;

    /**
     * Test the main cache service.
     *
     * @package PHY\CacheBundle\Tests\CacheTest
     * @category PHY\CacheBundle
     * @copyright Copyright (c) 2013 John Mullanaphy (http://jo.mu/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class CacheTest extends CacheTestAbstract
    {

        /**
         * For most of our tests we only need a cache object. It doesn't matter which cache
         * we use to test all of our service calls, so we'll just use Local.
         *
         * @return Cache
         */
        public function getCache()
        {
            return new Cache(new Local);
        }

        /**
         * Test that the service is loaded.
         */
        public function testServiceOrName()
        {
            $this->assertInstanceOf('PHY\CacheBundle\Cache', self::createContainer()->get('phy_cache'));
        }

        /**
         * We need to create our Container a la Symfony2
         *
         * @return ContainerBuilder
         */
        public function createContainer()
        {
            $container = new ContainerBuilder(new ParameterBag(array(
                'kernel.debug' => false,
                'kernel.bundles' => array('YamlBundle' => 'Fixtures\Bundles\YamlBundle\YamlBundle'),
                'kernel.cache_dir' => sys_get_temp_dir(),
                'kernel.environment' => 'test',
                'kernel.root_dir' => __DIR__ . '/../../../../', // src dir
            )));
            $extension = new PHYCacheExtension;
            $container->registerExtension($extension);
            $extension->load(array(array('class' => '\PHY\CacheBundle\Cache\Local')), $container);
            $container->getCompilerPassConfig()->setOptimizationPasses(array(new ResolveDefinitionTemplatesPass));
            $container->getCompilerPassConfig()->setRemovingPasses(array());
            $container->compile();
            return $container;
        }
    }
