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

    use PHY\CacheBundle\Cache\Exception;
    use PHY\CacheBundle\Cache\Apc;
    use PHY\CacheBundle\Cache\None;
    use PHY\CacheBundle\Tests\CacheTestAbstract;

    /**
     * Test our APC class.
     *
     * @package PHY\CacheBundle\Tests\Cache\ApcTest
     * @category PHY\CacheBundle
     * @copyright Copyright (c) 2013 John Mullanaphy (http://jo.mu/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class ApcTest extends CacheTestAbstract
    {

        /**
         * Return a Memcached class.
         *
         * @return Memcache
         */
        public function getCache()
        {
            try {
                $cache = new Apc(array('mode' => 'user'));
            } catch (Exception $exception) {
                $this->markTestSkipped($exception->getMessage());
                $cache = new None;
            }
            return $cache;
        }

        /**
         * Test our name is correct.
         */
        public function testServiceOrName()
        {
            $cache = $this->getCache();
            $this->assertEquals('APC', $cache->getName());
        }
    }
