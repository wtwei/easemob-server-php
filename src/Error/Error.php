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

    // TODO 异常信息过滤
    private function _getMessage($msg)
    {
        $message = $msg;

        return $message;
    }
}