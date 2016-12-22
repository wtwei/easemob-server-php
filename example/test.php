<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 2016/12/22
 * Time: 17:02
 */

require ('../init.php');

\Easemob\Im::setOrgName('OrgName');
\Easemob\Im::setAppName('AppName');
\Easemob\Im::setClientId('ClientId');
\Easemob\Im::setClientSecret('ClientSecret');

$obj = new \Easemob\AccessToken();
$token = $obj->getToken();
var_dump($token);
