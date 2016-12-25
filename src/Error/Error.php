<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 2016/12/22
 * Time: 10:55
 */

namespace Easemob\Error;

use Exception;

class Error extends \Exception
{
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        parent::__construct($this->_getMessage($message), $code, $previous);
    }

    private function _getMessage($msg){
        $message = $msg;
        switch ($msg){
            case 'duplicate_unique_property_exists':
                $message = '即时通讯用户已存在';
                break;
        }

        return $message;
    }
}