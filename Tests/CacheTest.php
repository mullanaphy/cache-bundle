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

    use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
    use PHY\CacheBundle\Cache;
    use PHY\CacheBundle\Cache\Local;

    /**
     * Test the main cache service.
     *
     * @package PHY\CacheBundle\Tests\CacheTest
     * @category PHY\CacheBundle
     * @copyright Copyright (c) 2013 John Mullanaphy (http://jo.mu/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class CacheTest extends WebTestCase
    {

        private $container;

        /**
         * Boot up our kernel and grab the container.
         */
        public function setUp()
        {
            $kernel = static::createKernel();
            $kernel->boot();
            $this->container = $kernel->getContainer();
        }

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
            $this->assertInstanceOf('PHY\CacheBundle\Cache', $this->container->get('phy_cache'));
        }

        /**
         * Test our basic set/get.
         */
        public function testSetAndGet()
        {
            $cache = $this->getCache();
            $cache->set('key1', 123);
            $this->assertEquals(123, $cache->get('key1'));
        }

        /**
         * Test a multi set and make sure we get the goods back.
         */
        public function testSetMultiAndGet()
        {
            $cache = $this->getCache();
            $cache->set(array(
                'key1' => 123,
                'key2' => 1234
            ));
            $this->assertEquals(123, $cache->get('key1'));
            $this->assertEquals(1234, $cache->get('key2'));
        }

        /**
         * Make sure we get back a null for missing keys.
         */
        public function testGetDoesntExists()
        {
            $cache = $this->getCache();
            $this->assertIsNull($cache->get('false'));
        }

        /**
         * Test getting multiple keys back.
         */
        public function testGetMulti()
        {
            $cache = $this->getCache();
            $cache->set(array(
                'key1' => 123,
                'key2' => 1234
            ));
            $this->assertEquals(array(
                'key1' => 123,
                'key2' => 1234
            ), $cache->get(array('key1', 'key2')));
        }

        /**
         * Test a replace.
         */
        public function testReplace()
        {
            $cache = $this->getCache();
            $cache->set('key1', 123);
            $cache->set('key1', 1234);
            $this->assertEquals(1234, $cache->get('key1'));
        }

        /**
         * Test a replace multi.
         */
        public function testReplaceMulti()
        {
            $cache = $this->getCache();
            $cache->set('key1', 123);
            $cache->set('key2', 1234);
            $cache->replace(array(
                'key1' => 1234,
                'key2' => 12345
            ));
            $this->assertEquals(array(
                'key1' => 1234,
                'key2' => 12345
            ), $cache->get(array('key1', 'key2')));
        }

        /**
         * Test a decrement.
         */
        public function testDecrement()
        {
            $cache = $this->getCache();
            $cache->set('key1', 3);
            $cache->decrement('key1');
            $this->assertEquals(2, $cache->get('key1'));
        }

        /**
         * Test a decrement by number.
         */
        public function testDecrementByNumber()
        {
            $cache = $this->getCache();
            $cache->set('key1', 3);
            $cache->decrement('key1', 2);
            $this->assertEquals(1, $cache->get('key1'));
        }

        /**
         * Test decrementing multiple numbers.
         */
        public function testDecrementMulti()
        {
            $cache = $this->getCache();
            $cache->set('key1', 3);
            $cache->set('key2', 2);
            $cache->decrement(array('key1', 'key2'));
            $this->assertEquals(array(
                'key1' => 2,
                'key2' => 1
            ), $cache->get(array('key1', 'key2')));
        }

        /**
         * Test an increment.
         */
        public function testIncrement()
        {
            $cache = $this->getCache();
            $cache->set('key1', 1);
            $cache->increment('key1');
            $this->assertEquals(2, $cache->get('key1'));
        }

        /**
         * Test an increment by number.
         */
        public function testIncrementByNumber()
        {
            $cache = $this->getCache();
            $cache->set('key1', 1);
            $cache->increment('key1', 2);
            $this->assertEquals(3, $cache->get('key1'));
        }

        /**
         * Test a full flush.
         */
        public function testFlush()
        {
            $cache = $this->getCache();
            $cache->set('key1', 1);
            $cache->flush();
            $this->assertIsNull($cache->get('key1'));
        }

        /**
         * Test changing our prefix.
         */
        public function testPrefix()
        {
            $cache = $this->getCache();
            $cache->setPrefix('prefix');
            $this->assertEquals('prefix', $cache->getPrefix());
        }

        /**
         * Test changing our default compression flag.
         */
        public function testSettingDefaultCompressionFlag()
        {
            $cache = $this->getCache();
            $cache->setCompression(10);
            $this->assertEquals(10, $cache->getCompression());
        }

        /**
         * Test setting our default expiration. Notice, it's over 9000 because outdated memes are fun and so is being
         * ironic about them.
         */
        public function testSettingDefaultExpiration()
        {
            $cache = $this->getCache();
            $cache->setExpiration(9001);
            $this->assertEquals(9001, $cache->getExpiration());
        }

        /**
         * Test deleting a key.
         */
        public function testDelete()
        {
            $cache = $this->getCache();
            $cache->set('key1', 123);
            $cache->delete('key1');
            $this->assertIsNull($cache->get('key1'));
        }

        /**
         * Test deleting multiple keys.
         */
        public function testDeleteMulti()
        {
            $cache = $this->getCache();
            $cache->set('key1', 123);
            $cache->set('key2', 1234);
            $cache->delete(array('key1', 'key2'));
            $this->assertIsNull($cache->get('key1'));
            $this->assertIsNull($cache->get('key2'));
        }
    }
