<?php
namespace tests\Auth\Mocks;

use WScore\Basic\Auth\UserPwd;

class UserRemember extends UserPwd
{
    const USER_TYPE = 'simple-user';

    /**
     * @param array $idList
     */
    public function __construct( $idList )
    {
        $this->idList = $idList;
    }

    /**
     * @return string
     */
    public function getUserTypeId()
    {
        return self::USER_TYPE;
    }

    /**
     * @param $type
     * @return bool
     */
    public function is($type) {
        return $type == $this->id;
    }

    /**
     * @return bool|string
     */
    public function getRememberToken()
    {
        return 'remembered: '.$this->id;
    }

    /**
     * @param string $token
     * @return bool
     */
    public function verifyRemember( $token )
    {
        return $token == $this->getRememberToken();
    }
}