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

    /**
     * Test our Memcached class.
     *
     * @package PHY\CacheBundle\Tests\Cache\MemcachedTest
     * @category PHY\CacheBundle
     * @copyright Copyright (c) 2013 John Mullanaphy (http://jo.mu/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class MemcachedTest extends \PHPUnit_Framework_TestCase
    {

        /**
         * Return a Memcached class.
         *
         * @return Memcached
         */
        public function getCache()
        {
            return new Memcached;
        }

        /**
         * Test our name is correct.
         */
        public function testName()
        {
            if (!class_exists('\Memcached')) {
                $this->markTestSkipped('Memcached not installed.');
            }
            $this->assertEquals('Memcached', $this->getCache()->getName());
        }
    }
