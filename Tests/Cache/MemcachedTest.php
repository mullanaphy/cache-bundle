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

    namespace PHY\CacheBundle\Tests\Cache;

    use PHY\CacheBundle\Cache\Memcached;
    use PHY\CacheBundle\Cache\None;
    use PHY\CacheBundle\Tests\CacheTestAbstract;

    /**
     * Test our Memcached class.
     *
     * @package PHY\CacheBundle\Tests\Cache\MemcachedTest
     * @category PHY\CacheBundle
     * @copyright Copyright (c) 2013 John Mullanaphy (http://jo.mu/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class MemcachedTest extends CacheTestAbstract
    {

        /**
         * Return a Memcached class.
         *
         * @return Memcached
         */
        public function getCache()
        {
            if (!class_exists('\Memcached')) {
                $this->markTestSkipped('Memcached not installed.');
                return new None;
            }
            return new Memcached(array(
                'id' => 'phy_testing',
                'server' => 'localhost:11211'
            ));
        }

        /**
         * Test our name is correct.
         */
        public function testServiceOrName()
        {
            $cache = $this->getCache();
            $this->assertEquals('Memcached', $cache->getName());
        }
    }
