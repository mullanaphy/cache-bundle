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
    use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

    /**
     * This will allow us to delete a cache key via:
     * php app/console phy:cache:delete --key=KEY
     *
     * @package PHY\CacheBundle\Command\CacheDeleteCommand
     * @category PHY\CacheBundle
     * @copyright Copyright (c) 2013 John Mullanaphy (http://jo.mu/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class CacheDeleteCommand extends ContainerAwareCommand
    {

        /**
         * Configure this CLI command.
         */
        protected function configure()
        {
            $this->setName('phy:cache:delete')->setDescription('Delete a cache key.')
                ->addOption('key', 'K', InputOption::VALUE_REQUIRED, 'Where to store the key.');
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
            $cache = $this->getContainer()->get('phy_cache');
            $key = $input->getOption('key');
            $output->writeln('Deleting '.$key.' on '.$cache->getName().' cache.');
            if ($cache->delete($key)) {
                $output->writeln('<info>Deleted!</info>');
            } else {
                $output->writeln('<error>Not Deleted! T^T</error>');
            }
        }

    }