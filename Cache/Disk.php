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
     * Using Disk for our cache management.
     *
     * @package PHY\CacheBundle\Cache\Disk
     * @category PHY\CacheBundle
     * @copyright Copyright (c) 2013 John Mullanaphy (http://jo.mu/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class Disk implements CacheInterface
    {

        private $location = '';
        private $stats = array('connect' => 0, 'success' => 0, 'failure' => 0, 'set' => 0);
        private $STATS;

        /**
         * $settings['location'] will define where to store the cache files.
         *
         * @param array $settings
         * @throws Exception If Disk caching folder doesn't exist or is not writable.
         */
        public function __construct(array $settings = array())
        {
            if (!array_key_exists('location', $settings)) {
                throw new Exception('No folder set for Disk Caching.');
            }
            if (!is_writable($settings['location'])) {
                throw new Exception('Disk Caching is disabled, cache folder is not writable.');
            }
            $this->location = $settings['location'];
            $this->openStats();
            ++$this->stats['connect'];
        }

        public function __destruct()
        {
            $this->closeStats();
        }

        /**
         * {@inheritDoc}
         */
        public function decrement($node, $decrement = 1)
        {
            $value = $this->get($node);
            if (false !== $value) {
                $value -= $decrement;
                return $this->replace($node, $value);
            } else {
                $value = 0 - $decrement;
                return $this->set($node, $value);
            }
        }

        /**
         * {@inheritDoc}
         */
        public function delete($node)
        {
            $file = $this->file($node);
            if (is_writeable($file)) {
                unlink($file);
                return true;
            }
            return false;
        }

        /**
         * {@inheritDoc}
         */
        public function flush()
        {
            $DIR = opendir($this->location);
            if ($DIR) {
                $ignore = array('.', '..');
                while (false !== ($file = readdir($DIR))) {
                    if (!in_array($file, $ignore)) {
                        unlink($this->location . DIRECTORY_SEPARATOR . $file);
                    }
                }
                closedir($DIR);
                return true;
            }
            return false;
        }

        /**
         * {@inheritDoc}
         */
        public function get($node, $flag = 0)
        {
            if (is_array($node)) {
                $return = array();
                foreach ($node as $key) {
                    $return[] = $this->get($key, $flag);
                }
                return $return;
            } else {
                $file = $this->file($node);
                if (is_readable($file)) {
                    $FILE = fopen($file, 'r+');
                    $item = fread($FILE, filesize($file));
                    fclose($FILE);
                    if (MEMCACHE_COMPRESSED === $flag) {
                        $item = unserialize(gzuncompress($item, -1));
                    } else {
                        $item = unserialize($item);
                    }
                    if (!$item->hasExpired()) {
                        return $item->getContent();
                    } else {
                        $this->delete($item);
                    }
                    ++$this->stats['success'];
                } else {
                    ++$this->stats['failure'];
                }
            }
            return false;
        }

        /**
         * {@inheritDoc}
         */
        public function increment($node, $increment = 1)
        {
            $value = $this->get($node);
            if (false !== $value) {
                $value += $increment;
                return $this->replace($node, $value);
            } else {
                $value = $increment;
                return $this->set($node, $value);
            }
        }

        /**
         * {@inheritDoc}
         */
        public function replace($node, $value = false, $expiration = 0, $flag = 0)
        {
            $file = $this->file($node);
            if (is_writeable($file)) {
                unlink($file);
            }
            return $this->set($node, $value, $expiration, $flag);
        }

        /**
         * {@inheritDoc}
         */
        public function set($node, $value = false, $expiration = 0, $flag = 0)
        {
            $file = $this->file($node);
            if (is_file($file)) {
                return false;
            }
            $_node = new Node($node, $value, $expiration);
            $FILE = fopen($file, 'w+');
            if (MEMCACHE_COMPRESSED === $flag) {
                fwrite($FILE, gzcompress(serialize($_node), -1));
            } else {
                fwrite($FILE, serialize($_node));
            }
            fclose($FILE);
            ++$this->stats['set'];
            return $value;
        }

        /**
         * Match node names to their appropriate file names.
         *
         * @param string $node
         * @return string
         */
        private function file($node)
        {
            return $this->location . md5($node) . '.cache';
        }

        /**
         * {@inheritDoc}
         */
        public function getStats()
        {
            $size = 0;
            $DIR = opendir($this->location);
            if ($DIR) {
                $ignore = array('.', '..');
                while (false !== ($file = readdir($DIR))) {
                    if (!in_array($file, $ignore)) {
                        $size += filesize($file);
                    }
                }
                closedir($DIR);
            }
            return array(
                'connections' => $this->stats['connect'], 'size' => $size, 'sets' => $this->stats['set'],
                'hits' => $this->stats['hit'], 'failures' => $this->stats['failure'],
                'success_rate' => sprintf('%d2', (($this->stats['hit'] / $this->stats['failure']) * 100))
            );
        }


        /**
         * Open our stats. This isn't ideal, since
         *
         * @ignore
         */
        private function openStats()
        {
            $file = $this->file('__phy_stats');
            $this->STATS = fopen($file, 'r+');
            $stats = fread($this->STATS, filesize($file));
            if ($stats) {
                $this->stats = unserialize($stats);
            }
            flock($this->STATS, LOCK_EX);
        }

        /**
         * Store and close out stats.
         *
         * @ignore
         */
        private function closeStats()
        {
            fwrite($this->STATS, serialize($this->stats));
            flock($this->STATS, LOCK_UN);
            fclose($this->STATS);
        }

        /**
         * {@inheritDoc}
         */
        public function getName()
        {
            return 'Disk';
        }
    }
