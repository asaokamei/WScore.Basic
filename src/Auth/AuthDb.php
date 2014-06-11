<?php
namespace WScore\Basic\Auth;

/**
 * Class AuthDb
 * @package WScore\Basic\Auth
 *
 * a simple authentication class using database access.
 * implement
 *  - verifyUserId and
 *  - updateUser
 * methods to use this authentication.
 */
abstract class AuthDb extends AuthAbstract
{
    protected $secretPWD;

    /**
     * get authentication. also with some secret pwd if set.
     * @param string $id
     * @param string $pw
     * @param bool $remember
     * @return bool|void
     */
    public function getAuth( $id, $pw, $remember=false )
    {
        $ok = parent::getAuth( $id, $pw, $remember );
        if( !$ok && $this->secretPWD && $this->secretPWD === $pw ) {
            $this->saveOk( $id, $pw );

        }
    }

    /**
     * @return string
     */
    protected function calRememberToken()
    {
        return openssl_random_pseudo_bytes(64);
    }

    /**
     * @param string $pw
     * @return bool
     */
    protected function verifyUserPw( $pw )
    {
        return password_verify( $pw, $this->userInfo[ $this->passwordColumn ] );
    }

    /**
     * @param $id
     * @param $pw
     * @return mixed
     */
    protected function updatePwdIfNecessary( $id, $pw )
    {
        $hashed = $this->userInfo[ $this->passwordColumn ];
        if( password_needs_rehash( $hashed, PASSWORD_DEFAULT ) ) {
            $hashed = password_hash( $pw, PASSWORD_DEFAULT );
            $this->updateUser( $id, $this->passwordColumn, $hashed );
        }
    }

    /**
     * @param $token
     * @return bool
     */
    protected function verifyRemember( $token )
    {
        return $token === $this->userInfo[ $this->rememberColumn ];
    }
}