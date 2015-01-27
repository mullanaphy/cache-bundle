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

    use Symfony\Component\Console\Input\InputOption;
    use Symfony\Component\Console\Input\InputArgument;
    use Symfony\Component\Console\Input\InputInterface;
    use Symfony\Component\Console\Output\OutputInterface;

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
    class CacheReplaceCommand extends CacheCommandAbstract
    {

        /**
         * {@inheritDoc}
         */
        protected function configure()
        {
            $this->setName('phy:cache:replace')
                ->setDescription('Replace a cache key.')
                ->addOption('key', null, InputOption::VALUE_REQUIRED, 'Where to store the key.')
                ->addOption('value', null, InputOption::VALUE_REQUIRED, 'Cache key\'s value.')
                ->addOption('expiration', null, InputOption::VALUE_REQUIRED, 'Key\'s timeout (0 for unlimited).', '1800')
                ->addOption('compress', null, InputOption::VALUE_REQUIRED, 'Compress data in cache.', '0');
            parent::configure();
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
            $cache = $this->getCache($input, $output);

            $key = $input->getOption('key');
            $value = $input->getOption('value');
            $output->writeln('Replacing "' . $value . '" in key ' . $key . ' in cache ' . $cache->getName() . '.');
            if ($cache->replace($key, $value, $input->getOption('expiration'), $input->getOption('compress'))) {
                $output->writeln('<info>SAVED!</info>');
            } else {
                $output->writeln('<error>NOT SAVED!</error>');
            }
        }

    }