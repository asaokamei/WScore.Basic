<?php
namespace WScore\Basic\Auth;

class Auth
{
    /**
     * @var Auth
     */
    static $self;

    /**
     * @var UserInterface[]
     */
    protected $users = array();

    /**
     * @var Authenticate
     */
    protected $auth;

    // +----------------------------------------------------------------------+
    //  static methods
    // +----------------------------------------------------------------------+
    /**
     * @return Auth
     * @throws \InvalidArgumentException
     */
    public static function user()
    {
        $users = func_get_args();
        if( empty( $users ) ) {
            throw new \InvalidArgumentException('User to authenticate not specified');
        }
        static::$self = self::forgeAuth();
        call_user_func_array( [static::$self, 'setUsers'], $users );
        return static::$self;
    }

    /**
     * @return Authenticate
     */
    public static function auth()
    {
        return self::$self->getAuthenticate();
    }

    /**
     * @param null|Authenticate $auth
     * @return Auth
     */
    public static function forgeAuth($auth=null)
    {
        if( !$auth ) $auth = new Authenticate();
        return new Auth( $auth );
    }

    /**
     * @param null|array $input
     * @return Input
     */
    public static function forgeInput( $input=null )
    {
        return new Input($input);
    }

    // +----------------------------------------------------------------------+
    //  construction of object.
    // +----------------------------------------------------------------------+
    /**
     * @param Authenticate $auth
     */
    public function __construct( $auth )
    {
        $this->auth = $auth;
    }

    /**
     * @param UserInterface
     * @param UserInterface
     * @return $this
     */
    public function setUsers()
    {
        $users = func_get_args();
        $this->users = $users;
        return $this;
    }

    /**
     * @param Input|array $input
     * @return bool|Authenticate
     */
    public function getAuth( $input=null )
    {
        if( is_array( $input ) ) {
            $input = $this->forgeInput( $input );
        }
        foreach( $this->users as $user ) {
            $this->auth->setUser( $user );
            if( $this->auth->getAuth( $input ) ) {
                return $this->auth;
            }
        }
        return false;
    }

    /**
     * @return Authenticate
     */
    public function getAuthenticate()
    {
        return $this->auth;
    }
    // +----------------------------------------------------------------------+
}