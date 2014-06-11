<?php
namespace WScore\Basic\Auth;

abstract class AuthAbstract implements AuthInterface
{
    /**
     * @var int
     */
    protected $status = self::AUTH_NONE;

    /**
     * @var string
     */
    protected $userId;

    /**
     * @var string
     */
    protected $userPw;

    protected $passwordColumn = 'password';

    protected $rememberColumn = 'remember_me';

    /**
     * @var array
     */
    protected $userInfo = array();

    /**
     * name of session to save this auth state.
     *
     * @var string
     */
    protected $saveId;

    /**
     * @var array
     */
    protected $loginInfo = array();

    protected $rememberDays = 7;

    // +----------------------------------------------------------------------+
    //  internal stuff
    // +----------------------------------------------------------------------+
    /**
     * @return mixed
     */
    protected function getSaveId()
    {
        if ( $this->saveId ) {
            return $this->saveId;
        }
        $class = get_called_class();
        return str_replace( '\\', '-', $class );
    }

    /**
     *
     */
    protected function saveOk( $id, $pw, $by = self::BY_POST )
    {
        $this->userId        = $id;
        $this->userPw        = $pw;
        $this->status        = self::AUTH_OK;
        $save                = [
            'id'   => $this->userId,
            'time' => date( 'Y-m-d H:i:s' ),
            'by'   => $by,
        ];
        $this->loginInfo     = $save;
        $saveId              = $this->getSaveId();
        $_SESSION[ $saveId ] = $save;
    }

    /**
     * @param $id
     */
    protected function rememberMe( $id )
    {
        $token = $this->calRememberToken();
        $this->updateUser( $id, $this->rememberColumn, $token );
        $time = time() + 60 * 60 * 24 * $this->rememberDays;
        setcookie( self::REMEMBER_ID, $id, $time, '/', true );
        setcookie( self::REMEMBER_ME, $token, $time, '/', true );
    }

    // +----------------------------------------------------------------------+
    //  get the state of the auth
    // +----------------------------------------------------------------------+
    /**
     * @return bool
     */
    public function isLogin()
    {
        return $this->status === self::AUTH_OK;
    }

    /**
     * @return array
     */
    public function getUserInfo()
    {
        return $this->userInfo;
    }

    /**
     * @return array
     */
    public function getLoginInfo()
    {
        return $this->loginInfo;
    }

    /**
     *
     */
    public function logout()
    {
        $this->status = self::AUTH_NONE;
        $saveId       = $this->getSaveId();
        if ( isset( $_SESSION[ $saveId ] ) ) {
            unset( $_SESSION[ $saveId ] );
        }
    }

    // +----------------------------------------------------------------------+
    //  authorization
    // +----------------------------------------------------------------------+
    /**
     * @param string $id
     * @param string $pw
     * @param bool $remember
     * @return bool
     */
    public function getAuth( $id, $pw, $remember = false )
    {
        if ( !$this->verifyUserId( $id ) ) {
            $this->status = self::AUTH_FAILED;
            return $this->isLogin();
        }
        if ( !$this->verifyUserPw( $pw ) ) {
            $this->status = self::AUTH_FAILED;
            return $this->isLogin();
        }
        $this->saveOk( $id, $pw );
        $this->updatePwdIfNecessary( $id, $pw );
        if ( $remember ) {
            $this->rememberMe( $id );
        }
        return $this->isLogin();
    }

    /**
     * @param $id
     * @param null $pw
     * @return bool|mixed
     */
    public function forceAuth( $id, $pw=null )
    {
        $this->verifyUserId($id);
        if( $pw ) {
            $this->updatePwdIfNecessary( $id, $pw );
        }
        $this->saveOk( $id, $pw, self::BY_FORCED );
        return $this->isLogin();
    }

    /**
     * @return bool
     */
    public function getRemember()
    {
        $id    = $_COOKIE[ self::REMEMBER_ID ];
        $token = $_COOKIE[ self::REMEMBER_ME ];
        if ( !$this->verifyUserId( $id ) ) {
            $this->status = self::AUTH_FAILED;
        } elseif ( $this->verifyRemember( $token ) ) {
            $this->saveOk( $id, null, self::BY_REMEMBER );
            $this->rememberMe( $id );
        }
        return $this->isLogin();
    }

    /**
     * @return bool
     */
    public function getSession()
    {
        $saveId = $this->getSaveId();
        if ( isset( $_SESSION[ $saveId ] ) ) {
            $id = $_SESSION[ $saveId ][ 'id' ];
            if( $this->verifyUserId( $id ) ) {
                $this->userId    = $_SESSION[ $saveId ][ 'id' ];
                $this->loginInfo = $_SESSION[ $saveId ];
                $this->status    = self::AUTH_OK;
            }
        }
        return $this->isLogin();
    }

    // +----------------------------------------------------------------------+
    //  verify input
    // +----------------------------------------------------------------------+
    /**
     * loads user data from database.
     *
     * @param string $id
     * @return bool
     */
    abstract protected function verifyUserId( $id );

    /**
     * @param string $pw
     * @return bool
     */
    abstract protected function verifyUserPw( $pw );

    /**
     * @param $token
     * @return bool
     */
    abstract protected function verifyRemember( $token );

    /**
     * @param $id
     * @param $column
     * @param $value
     */
    abstract protected function updateUser( $id, $column, $value );

    /**
     * @param $id
     * @param $pw
     */
    abstract protected function updatePwdIfNecessary( $id, $pw );

    /**
     * @return mixed
     */
    abstract protected function calRememberToken();
    // +----------------------------------------------------------------------+
}