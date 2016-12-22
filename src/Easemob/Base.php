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

class Base
{
    /**
     * 发送请求
     * @param $method
     * @param array $params
     * @return array|mixed
     * @throws Error
     */
    public function request($method, $params = [])
    {
        $config = ['base_uri' => static::baseUrl()];
        $client = new Client($config);
        $header = ['Content-Type' => 'application/json',
                   'Accept' => 'application/json'];

        if (static::className() != 'token') {
            $token_obj = new AccessToken();
            $token                   = $token_obj->getToken();
            $header['Authorization'] = "Bearer ${token}";
        }

        $data = ['connect_timeout' => 30, 'headers' => $header];
        if ($method == 'GET') {
            $data['query'] = $params;
        } else {
            $data['form_params'] = $params;
        }

        $body = [];
        try {
            $res = $client->request($method, static::classUrl(), $data);
            if ($res->getStatusCode() == 200) {
                $body = \GuzzleHttp\json_decode($res->getBody(), 1);
            }
        } catch (RequestException $e) {
            throw new Error($e->getMessage(), -1);
        }

        return $body;
    }

    /**
     * GET请求
     * @param $params
     * @return array|mixed
     */
    public function get($params)
    {
        return $this->request('GET', $params);
    }

    /**
     * POST请求
     * @param $params
     * @return array|mixed
     */
    public function post($params)
    {
        return $this->request('POST', $params);
    }

    /**
     * PUT请求
     * @param $params
     * @return array|mixed
     */
    public function put($params)
    {
        return $this->request('PUT', $params);
    }

    /**
     * delete请求
     * @param $params
     * @return array|mixed
     */
    public function delete($params)
    {
        return $this->request('DELETE', $params);
    }

    /**
     * 基础URL
     *
     * @return string
     */
    public static function baseUrl()
    {
        return Im::$gatewayUrl . Im::$orgName . '/' . Im::$appName;
    }

    /**
     * The endpoint URL for the given class.
     *
     * @return string
     */
    public static function classUrl()
    {
        $base = static::className();
        return "/${base}" . ($base == 'token' ?: 's');
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
}