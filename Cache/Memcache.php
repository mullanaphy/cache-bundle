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
     * Using Memcache for our cache management.
     *
     * @package PHY\CacheBundle\Cache\Memcache
     * @category PHY\CacheBundle
     * @copyright Copyright (c) 2013 John Mullanaphy (http://jo.mu/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class Memcache extends \Memcache implements CacheInterface
    {

        /**
         * $settings['server'] will try to connect to that server.
         *
         * @param array $settings
         * @throws Exception If APC caching is not available.
         */
        public function __construct(array $settings = array())
        {
            if (array_key_exists('server', $settings)) {
                if (is_array($settings['server'])) {
                    $first = false;
                    foreach ($settings['server'] as $server) {
                        if (!$first) {
                            $first = true;
                            call_user_func_array(array($this, 'connect'), array($server));
                        } else {
                            call_user_func_array(array($this, 'addServer'), array($server));
                        }
                    }
                } else {
                    $this->connect($settings['server']);
                }
            }
        }

        /**
         * {@inheritDoc}
         */
        public function decrement($node, $decrement = 1)
        {
            if (is_array($node)) {
                foreach ($node as $key) {
                    parent::decrement($key, $decrement);
                }
            } else {
                parent::decrement($node, $decrement);
            }
        }

        /**
         * {@inheritDoc}
         */
        public function increment($node, $increment = 1)
        {
            if (is_array($node)) {
                foreach ($node as $key) {
                    parent::increment($key, $increment);
                }
            } else {
                parent::increment($node, $increment);
            }
        }

        /**
         * {@inheritDoc}
         */
        public function set($node, $value, $expiration = 0, $flag = 0)
        {
            if (is_array($node)) {
                $return = [];
                foreach ($node as $key => $v) {
                    $return[$key] = parent::set($key, $v, $value, $expiration);
                }
                return $return;
            } else {
                return parent::set($node, $value, $flag, $expiration);
            }
        }

        /**
         * {@inheritDoc}
         */
        public function replace($node, $value, $expiration = 0, $flag = 0)
        {
            if (is_array($node)) {
                foreach ($node as $key => $v) {
                    parent::replace($key, $v, $value, $expiration);
                }
            } else {
                parent::replace($node, $value, $flag, $expiration);
            }
            return true;
        }

        /**
         * {@inheritDoc}
         */
        public function get($node, $flag = 0)
        {
            return parent::get($node, $flag);
        }

        /**
         * {@inheritDoc}
         */
        public function delete($node, $timeout = 0)
        {
            if (is_array($node)) {
                foreach ($node as $key) {
                    parent::delete($key, $timeout);
                }
            } else {
                parent::delete($node, $timeout);
            }
            return true;
        }

        /**
         * {@inheritDoc}
         */
        public function getName()
        {
            return 'Memcache';
        }

    }