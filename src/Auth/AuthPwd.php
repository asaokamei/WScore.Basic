<?php
namespace WScore\Basic\Auth;

/**
 * Class AuthPwd
 * @package WScore\Basic\Auth
 *
 * a simple auth for fixed id/pw authentication.
 * implement
 *  - getSecretId and
 *  - getSecretPwd
 * methods to use this authentication.
 *
 */
abstract class AuthPwd extends AuthAbstract
{
    abstract protected function getSecretId();

    abstract protected function getSecretPwd();

    /**
     * loads user data from database.
     *
     * @param string $id
     * @return bool
     */
    protected function verifyUserId( $id )
    {
        if( $id != $this->getSecretId() ) {
            return false;
        }
        $this->userInfo = array(
            'id'                   => $this->getSecretId(),
            $this->passwordColumn  => $this->getSecretPwd(),
            $this->rememberColumn  => $this->calRememberToken(),
        );
        return true;
    }

    /**
     * @param $id
     * @param $column
     * @param $value
     */
    protected function updateUser( $id, $column, $value ) {}

    /**
     * @return string
     */
    protected function calRememberToken()
    {
        return sha1( $this->getSecretId() . $this->getSecretPwd() . get_called_class() );
    }

    /**
     * @param string $pw
     * @return bool
     */
    protected function verifyUserPw( $pw )
    {
        return $this->userInfo[ $this->passwordColumn ] == $pw;
    }

    /**
     * @param $token
     * @return bool
     */
    protected function verifyRemember( $token )
    {
        return $this->calRememberToken() == $token;
    }

    /**
     * @param $id
     * @param $pw
     */
    protected function updatePwdIfNecessary( $id, $pw ) {}
}