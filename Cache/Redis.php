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

    namespace PHY\CacheBundle\Cache;

    /**
     * Using Memcached for our cache management.
     *
     * @package PHY\CacheBundle\Cache\Memcached
     * @category PHY\CacheBundle
     * @copyright Copyright (c) 2013 John Mullanaphy (http://jo.mu/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class Redis implements CacheInterface
    {
        /**
         * @var \Redis $instance
         */
        private $instance;

        /**
         * $settings['server'] server we're using.
         * $settings['port'] port to connect to.
         * $settings['timeout'] connection timeout.
         * $settings['retry'] retry wait time.
         * $settings['persist'] will try and make this connection persistent by using pconnect instead of a connect.
         * $settings['persistent_id'] will set the persistent_id of this Redis session.
         * $settings['password'] if your redis instance is password protected.
         *
         * There is an extra Redis::getInstance() which returns the raw Redis instance, which is helpful since you can
         * do a lot more in Redis then simple caching.
         *
         * @param array $settings
         */
        public function __construct(array $settings = array())
        {
            $this->instance = new \Redis;
            $persist = array_key_exists('persist', $settings) && $settings['persist'];
            $parameters = array(
                $settings['server'],
                array_key_exists('port', $settings)
                    ? $settings['port']
                    : null,
                array_key_exists('timeout', $settings)
                    ? $settings['timeout']
                    : null,
            );
            if ($persist) {
                $parameters[] = array_key_exists('persistent_id', $settings)
                    ? $settings['persistent_id']
                    : null;
                $persist = 'pconnect';
            } else {
                $parameters[] = null;
                $persist = 'connect';
            }
            $parameters[] = array_key_exists('retry', $settings)
                ? $settings['retry']
                : null;
            call_user_func_array(array($this->instance, $persist), $parameters);
            if (array_key_exists('password', $settings)) {
                $this->instance->auth($settings['password']);
            }
        }

        /**
         * {@inheritDoc}
         */
        public function decrement($node, $decrement = 1)
        {
            if (is_array($node)) {
                $func = __FUNCTION__;
                $rows = array();
                foreach ($node as $key) {
                    $rows[$key] = call_user_func_array(array($this, $func), array($key, $decrement));
                }
                return $rows;
            } else {
                return $decrement > 1
                    ? $this->instance->decrBy($node, $decrement)
                    : $this->instance->decr($node);
            }
        }

        /**
         * {@inheritDoc}
         */
        public function delete($node, $timeout = 0)
        {
            return $this->instance->delete($node);
        }

        /**
         * {@inheritDoc}
         */
        public function flush()
        {
            return parent::flush();
        }

        /**
         * {@inheritDoc}
         */
        public function get($node, $flag = 0)
        {
            if (is_array($node)) {
                if ($flag) {
                    $func = __FUNCTION__;
                    $rows = array();
                    foreach ($node as $key) {
                        $rows[$key] = call_user_func_array(array($this, $func), array($key, $flag));
                    }
                    return $rows;
                } else {
                    return $this->instance->mGet($node);
                }
            } else {
                if (MEMCACHE_COMPRESSED === $flag) {
                    return gzuncompress($this->instance->get($node), -1);
                } else {
                    return $this->instance->get($node);
                }
            }
        }

        /**
         * {@inheritDoc}
         */
        public function increment($node, $increment = 1)
        {
            if (is_array($node)) {
                $func = __FUNCTION__;
                $rows = array();
                foreach ($node as $key) {
                    $rows[$key] = call_user_func_array(array($this, $func), array($key, $increment));
                }
                return $rows;
            } else {
                return $increment > 1
                    ? $this->instance->incrBy($node, $increment)
                    : $this->instance->incr($node);
            }
        }

        /**
         * {@inheritDoc}
         */
        public function replace($node, $value, $expiration = 0, $flag = 0)
        {
            return $this->set($node, $value, $expiration, $flag);
        }

        /**
         * {@inheritDoc}
         */
        public function set($node, $value, $expiration = 0, $flag = 0)
        {
            if (is_array($node)) {
                $func = __FUNCTION__;
                $return = array();
                foreach ($node as $key => $v) {
                    $return[$key] = call_user_func_array(array($this, $func), array($key, $v, $value, $expiration));
                }
                return $return;
            } else {
                $compressor = function ($value) {
                    return gzcompress($value, -1);
                };
                if (MEMCACHE_COMPRESSED === $flag) {
                    $set = $this->instance->set($node, $compressor($node));
                } else {
                    $set = $this->instance->set($node, $value);
                }
                if ($set && $expiration) {
                    $this->instance->setTimeout($node, $expiration);
                }
                return $set;
            }
        }

        /**
         * {@inheritDoc}
         */
        public function getName()
        {
            return 'Redis';
        }

        public function getInstance()
        {
            return $this->instance;
        }

        /**
         * Grab any stats we can pertaining to our caching.
         *
         * @return array
         */
        public function getStats()
        {
            return $this->instance->info();
        }
    }
