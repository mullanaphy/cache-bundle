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
     * Using APC for our cache management.
     *
     * @package PHY\CacheBundle\Cache\Apc
     * @category PHY\CacheBundle
     * @copyright Copyright (c) 2013 John Mullanaphy (http://jo.mu/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class Apc implements CacheInterface
    {

        /**
         * $settings['mode'] will define what type of APC caching to use.
         *
         * @param array $settings
         * @throws Exception If APC caching is not available.
         */
        public function __construct(array $settings = array())
        {
            if (!array_key_exists('mode', $settings)) {
                $settings['mode'] = 'opcode';
            }
            if (!function_exists('apc_cache_info') || !@apc_cache_info($settings['mode'])) {
                throw new Exception('APC Caching is disabled.');
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
                return apc_dec($node, $decrement);
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
                return apc_delete($node);
            }
        }

        /**
         * {@inheritDoc}
         */
        public function flush()
        {
            return apc_clear_cache();
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
                    return gzuncompress(apc_fetch($node), -1);
                } else {
                    return apc_fetch($node);
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
                return apc_inc($node, $increment);
            }
        }

        /**
         * {@inheritDoc}
         */
        public function replace($node, $value, $expiration = 0, $flag = 0)
        {
            if (is_array($node)) {
                $func = __FUNCTION__;
                $rows = array();
                foreach ($node as $key => $v) {
                    $rows[$key] = call_user_func_array(array($this, $func), array($key, $v, $value, $expiration));
                }
                return $rows;
            } else {
                if (MEMCACHE_COMPRESSED === $flag) {
                    return apc_store($node, gzcompress($value), $expiration);
                } else {
                    return apc_store($node, $value, $expiration);
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
                $rows = array();
                foreach ($node as $key => $v) {
                    $rows[$key] = call_user_func_array(array($this, $func), array($key, $v, $value, $expiration));
                }
                return $rows;
            } else {
                if (MEMCACHE_COMPRESSED === $flag) {
                    return apc_add($node, gzcompress($value), $expiration);
                } else {
                    return apc_add($node, $value, $expiration);
                }
            }
        }

        /**
         * {@inheritDoc}
         */
        public function getStats()
        {
            return apc_cache_info();
        }

        /**
         * {@inheritDoc}
         */
        public function getName()
        {
            return 'APC';
        }

        /**
         * {@inheritDoc}
         */
        public function getInstance()
        {
            return null;
        }
    }
