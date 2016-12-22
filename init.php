<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 2016/12/22
 * Time: 17:08
 */

if (!function_exists('curl_init')) {
    throw new Exception('emchat needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
    throw new Exception('emchat needs the JSON PHP extension.');
}
if (!function_exists('mb_detect_encoding')) {
    throw new Exception('emchat needs the Multibyte String PHP extension.');
}


require(dirname(__FILE__) . '/src/Easemob/Im.php');
require(dirname(__FILE__) . '/src/Easemob/Error/Error.php');
require(dirname(__FILE__) . '/src/Easemob/Base.php');
require(dirname(__FILE__) . '/src/Easemob/AccessToken.php');
