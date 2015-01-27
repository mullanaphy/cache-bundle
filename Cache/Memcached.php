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
    class Memcached implements CacheInterface
    {
        /**
         * @var \Memcached
         */
        private $instance;

        /**
         * $settings['id'] will set the persistent_id of this Memcached session.
         * $settings['server'] will try to connect to that server and add pools
         * if an array of servers is sent.
         *
         * @param array $settings
         */
        public function __construct(array $settings = array())
        {

            $this->instance = new \Memcached(array_key_exists('id', $settings)
                ? $settings['id']
                : null);
            if (array_key_exists('server', $settings)) {
                $this->instance->addServers($settings['servers']);
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
                return $this->instance->decrement($node, $decrement);
            }
        }

        /**
         * {@inheritDoc}
         */
        public function delete($node, $timeout = 0)
        {
            if (is_array($node)) {
                $func = __FUNCTION__;
                $rows = array();
                foreach ($node as $key) {
                    $rows[$key] = call_user_func_array(array($this, $func), array($key, $timeout));
                }
                return $rows;
            } else {
                return $this->instance->delete($node);
            }
        }

        /**
         * {@inheritDoc}
         */
        public function flush()
        {
            return $this->instance->flush();
        }

        /**
         * {@inheritDoc}
         */
        public function get($node, $flag = 0)
        {
            if (is_array($node)) {
                $func = __FUNCTION__;
                $rows = array();
                foreach ($node as $key) {
                    $rows[$key] = call_user_func_array(array($this, $func), array($key, $flag));
                }
                return $rows;
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
                return $this->instance->increment($node, $increment);
            }
        }

        /**
         * {@inheritDoc}
         */
        public function replace($node, $value, $expiration = 0, $flag = 0)
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
                    return $this->instance->replace($node, $compressor($value), $flag, $expiration);
                } else {
                    return $this->instance->replace($node, $value, $expiration);
                }
            }
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
                    return $this->instance->set($node, $compressor($node), $expiration);
                } else {
                    return $this->instance->set($node, $value, $expiration);
                }
            }
        }

        /**
         * {@inheritDoc}
         */
        public function getName()
        {
            return 'Memcached';
        }

        /**
         * {@inheritDoc}
         */
        public function getInstance()
        {
            return $this->instance;
        }

        /**
         * {@inheritDoc}
         */
        public function getStats()
        {
            return $this->instance->getStats();
        }
    }
