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

    namespace PHY\CacheBundle\Tests;

    use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
    use PHY\CacheBundle\Cache;

    /**
     * Test the main cache service.
     *
     * @package PHY\CacheBundle\Tests\CacheTest
     * @category PHY\CacheBundle
     * @copyright Copyright (c) 2013 John Mullanaphy (http://jo.mu/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class CacheTest extends WebTestCase
    {

        private $container;

        /**
         * Boot up our kernel and grab the container.
         */
        public function setUp()
        {
            $kernel = static::createKernel();
            $kernel->boot();
            $this->container = $kernel->getContainer();
        }

        /**
         * Test that the service is loaded.
         */
        public function testService()
        {
            $this->assertInstanceOf('PHY\CacheBundle\Cache', $this->container->get('phy_cache'));
        }

    }
