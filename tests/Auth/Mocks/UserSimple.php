<?php
namespace tests\Auth\Mocks;

use WScore\Basic\Auth\UserPwd;

class UserSimple extends UserPwd
{
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
        return 'simple-user';
    }

    /**
     * @param $type
     * @return bool
     */
    public function is($type) {
        return $type == $this->id;
    }
}