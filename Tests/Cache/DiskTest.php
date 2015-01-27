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

    use PHY\CacheBundle\Cache\Disk;

    /**
     * Test our Disk class.
     *
     * @package PHY\CacheBundle\Tests\Cache\DiskTest
     * @category PHY\CacheBundle
     * @copyright Copyright (c) 2013 John Mullanaphy (http://jo.mu/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class DiskTest extends \PHPUnit_Framework_TestCase
    {

        private $directory = false;

        /**
         * {@inheritDoc}
         */
        public function setUp()
        {
            $directory = '.' . DIRECTORY_SEPARATOR . 'tmp';
            if (is_writable($directory)) {
                $this->directory = $directory;
                $this->cleanUp();
            }
        }

        /**
         * {@inheritDoc}
         */
        public function tearDown()
        {
            if ($this->directory) {
                $this->cleanUp();
            }
        }

        /**
         * Let's clear our or tmp directory so we know we're running these tests clean.
         */
        private function cleanUp()
        {
            $files = glob($this->directory . DIRECTORY_SEPARATOR . '*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }

        /**
         * Return a Disk class.
         *
         * @return Disk
         */
        public function getCache()
        {
            return new Disk(array('location' => $this->directory));
        }

        /**
         * Test our name is correct.
         */
        public function testName()
        {
            if (!$this->directory) {
                $this->markTestSkipped('Temporary test folder is not writable.');
            }
            $this->assertEquals('Disk', $this->getCache()->getName());
        }
    }
