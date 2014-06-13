<?php
namespace WScore\Basic\Auth;

abstract class UserAbstract implements UserInterface
{
    protected $id;

    protected $pw;

    /**
     * returns user type token string to identify the
     * user when using multiple user object.
     *
     * default returns string based on the class name.
     *
     * @return string
     */
    public function getUserTypeId()
    {
        return str_replace('\\', '-', get_called_class());
    }

    /**
     * verifies if the remember token is valid for the user.
     *
     * @param string $token
     * @return bool
     */
    public function verifyRemember( $token )
    {
        return false;
    }

    /**
     * @return bool|string
     */
    public function getRememberToken()
    {
        return false;
    }

    /**
     * calculates a random string for new remember token.
     *
     * @return string
     */
    protected function calRememberToken()
    {
        return bin2hex(openssl_random_pseudo_bytes(32));
    }
}