<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 2016/12/22
 * Time: 17:39
 */

namespace Easemob;

class User extends Common
{

    /**
     * 注册 IM 用户[单个]
     * @param string $username
     * @param string $password
     * @param null $nickname
     * @return array|mixed
     */
    public static function register($username, $password, $nickname = null)
    {
        $params = ['username' => $username, 'password' => $password];
        $nickname && $params['nickname'] = $nickname;

        return self::create($params);
    }

    /**
     * 注册 IM 用户[批量]
     * @param array $users
     * @return array|mixed
     */
    public static function registerAll($users)
    {
        return static::_create($users);
    }

    /**
     * 获取用户信息[单个]
     * @param $username
     * @return array|mixed
     */
    public static function retrieve($username)
    {
        return static::_retrieve($username);
    }

    /**
     * 获取用户信息[批量]
     * @param $params
     * @return array|mixed
     */
    public static function all($params)
    {
        return static::_all($params);
    }

    /**
     * 删除用户[单个]
     * @param $username
     * @return array|mixed
     */
    public static function delete($username)
    {
        return static::_delete($username);
    }

    /**
     * 修改昵称
     * @param $username
     * @param $nickname
     * @return array|mixed
     */
    public static function nickname($username, $nickname)
    {
        return static::_save($username, ['nickname'=>$nickname]);
    }

    /**
     * 修改密码
     * @param $username
     * @param $password
     * @return array|mixed
     */
    public static function password($username, $password)
    {
        return static::_save($username . '/password', ['password'=>$password]);
    }

}
