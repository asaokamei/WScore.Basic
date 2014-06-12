<?php
namespace tests\Auth\Mocks;

use WScore\Basic\Auth\UserPwd;

class UserSimple2 extends UserPwd
{
    const USER_TYPE = 'simple-more';

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
}