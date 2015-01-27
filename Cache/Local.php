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
     * Using Local for our cache management. Data will stay until the end of
     * execution, it will not persist data.
     *
     * @package PHY\CacheBundle\Cache\Local
     * @category PHY\CacheBundle
     * @copyright Copyright (c) 2013 John Mullanaphy (http://jo.mu/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class Local implements CacheInterface
    {

        protected $data = array();

        /**
         * No settings are needed as we're only going to store the data locally.
         *
         * @param array $settings
         */
        public function __construct(array $settings = array())
        {

        }

        /**
         * {@inheritDoc}
         */
        public function decrement($node, $decrement = 1)
        {
            if (is_array($node)) {
                $rows = array();
                foreach ($node as $key) {
                    $rows[$key] = $this->decrement($key, $decrement);
                }
                return $rows;
            } else {
                $value = $this->get($node);
                if ($value !== false) {
                    $value -= $decrement;
                    $this->replace($node, $value);
                } else {
                    $value = 0 - $decrement;
                    $this->set($node, $value);
                }
                return $value;
            }
        }

        /**
         * {@inheritDoc}
         */
        public function delete($node, $timeout = 0)
        {
            if (is_array($node)) {
                $rows = array();
                foreach ($node as $key) {
                    $rows[$key] = $this->delete($key, $timeout);
                }
                return $rows;
            } else {
                $key = $this->key($node);
                if (array_key_exists($key, $this->data)) {
                    if ($timeout) {
                        $value = $this->get($node);
                        if ($value) {
                            $expires = time() + $timeout;
                            $this->replace($node, $value, $expires);
                        }
                    } else {
                        unset($this->data[$key]);
                    }
                    return true;
                }
                return false;
            }
        }

        /**
         * {@inheritDoc}
         */
        public function flush()
        {
            $count = count($this->data);
            $this->data = array();
            return $count > 0;
        }

        /**
         * {@inheritDoc}
         */
        public function get($node, $flag = 0)
        {
            if (is_array($node)) {
                $return = array();
                foreach ($node as $key) {
                    $return[$key] = $this->get($key, $flag);
                }
                return $return;
            } else {
                $key = $this->key($node);
                if (array_key_exists($key, $this->data)) {
                    /**
                     * @var $item Node
                     */
                    $item = $this->data[$key];
                    if (!$item->hasExpired()) {
                        return $item->getContent();
                    } else {
                        $this->delete($item);
                    }
                }
            }
            return false;
        }

        /**
         * {@inheritDoc}
         */
        public function increment($node, $increment = 1)
        {
            if (is_array($node)) {
                $rows = array();
                foreach ($node as $key) {
                    $rows[$key] = $this->increment($key, $increment);
                }
                return $rows;
            } else {
                $value = $this->get($node);
                if ($value !== false) {
                    $value += $increment;
                    $this->replace($node, $value);
                } else {
                    $value = $increment;
                    $this->set($node, $value);
                }
                return $value;
            }
        }

        /**
         * {@inheritDoc}
         */
        public function replace($node, $value, $expiration = 0, $flag = 0)
        {
            if (is_array($node)) {
                $rows = array();
                foreach ($node as $key => $v) {
                    $rows[$key] = $this->replace($key, $v, $value, $expiration);
                }
                return $rows;
            } else {
                $key = $this->key($node);
                $_node = new Node($node, $value, $expiration);
                if (array_key_exists($key, $this->data)) {
                    $this->data[$key] = null;
                }
                $this->data[$key] = $_node;
                return $value;
            }
        }

        /**
         * {@inheritDoc}
         */
        public function set($node, $value, $expiration = 0, $flag = 0)
        {
            if (is_array($node)) {
                $rows = array();
                foreach ($node as $key => $v) {
                    $rows[$key] = $this->set($key, $v, $value, $expiration);
                }
                return $rows;
            } else {
                $key = $this->key($node);
                if (array_key_exists($key, $this->data)) {
                    return false;
                }
                $_node = new Node($node, $value, $expiration);
                $this->data[$key] = $_node;
                return $value;
            }
        }

        /**
         * Match node names to their appropriate file names.
         *
         * @param string $node
         * @return string
         */
        protected function key($node)
        {
            return md5($node);
        }

        /**
         * {@inheritDoc}
         */
        public function getStats()
        {
            return array();
        }

        /**
         * {@inheritDoc}
         */
        public function getName()
        {
            return 'Local';
        }

        /**
         * {@inheritDoc}
         */
        public function getInstance()
        {
            return null;
        }
    }
