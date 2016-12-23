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
    protected $url;

    /**
     * 发送请求
     * @param $method
     * @param array $params
     * @param $options
     * @return array|mixed
     * @throws Error
     */
    protected function request($method, $params = [], $options = [])
    {
        $config = ['base_uri' => static::baseUrl()];
        $client = new Client($config);
        $header = [
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ];

        if (static::className() != 'token') {
            $token_obj               = new AccessToken();
            $token                   = $token_obj->getToken();
            $header['Authorization'] = "Bearer ${token}";
        }

        $data = ['connect_timeout' => 30, 'headers' => $header];
        if ($method == 'GET') {
            $data['query'] = $params;
        } else {
            $data['form_params'] = $params;
        }

        $options && $data = array_merge($data, $options);

        $body = [];
        try {
            $res = $client->request($method, $this->url, $data);
            if ($res->getStatusCode() == 200) {
                $body = \GuzzleHttp\json_decode($res->getBody(), 1);
            }
        } catch (RequestException $e) {
            throw new Error($e->getMessage(), -1);
        }

        return $body;
    }

    protected function _retrieve($id, $options = null)
    {
        $this->url = $this->instanceUrl($id);

        return $this->request('GET', null, $options);
    }

    /**
     * 完整资源URL
     * @param $id
     * @return string
     * @throws Error
     */
    public function instanceUrl($id)
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
    public function _all($params, $options = null)
    {
        $this->url = static::classUrl();
        return $this->request('GET', $params, $options);
    }

    /**
     * POST请求
     * @param $params
     * @param $options
     * @return array|mixed
     */
    protected function _create($params, $options = null)
    {
        $this->url = static::classUrl();
        static::validateParams($params);

        return $this->request('POST', $params, $options);
    }

    /**
     * PUT请求
     * @param $id
     * @param $params
     * @param $options
     * @return array|mixed
     */
    protected function _save($id, $params, $options = null)
    {
        static::validateParams($params);
        $this->url = $this->instanceUrl($id);
        return $this->request('PUT', $params, $options);
    }

    /**
     * delete请求
     * @param $id
     * @param $params
     * @param $options
     * @return array|mixed
     */
    protected function _delete($id, $params = null, $options = null)
    {
        $this->url = $this->instanceUrl($id);
        return $this->request('DELETE', $params, $options);
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

    private static function validateParams($params = null)
    {
        if ($params && !is_array($params)) {
            $message = "You must pass an array as the first argument to Pingpp API "
                . "method calls.";
            throw new Error($message);
        }
    }
}