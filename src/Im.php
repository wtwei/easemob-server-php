<?php
namespace Easemob;

/**
 * Class Im
 *
 * @package  Easemob
 */
class Im
{
    /**
     * Client Id
     *
     * @var string
     */
    public static $clientId;

    /**
     * Client Secret
     *
     * @var string
     */
    public static $clientSecret;

    /**
     * 企业的唯一标识
     *
     * @var string
     */
    public static $orgName;

    /**
     * APP唯一标识
     *
     * @var string
     */
    public static $appName;

    /**
     * 网关地址
     *
     * @var string
     */
    public static $gatewayUrl = 'https://a1.easemob.com/';

    /**
     * 获取终端ID
     *
     * @return string
     */
    public static function getClientId()
    {
        return self::$clientId;
    }

    /**
     * 设置终端ID
     *
     * @param string $clientId ID
     *
     * @return void
     */
    public static function setClientId($clientId)
    {
        self::$clientId = $clientId;
    }

    /**
     * 获取终端安全码
     *
     * @return string
     */
    public static function getClientSecret()
    {
        return self::$clientSecret;
    }

    /**
     * 设置终端安全码
     *
     * @param string $clientSecret 安全码
     *
     * @return void
     */
    public static function setClientSecret($clientSecret)
    {
        self::$clientSecret = $clientSecret;
    }

    /**
     * 获取企业唯一标识
     *
     * @return string
     */
    public static function getOrgName()
    {
        return self::$orgName;
    }

    /**
     * 设置企业唯一标识
     *
     * @param string $orgName 标识名
     *
     * @return void
     */
    public static function setOrgName($orgName)
    {
        self::$orgName = $orgName;
    }

    /**
     * 获取APP唯一标识
     *
     * @return string
     */
    public static function getAppName()
    {
        return self::$appName;
    }

    /**
     * 设置APP唯一标识
     *
     * @param string $appName 标识名
     *
     * @return void
     */
    public static function setAppName($appName)
    {
        self::$appName = $appName;
    }

    /**
     * 获取网关地址
     *
     * @return string
     */
    public static function getGatewayUrl()
    {
        return self::$gatewayUrl;
    }

}