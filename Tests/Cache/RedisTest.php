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

    use PHY\CacheBundle\Cache\None;
    use PHY\CacheBundle\Cache\Redis;
    use PHY\CacheBundle\Tests\CacheTestAbstract;

    /**
     * Test our Redis class.
     *
     * @package PHY\CacheBundle\Tests\Cache\RedisTest
     * @category PHY\CacheBundle
     * @copyright Copyright (c) 2013 John Mullanaphy (http://jo.mu/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class RedisTest extends CacheTestAbstract
    {

        private $redisException = 'Could not connect to Redis at localhost:6379';

        /**
         * Return a Redis class.
         *
         * @return Redis
         * @throws \Exception
         */
        public function getCache()
        {
            if (!class_exists('\Redis')) {
                throw new RedisTestException('Redis not installed.');
            }
            $cache = new Redis;
            return $cache;
        }

        /**
         * {@inheritDoc}
         */
        public function testServiceOrName()
        {
            try {
                $cache = $this->getCache();
                $this->assertEquals('Redis', $cache->getName());
            } catch (\Exception $exception) {
                $this->markTestSkipped($exception->getMessage());
            }
        }

        /**
         * {@inheritDoc}
         */
        public function testSetAndGet()
        {
            $this->_test(__FUNCTION__);
        }

        /**
         * {@inheritDoc}
         */
        public function testSetMultiAndGet()
        {
            $this->_test(__FUNCTION__);
        }

        /**
         * {@inheritDoc}
         */
        public function testGetDoesntExists()
        {
            $this->_test(__FUNCTION__);
        }

        /**
         * {@inheritDoc}
         */
        public function testGetMulti()
        {
            $this->_test(__FUNCTION__);
        }

        /**
         * {@inheritDoc}
         */
        public function testReplace()
        {
            $this->_test(__FUNCTION__);
        }

        /**
         * {@inheritDoc}
         */
        public function testReplaceMulti()
        {
            $this->_test(__FUNCTION__);
        }

        /**
         * {@inheritDoc}
         */
        public function testDecrement()
        {
            $this->_test(__FUNCTION__);
        }

        /**
         * {@inheritDoc}
         */
        public function testDecrementMulti()
        {
            $this->_test(__FUNCTION__);
        }

        /**
         * {@inheritDoc}
         */
        public function testDecrementByNumber()
        {
            $this->_test(__FUNCTION__);
        }

        /**
         * All of our tests are the default ones, just wrapped with a try for instances of no Redis connection.
         */
        private function _test($func)
        {
            try {
                parent::$func();
            } catch (\RedisException $exception) {
                $this->markTestSkipped($this->redisException);
            } catch (RedisTestException $exception) {
                $this->markTestSkipped($exception->getMessage());
            }
        }
    }
