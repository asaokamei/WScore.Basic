<?php
namespace WScore\Basic\Auth;

class Authenticate
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
     * @var int
     */
    protected $status = self::AUTH_NONE;

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

    /**
     * @var UserInterface
     */
    protected $user;

    protected $session = array();

    // +----------------------------------------------------------------------+
    //  get the state of the auth
    // +----------------------------------------------------------------------+
    /**
     * @param array $session
     */
    public function __construct($session=array())
    {
        if( $session ) {
            $this->session = &$session;
        } else {
            $this->session = &$_SESSION;
        }
    }

    /**
     * @param UserInterface $user
     */
    public function setUser( $user )
    {
        $this->user      = $user;
        $this->status    = self::AUTH_NONE;
        $this->loginInfo = array();
    }

    /**
     * @return bool
     */
    public function isLogin()
    {
        return $this->status === self::AUTH_OK;
    }

    /**
     * @return UserInterface
     */
    public function getUser() {
        return $this->user;
    }

    // +----------------------------------------------------------------------+
    //  authorization
    // +----------------------------------------------------------------------+
    /**
     * @param Input $input
     * @return bool
     */
    public function getAuth( $input=null )
    {
        if( $input ) {
            $id = $input->getId();
            $pw = $input->getPw();
            $remember = $input->getRemember();
            if( $this->getInput( $id, $pw, $remember ) ) {
                return $this->isLogin();
            }
        }
        $this->getSession() ||
        $this->getRemember();
        return $this->isLogin();
    }

    /**
     * @param string $id
     * @param string $pw
     * @param bool $remember
     * @return bool
     */
    public function getInput( $id, $pw, $remember = false )
    {
        if ( !$this->user->verifyUserId( $id ) ) {
            $this->status = self::AUTH_FAILED;
            return $this->isLogin();
        }
        if ( !$this->user->verifyUserPw( $pw ) ) {
            $this->status = self::AUTH_FAILED;
            return $this->isLogin();
        }
        $this->saveOk( $id, self::BY_POST );
        if ( $remember ) {
            $this->rememberMe( $id );
        }
        return $this->isLogin();
    }

    /**
     * @param $id
     * @return bool|mixed
     */
    public function forceAuth( $id )
    {
        $this->user->verifyUserId($id);
        $this->saveOk( $id, self::BY_FORCED );
        return $this->isLogin();
    }

    /**
     * @return bool
     */
    public function getRemember()
    {
        $id    = $_COOKIE[ self::REMEMBER_ID ];
        $token = $_COOKIE[ self::REMEMBER_ME ];
        if ( !$this->user->verifyUserId( $id ) ) {
            $this->status = self::AUTH_FAILED;
        } elseif ( $this->user->verifyRemember( $token ) ) {
            $this->saveOk( $id, self::BY_REMEMBER );
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
        if ( !isset( $this->session[ $saveId ] ) ) {
            return false;
        }
        if ( !isset( $this->session[ $saveId ]['type'] ) ) {
            return false;
        }
        if( $this->session[ $saveId ][ 'type' ] !== $this->user->getUserTypeId() ) {
            return false;
        }
        $id = $this->session[ $saveId ][ 'id' ];
        if( $this->user->verifyUserId( $id ) ) {
            $this->loginInfo = $this->session[ $saveId ];
            $this->status    = self::AUTH_OK;
        }
        return $this->isLogin();
    }

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
    protected function saveOk( $id, $by = self::BY_POST )
    {
        $this->status        = self::AUTH_OK;
        $save                = [
            'id'   => $id,
            'time' => date( 'Y-m-d H:i:s' ),
            'by'   => $by,
            'user' => $this->user->getUserTypeId(),
        ];
        $this->loginInfo     = $save;
        $saveId              = $this->getSaveId();
        $this->session[ $saveId ] = $save;
    }

    /**
     * @param $id
     */
    protected function rememberMe( $id )
    {
        $token = $this->calRememberToken();
        $this->user->saveRememberToken( $token );
        $time = time() + 60 * 60 * 24 * $this->rememberDays;
        setcookie( self::REMEMBER_ID, $id, $time, '/', true );
        setcookie( self::REMEMBER_ME, $token, $time, '/', true );
    }

    /**
     * @return string
     */
    protected function calRememberToken()
    {
        return openssl_random_pseudo_bytes(64);
    }

    // +----------------------------------------------------------------------+
}