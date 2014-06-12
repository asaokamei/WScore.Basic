<?php
namespace WScore\Basic\Auth;

class UserPwd implements UserInterface
{
    protected $idList = array();

    protected $id;

    protected $pw;

    /**
     * returns user type token string to identify the
     * user when using multiple user object.
     *
     * @return string
     */
    public function getUserTypeId()
    {
        return 'UserPwd';
    }

    /**
     * verifies if $id is valid user's ID.
     *
     * @param string $id
     * @return bool
     */
    public function verifyUserId( $id )
    {
        if( isset( $this->idList[$id] ) ) {
            $this->id = $id;
            $this->pw = $this->idList[$id];
            return true;
        }
        return false;
    }

    /**
     * verifies if the $pw is valid password for the user.
     *
     * @param string $pw
     * @return bool
     */
    public function verifyUserPw( $pw )
    {
        return $this->pw === $pw;
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
     * saves the remember-token to the user's data.
     *
     * @param string $token
     * @return bool|void
     */
    public function saveRememberToken( $token )
    {
        // do nothing for rememberMe token.
        // cannot save it in this UserPwd.
        return false;
    }
}