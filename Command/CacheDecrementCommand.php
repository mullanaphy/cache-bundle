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
     * This will allow us to decrement a cache key via:
     * php app/console phy:cache:decrement --key=KEY --value=VALUE
     *
     * @package PHY\CacheBundle\Command\CacheDecrementCommand
     * @category PHY\CacheBundle
     * @copyright Copyright (c) 2013 John Mullanaphy (http://jo.mu/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class CacheDecrementCommand extends ContainerAwareCommand
    {

        /**
         * Configure this CLI command.
         */
        protected function configure()
        {
            $this->setName('phy:cache:decrement')->setDescription('Decrement a cache key.')
                ->addOption('key', 'K', InputOption::VALUE_REQUIRED, 'Where to store the key.')
                ->addOption('value', 'V', InputOption::VALUE_REQUIRED, 'Decrementing value.', '1');
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
            $value = $input->getOption('value');
            $output->writeln('Decrementing key '.$key.' by '.$value.' in cache '.$cache->getName().'.');
            if ($cache->decrement($key, $value)) {
                $output->writeln('<info>Decremented by '.$value.'!</info>');
            } else {
                $output->writeln('<error>Decremented by absolutely nothing...</error>');
            }
        }

    }