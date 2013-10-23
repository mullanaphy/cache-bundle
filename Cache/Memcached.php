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
    class Memcached extends \Memcached implements CacheInterface
    {

        /**
         * $settings['id'] will set the persistant_id of this Memcached session.
         * $settings['server'] will try to connect to that server and add pools
         * if an array of servers is sent.
         *
         * @param array $settings
         * @throws Exception If APC caching is not available.
         */
        public function __construct(array $settings = array())
        {
            if (array_key_exists('server', $settings)) {
                $this->addServers($settings['servers']);
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
                    $rows[$key] = $func($key, $decrement);
                }
                return $rows;
            } else {
                return parent::decrement($node, $decrement);
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
                    $rows[$key] = $func($key, $timeout);
                }
                return $rows;
            } else {
                return parent::delete($node);
            }
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
                $func = __FUNCTION__;
                $rows = array();
                foreach ($node as $key) {
                    $rows[$key] = $func($key, $flag);
                }
                return $rows;
            } else {
                if (MEMCACHE_COMPRESSED === $flag) {
                    return gzuncompress(parent::get($node), -1);
                } else {
                    return parent::get($node);
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
                    $rows[$key] = $func($key, $increment);
                }
                return $rows;
            } else {
                return parent::increment($node, $increment);
            }
        }

        /**
         * {@inheritDoc}
         */
        public function replace($node, $value, $expiration = 0, $flag = 0)
        {
            if (is_array($node)) {
                $func = __FUNCTION__;
                $return = [];
                foreach ($node as $key => $v) {
                    $return[$key] = $func($key, $v, $value, $expiration);
                }
                return $return;
            } else {
                $compressor = function ($value) {
                    return gzcompress($value, -1);
                };
                if (MEMCACHE_COMPRESSED === $flag) {
                    return parent::replace($node, $compressor($value), $flag, $expiration);
                } else {
                    return parent::replace($node, $value, $expiration);
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
                $return = [];
                foreach ($node as $key => $v) {
                    $return[$key] = $func($key, $v, $value, $expiration);
                }
                return $return;
            } else {
                $compressor = function ($value) {
                    return gzcompress($value, -1);
                };
                if (MEMCACHE_COMPRESSED === $flag) {
                    return parent::set($node, $compressor($node), $expiration);
                } else {
                    return parent::set($node, $value, $expiration);
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
    }
