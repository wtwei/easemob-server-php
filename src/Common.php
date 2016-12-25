<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 2016/12/21
 * Time: 18:55
 */

namespace Easemob;

use Easemob\Error\Error;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Common
{
    /**
     * 发送请求
     * @param $method
     * @param array $params
     * @param $options
     * @return array|mixed
     * @throws Error
     */
    protected static function _request($method, $url, $params = [], $options = [])
    {
        $config = ['base_uri' => static::baseUrl()];
        $client = new Client($config);
        $header = [
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ];

        $token_obj               = new AccessToken();
        $token                   = $token_obj->getToken();
        $header['Authorization'] = "Bearer ${token}";

        $data = ['connect_timeout' => 30, 'headers' => $header];
        if ($method == 'GET') {
            $data['query'] = $params;
        } else {
            $data['json'] = $params;
        }

        $options && $data = array_merge($data, $options);

        $body = [];
        try {
            $res = $client->request($method, $url, $data);
            $body = \GuzzleHttp\json_decode($res->getBody()->getContents(), 1);
        } catch (RequestException $e) {
            $info = \GuzzleHttp\json_decode($e->getResponse()->getBody()->getContents(), 1);
            throw new Error($info['error'], -1);
        }

        return $body;
    }

    protected static function _retrieve($id, $options = null)
    {
        $url = static::instanceUrl($id);

        return static::_request('GET', $url, null, $options);
    }

    /**
     * 完整资源URL
     * @param $id
     * @return string
     * @throws Error
     */
    public static function instanceUrl($id)
    {
        $class = get_called_class();
        if ($id === null) {
            $message = "Could not determine which URL to request: "
                . "$class instance has invalid ID: $id";
            throw new Error($message, null);
        }
        $base = static::classUrl();
        $extn = urlencode($id);
        return "$base/$extn";
    }

    /**
     * GET请求
     * @param $params
     * @param $options
     * @return array|mixed
     */
    public static function _all($params, $options = null)
    {
        $url = static::classUrl();
        return static::_request('GET', $url, $params, $options);
    }

    /**
     * POST请求
     * @param $params
     * @param $options
     * @return array|mixed
     */
    protected static function _create($params, $options = null)
    {
        $url = static::classUrl();
        static::validateParams($params);

        return static::_request('POST', $url, $params, $options);
    }

    /**
     * PUT请求
     * @param $id
     * @param $params
     * @param $options
     * @return array|mixed
     */
    protected static function _save($id, $params, $options = null)
    {
        static::validateParams($params);
        $url = static::instanceUrl($id);
        return static::_request('PUT', $url, $params, $options);
    }

    /**
     * delete请求
     * @param $id
     * @param $params
     * @param $options
     * @return array|mixed
     */
    protected static function _delete($id, $params = null, $options = null)
    {
        $url = static::instanceUrl($id);
        return static::_request('DELETE', $url, $params, $options);
    }

    /**
     * 基础URL
     *
     * @return string
     */
    public static function baseUrl()
    {
        return Im::$gatewayUrl . Im::$orgName . '/' . Im::$appName . '/';
    }

    /**
     * The endpoint URL for the given class.
     *
     * @return string
     */
    public static function classUrl()
    {
        $base = static::className();
        return "${base}" . ($base == 'token' ?: 's');
    }

    /**
     * The name of the class, with namespacing and underscores
     *
     * @return string stripped.
     */
    public static function className()
    {
        $class = get_called_class();
        // Useful for namespaces: Foo\Charge
        if ($postfix = strrchr($class, '\\')) {
            $class = substr($postfix, 1);
        }
        // Useful for underscored 'namespaces': Foo_Charge
        if ($postfixFakeNamespaces = strrchr($class, '')) {
            $class = $postfixFakeNamespaces;
        }
        if (substr($class, 0, strlen('Im')) == 'Im') {
            $class = substr($class, strlen('Im'));
        }
        $class = str_replace('_', '', $class);
        $name  = urlencode($class);
        $name  = strtolower($name);
        return $name;
    }

    private static function validateParams($params = null)
    {
        if ($params && !is_array($params)) {
            $message = "You must pass an array as the first argument to Pingpp API "
                . "method calls.";
            throw new Error($message);
        }
    }
}