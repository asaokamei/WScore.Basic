<?php
namespace WScore\Basic\Auth;

class Input
{
    protected $name = 'auth';

    protected $action  = 'login';

    protected $id = 'user';

    protected $pw = 'pass';

    protected $remember = false;

    protected $input = array();

    /**
     * @param array $input
     */
    public function __construct( $input=null )
    {
        if( !$input ) {
            $input = $_POST;
        }
        if( array_key_exists( $this->name, $input ) &&
            $input[$this->name]===$this->action ) {
            $this->input = $input;
        }
    }

    /**
     * @return null|string
     */
    public function getId()
    {
        return $this->get( $this->id );
    }

    /**
     * @return null|string
     */
    public function getPw()
    {
        return $this->get( $this->pw );
    }

    /**
     * @return null|string
     */
    public function getRemember()
    {
        if( $this->remember ) {
            return $this->get( $this->remember );
        }
        return false;
    }

    protected function get( $name )
    {
        return isset( $this->input[$name] ) ? $this->input[$name] : null;
    }
}