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
    use Symfony\Component\DependencyInjection\Definition;
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
    class CacheTest extends \PHPUnit_Framework_TestCase
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
        public function testService()
        {
            $this->assertInstanceOf('PHY\CacheBundle\Cache', self::createContainer()->get('phy_cache'));
        }

        /**
         * Test our basic set/get.
         */
        public function testSetAndGet()
        {
            $cache = $this->getCache();
            $cache->set('a', 123);
            $this->assertEquals(123, $cache->get('a'));
        }

        /**
         * Test a multi set and make sure we get the goods back.
         */
        public function testSetMultiAndGet()
        {
            $cache = $this->getCache();
            $cache->set(array(
                'b' => 123,
                'c' => 1234
            ));
            $this->assertEquals(123, $cache->get('b'));
            $this->assertEquals(1234, $cache->get('c'));
        }

        /**
         * Make sure we get back a null for missing keys.
         */
        public function testGetDoesntExists()
        {
            $cache = $this->getCache();
            $this->assertFalse($cache->get('false'));
        }

        /**
         * Test getting multiple keys back.
         */
        public function testGetMulti()
        {
            $cache = $this->getCache();
            $cache->set(array(
                'd' => 123,
                'e' => 1234
            ));
            $this->assertEquals(array(
                'd' => 123,
                'e' => 1234
            ), $cache->get(array('d', 'e')));
        }

        /**
         * Test a replace.
         */
        public function testReplace()
        {
            $cache = $this->getCache();
            $cache->set('f', 123);
            $cache->replace('f', 1234);
            $this->assertEquals(1234, $cache->get('f'));
        }

        /**
         * Test a replace multi.
         */
        public function testReplaceMulti()
        {
            $cache = $this->getCache();
            $cache->set('h', 123);
            $cache->set('i', 1234);
            $cache->replace(array(
                'h' => 1234,
                'i' => 12345
            ));
            $this->assertEquals(array(
                'h' => 1234,
                'i' => 12345
            ), $cache->get(array('h', 'i')));
        }

        /**
         * Test a decrement.
         */
        public function testDecrement()
        {
            $cache = $this->getCache();
            $cache->set('j', 3);
            $cache->decrement('j');
            $this->assertEquals(2, $cache->get('j'));
        }

        /**
         * Test a decrement by number.
         */
        public function testDecrementByNumber()
        {
            $cache = $this->getCache();
            $cache->set('k', 3);
            $cache->decrement('k', 2);
            $this->assertEquals(1, $cache->get('k'));
        }

        /**
         * Test decrementing multiple numbers.
         */
        public function testDecrementMulti()
        {
            $cache = $this->getCache();
            $cache->set('l', 3);
            $cache->set('m', 2);
            $cache->decrement(array('l', 'm'));
            $this->assertEquals(array(
                'l' => 2,
                'm' => 1
            ), $cache->get(array('l', 'm')));
        }

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
            $extension->load(array(), $container);
            $container->getCompilerPassConfig()->setOptimizationPasses(array(new ResolveDefinitionTemplatesPass));
            $container->getCompilerPassConfig()->setRemovingPasses(array());
            $container->compile();
            return $container;
        }
    }
