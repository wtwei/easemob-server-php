<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 2016/12/21
 * Time: 18:54
 */

namespace Easemob;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\FilesystemCache;
use Easemob\Error\Error;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * Class Token
 *
 * @package Easemob
 */
class AccessToken extends Common
{
    /**
     * Cache.
     *
     * @var Cache
     */
    protected $cache;

    /**
     * Cache Key.
     *
     * @var $cacheKey
     */
    protected $cacheKey;

    /**
     * Cache key prefix.
     *
     * @var string
     */
    protected $prefix = 'easemob.common.access_token.';

    /**
     * Get token from EaseMob API.
     *
     * @param bool $forceRefresh
     *
     * @return string
     */
    public function getToken($forceRefresh = false)
    {
        $cacheKey = $this->getCacheKey();
        $cached = $this->getCache()->fetch($cacheKey);

        if ($forceRefresh || empty($cached)) {
            $token = $this->getTokenFromServer();

            // 防止误差^_^
            $ret = $this->getCache()->save($cacheKey, $token['access_token'], $token['expires_in'] - 1500);
            
            return $token['access_token'];
        }

        return $cached;
    }

    public function getTokenFromServer()
    {
        $data   = [
            'grant_type'    => 'client_credentials',
            'client_id'     => Im::$clientId,
            'client_secret' => Im::$clientSecret,
        ];
        $result = self::request('POST', 'token', $data);
        // $token  = '';
        // if (isset($result['access_token'])) {
        //     $token = $result['access_token'];
        // }

        return $result;
    }

    /**
     * Set cache instance.
     *
     * @param Cache $cache
     *
     * @return $this
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Return the cache manager.
     *
     * @return \Doctrine\Common\Cache\Cache
     */
    public function getCache()
    {
        return $this->cache ?: $this->cache = new FilesystemCache(sys_get_temp_dir());
    }

    /**
     * Get access token cache key.
     *
     * @return string $this->cacheKey
     */
    public function getCacheKey()
    {
        if (is_null($this->cacheKey)) {
            return $this->prefix . Im::$orgName . '#' . Im::$appName;
        }

        return $this->cacheKey;
    }

    /**
     * 发送请求
     * @param $method
     * @param array $params
     * @param $options
     * @return array|mixed
     * @throws Error
     */
    protected static function request($method, $url, $params = [], $options = [])
    {
        $config = ['base_uri' => static::baseUrl()];
        $client = new Client($config);
        $header = [
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ];

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
            throw new Error($e->getMessage(), -1);
        }

        return $body;
    }
}