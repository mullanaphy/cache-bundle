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

    namespace PHY\CacheBundle;

    use PHY\CacheBundle\Cache\CacheInterface;

    /**
     * Our main cache class. This is what will be sent along to the container.
     *
     * @package PHY\CacheBundle\Cache
     * @category PHY\CacheBundle
     * @copyright Copyright (c) 2013 John Mullanaphy (http://jo.mu/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class Cache
    {

        private $client;
        private $prefix = '';
        private $flag = 0;
        private $expiration = 300;

        /**
         * Inject our cache client on construct.
         *
         * @param CacheInterface $client
         */
        public function __construct(CacheInterface $client = null)
        {
            if (null !== $client) {
                $this->client = $client;
            }
        }

        /**
         * Set our compression flag
         *
         * @param int $flag
         * @return self
         */
        public function setCompression($flag)
        {
            $this->flag = $flag;
            return $this;
        }

        /**
         * Get our compression flag.
         *
         * @return int
         */
        public function getCompression()
        {
            return $this->flag;
        }

        /**
         * Set our default expiration time.
         *
         * @param int $expiration
         * @return self
         */
        public function setExpiration($expiration)
        {
            $this->expiration = $expiration;
            return $this;
        }

        /**
         * Grab our currently set default expiration time.
         *
         * @return int
         */
        public function getExpiration()
        {
            return $this->expiration;
        }

        /**
         * Add a prefix to our cache sets/gets/etcs.
         *
         * @param string $prefix
         * @return self
         */
        public function setPrefix($prefix)
        {
            $this->prefix = $prefix;
            return $this;
        }

        /**
         * Grab our defined prefix
         *
         * @return string
         */
        public function getPrefix()
        {
            return $this->prefix;
        }

        /**
         * Grab our actual cache client.
         *
         * @return CacheInterface
         */
        public function getClient()
        {
            return $this->client;
        }

        /**
         * Decrement a node by $decrement.
         *
         * @param string $node
         * @param int $decrement
         * @return bool
         */
        public function decrement($node, $decrement = 1)
        {
            return $this->getClient()->decrement($this->getPrefix().$node, $decrement);
        }

        /**
         * Delete an entry
         *
         * @param string $node
         * @param int $timeout When to delete this node.
         * @return bool
         */
        public function delete($node, $timeout = 0)
        {
            return $this->getClient()->delete($this->getPrefix().$node, $timeout);
        }

        /**
         * Flush out all keys.
         *
         * @return bool
         */
        public function flush()
        {
            return $this->getClient()->flush();
        }

        /**
         * Grab a node if it exists.
         *
         * @param string $node
         * @param int $flag
         * @return mixed
         */
        public function get($node, $flag = -1)
        {
            if (-1 === $flag) {
                $flag = $this->getCompression();
            }
            return $this->getClient()->get($this->getPrefix().$node, $flag);
        }

        /**
         * Increment a node by $increment.
         *
         * @param string $node
         * @param int $increment
         * @return bool
         */
        public function increment($node, $increment = 1)
        {
            return $this->getClient()->increment($this->getPrefix().$node, $increment);
        }

        /**
         * Replace a node with new data. WARNING: No fault tolerance built in.
         *
         * @param string $node
         * @param mixed $value
         * @param int $flag
         * @param int $expiration
         * @return bool
         */
        public function replace($node, $value = false, $expiration = -1, $flag = -1)
        {
            if (-1 === $expiration) {
                $expiration = $this->getExpiration();
            }
            if (-1 === $flag) {
                $flag = $this->getCompression();
            }
            return $this->getClient()->replace($this->getPrefix().$node, $value, $expiration, $flag);
        }

        /**
         * Store a new key into the memory table.
         *
         * @param string $node
         * @param mixed $value
         * @param int $flag
         * @param int $expiration
         * @return bool
         */
        public function set($node, $value = false, $expiration = -1, $flag = -1)
        {
            if (-1 === $expiration) {
                $expiration = $this->getExpiration();
            }
            if (-1 === $flag) {
                $flag = $this->getCompression();
            }
            return $this->getClient()->set($this->getPrefix().$node, $value, $expiration, $flag);
        }

        /**
         * Grab any stats we can pertaining to our caching.
         *
         * @return array
         */
        public function getStats()
        {
            return $this->getClient()->getStats();
        }

        /**
         * Grab the name of our client.
         *
         * @return string
         */
        public function getName()
        {
            return $this->getClient()->getName();
        }

    }

