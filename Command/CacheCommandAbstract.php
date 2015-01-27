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

    namespace PHY\CacheBundle\Command;

    use PHY\CacheBundle\Cache;
    use Symfony\Component\Console\Input\InputOption;
    use Symfony\Component\Console\Input\InputArgument;
    use Symfony\Component\Console\Input\InputInterface;
    use Symfony\Component\Console\Output\OutputInterface;
    use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

    /**
     * This will allow us to set a cache key via:
     * php app/console phy:cache:set --key=KEY --value=VALUE
     *
     * @package PHY\CacheBundle\Command\CacheSetCommand
     * @category PHY\CacheBundle
     * @copyright Copyright (c) 2013 John Mullanaphy (http://jo.mu/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class CacheCommandAbstract extends ContainerAwareCommand
    {

        /**
         * Configure this CLI command.
         */
        protected function configure()
        {
            $this->addOption('config', null, InputOption::VALUE_OPTIONAL, 'Config options to use.');
        }

        /**
         * Return either the default phy_cache from the service locator or if someone sends in a JSON object to
         * --config in this format we'll use that instead.:
         *
         * {
         *   "type": "memcached",
         *   "settings": {
         *      "server": ["localhost:11211"]
         *    },
         *    "options": {
         *      "prefix": "phy_",
         *      "expiration": 300,
         *      "compression": 0,
         *    }
         * }
         *
         * @param InputInterface $input
         * @param OutputInterface $output
         * @return \PHY\CacheBundle\Cache
         */
        protected function getCache(InputInterface $input, OutputInterface $output)
        {

            $config = false;
            if ($input->hasOption('config') && $config = $input->getOption('config')) {
                $config = @json_decode($config, JSON_OBJECT_AS_ARRAY);
            }
            if ($config) {
                $class = '\PHY\CacheBundle\Cache\\' . ucfirst($config['type']);
                $cache = new Cache(new $class($config['client']), $config['options']);
                return $cache;
            }
            return $this->getContainer()->get('phy_cache');
        }

        /**
         * Run our CLI command.
         *
         * @param InputInterface $input
         * @param OutputInterface $output
         */
        protected function execute(InputInterface $input, OutputInterface $output)
        {
            /**
             * @var \PHY\CacheBundle\Cache $cache
             */

            $key = $input->getOption('key');
            $value = $input->getOption('value');
            $output->writeln('Storing "' . $value . '" in key ' . $key . ' in cache ' . $cache->getName() . '.');
            if ($cache->set($key, $value, $input->getOption('expiration'), $input->getOption('compress'))) {
                $output->writeln('<info>SAVED!</info>');
            } else {
                $output->writeln('<error>NOT SAVED!</error>');
            }
        }

    }