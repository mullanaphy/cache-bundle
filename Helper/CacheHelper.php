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

    namespace PHY\CacheBundle\Helper;

    /**
     * A place to toss any helper methods into.
     *
     * @package PHY\CacheBundle\Helper\CacheHelper
     * @category PHY\CacheBundle
     * @copyright Copyright (c) 2013 John Mullanaphy (http://jo.mu/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class CacheHelper
    {

        /**
         * Send data to be JSON prettified. For PHP 5.4 we can just use the JSON_PRETTY_PRINT instead of this odd
         * looping action.
         *
         * @param mixed $mixed
         * @return string
         */
        public function prettyJson($mixed)
        {
            if (version_compare(PHP_VERSION, '5.4.0' >= 0)) {
                return json_encode($mixed, JSON_PRETTY_PRINT);
            }
            $encode = json_encode($mixed);
            $return = '';
            $indented = 0;
            $string = false;
            for ($i = 0, $count = strlen($encode); $i <= $count; ++$i) {
                $_ = substr($encode, $i, 1);
                switch ($_) {
                    case '"':
                        if (!$string) {
                            $string = true;
                        } else if (substr($return, -1) !== '\\') {
                            $string = false;
                        }
                        break;
                    case '{':
                    case '[':
                        if ($string) {
                            break;
                        }
                        ++$indented;
                        $_ .= PHP_EOL;
                        for ($ident = 0; $ident < $indented; ++$ident) {
                            $_ .= '    ';
                        }
                        break;
                    case '}':
                    case ']':
                        if ($string) {
                            break;
                        }
                        --$indented;
                        for ($ident = 0; $ident < $indented; ++$ident) {
                            $_ = '    '.$_;
                        }
                        $_ = PHP_EOL.$_;
                        break;
                    case ',':
                        if ($string) {
                            break;
                        }
                        $_ .= PHP_EOL;
                        for ($ident = 0; $ident < $indented; ++$ident) {
                            $_ .= '    ';
                        }
                        break;
                    case ':':
                        if ($string) {
                            break;
                        }
                        $_ .= ' ';
                        break;
                }
                $return .= $_;
            }
            return preg_replace('#"(-?\d+\.?\d*)"#', '$1', $return);
        }
    }