<?php
namespace tests\Basic\Auth;

use tests\Auth\Mocks\UserSimple;
use WScore\Basic\Auth\Authenticate;
use WScore\Basic\Auth\Input;

require_once( dirname( __DIR__ ) . '/autoload.php' );

class Authenticate_Test extends \PHPUnit_Framework_TestCase
{
    var $idList = array();

    /**
     * @var UserSimple
     */
    var $user;

    /**
     * @var Authenticate
     */
    var $auth;

    var $session = array();

    function setup()
    {
        $this->idList = array(
            'test' => 'test-PW',
            'more' => 'more-PW',
        );
        $this->user = new UserSimple( $this->idList );
        $this->auth = new Authenticate( $this->session );
        $this->auth->setUser( $this->user );
    }

    function input($input=array())
    {
        return new Input($input);
    }

    function test0()
    {
        $this->assertEquals( 'tests\Auth\Mocks\UserSimple', get_class($this->user) );
        $this->assertEquals( 'WScore\Basic\Auth\Authenticate', get_class($this->auth) );
        $this->assertEquals( 'tests\Auth\Mocks\UserSimple', get_class($this->auth->getUser() ) );
    }

    /**
     * @test
     */
    function simple()
    {
        $input  = $this->input(['user'=>'test','pass'=>'test-PW','auth'=>'login']);
        $authOK = $this->auth->getAuth( $input );
        $this->assertEquals( true, $authOK );
        $this->assertEquals( true, $this->auth->getUser()->is('test') );
    }
}