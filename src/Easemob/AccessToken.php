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

/**
 * Class Token
 *
 * @package Easemob
 */
class AccessToken extends Base
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
            $this->getCache()->save($cacheKey, $token['access_token'], $token['expires_in'] - 1500);

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
        $result = self::post($data);
        $token  = '';
        if (isset($result['access_token'])) {
            $token = $result['access_token'];
        }

        return $token;
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
}