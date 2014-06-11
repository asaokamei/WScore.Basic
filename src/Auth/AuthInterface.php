<?php
/**
 * Created by PhpStorm.
 * User: asao
 * Date: 2014/06/11
 * Time: 16:24
 */
namespace WScore\Basic\Auth;

interface AuthInterface
{
    const AUTH_NONE   =  0;
    const AUTH_OK     =  1;
    const AUTH_FAILED = -1;

    const BY_POST     = 'post';
    const BY_REMEMBER = 'remember';
    const BY_FORCED   = 'forced';
    const BY_SECRET   = 'secret';

    const REMEMBER_ID = 'remember-id';
    const REMEMBER_ME = 'remember-md';

    /**
     * authorizes based on $id and $pw.
     * set $remember to true to set auto-login in the cookie.
     *
     * @param string $id
     * @param string $pw
     * @param bool $remember
     * @return bool
     */
    public function getAuth( $id, $pw, $remember = false );

    /**
     * authorizes based on the session, after getAuth or getRemember.
     *
     * @return bool
     */
    public function getSession();

    /**
     * authorizes based on the cookie's remember-{id|me} value.
     *
     * @return bool
     */
    public function getRemember();

    /**
     * forces to authenticate the user.
     *
     * @param $id
     * @param null $pw
     * @return mixed
     */
    public function forceAuth( $id, $pw = null );

    /**
     * logout.
     * removes session login data.
     */
    public function logout();

    /**
     * checks if user is logged in.
     *
     * @return bool
     */
    public function isLogin();

    /**
     * get the login info.
     *  - id   : id of the user.
     *  - time : login time.
     *  - by   : login method: BY_POST or BY_REMEMBER
     *
     * @return array
     */
    public function getLoginInfo();

    /**
     * get the login user's information as array.
     *
     * @return array
     */
    public function getUserInfo();
}