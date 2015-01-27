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
    class Memcache implements CacheInterface
    {

        /**
         * @var \Memcache
         */
        private $instance;

        /**
         * $settings['server'] will try to connect to that server.
         *
         * @param array $settings
         */
        public function __construct(array $settings = array())
        {
            $this->instance = new \Memcache;
            if (array_key_exists('server', $settings)) {
                if (is_array($settings['server'])) {
                    $first = false;
                    foreach ($settings['server'] as $server) {
                        if (!$first) {
                            $first = true;
                            call_user_func_array(array($this->instance, 'connect'), array($server));
                        } else {
                            call_user_func_array(array($this->instance, 'addServer'), array($server));
                        }
                    }
                } else {
                    $this->instance->connect($settings['server']);
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
                    $this->instance->decrement($key, $decrement);
                }
            } else {
                $this->instance->decrement($node, $decrement);
            }
        }

        /**
         * {@inheritDoc}
         */
        public function increment($node, $increment = 1)
        {
            if (is_array($node)) {
                foreach ($node as $key) {
                    $this->instance->increment($key, $increment);
                }
            } else {
                $this->instance->increment($node, $increment);
            }
        }

        /**
         * {@inheritDoc}
         */
        public function set($node, $value = null, $expiration = 0, $flag = 0)
        {
            if (is_array($node)) {
                $return = array();
                foreach ($node as $key => $v) {
                    $return[$key] = $this->instance->set($key, $v, $value, $expiration);
                }
                return $return;
            } else {
                return $this->instance->set($node, $value, (int)$flag, $expiration);
            }
        }

        /**
         * {@inheritDoc}
         */
        public function replace($node, $value = null, $expiration = 0, $flag = 0)
        {
            if (is_array($node)) {
                foreach ($node as $key => $v) {
                    $this->instance->replace($key, $v, $value, $expiration);
                }
            } else {
                $this->instance->replace($node, $value, (int)$flag, $expiration);
            }
            return true;
        }

        /**
         * {@inheritDoc}
         */
        public function get($node, $flag = 0)
        {
            $flag = (int)$flag;
            return $this->instance->get($node, $flag);
        }

        /**
         * {@inheritDoc}
         */
        public function delete($node, $timeout = 0)
        {
            if (is_array($node)) {
                foreach ($node as $key) {
                    $this->instance->delete($key, $timeout);
                }
            } else {
                $this->instance->delete($node, $timeout);
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
        public function flush()
        {
            $this->instance->flush();
        }

        /**
         * {@inheritDoc}
         */
        public function getStats()
        {
            return $this->instance->getStats();
        }
    }