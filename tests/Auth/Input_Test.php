<?php
namespace tests\Basic\Auth;

use WScore\Basic\Auth\Input;

require_once( dirname( __DIR__ ) . '/autoload.php' );

class Input_Test extends \PHPUnit_Framework_TestCase
{
    var $input;

    function input($input=array())
    {
        return new Input($input);
    }

    function test0()
    {
        $this->assertEquals( 'WScore\Basic\Auth\Input', get_class($this->input()) );
    }

    /**
     * @test
     */
    function get_id_and_pw_from_input()
    {
        $input = $this->input( array('user'=>'test-ID','pass'=>'test-PW','auth'=>'login') );
        $this->assertEquals( true, $input->authLogin() );
        $this->assertEquals( 'test-ID', $input->getId() );
        $this->assertEquals( 'test-PW', $input->getPw() );
        $this->assertEquals( false, $input->getRemember() );
    }

    /**
     * @test
     */
    function input_returns_null_if_auth_login_not_set()
    {
        $input = $this->input( array('user'=>'test-ID','pass'=>'test-PW') );
        $this->assertEquals( false, $input->authLogin() );
        $this->assertEquals( null, $input->getId() );
        $this->assertEquals( null, $input->getPw() );
        $this->assertEquals( false, $input->getRemember() );
    }

    /**
     * @test
     */
    function rememberMe_returns_whatever_is_set()
    {
        $input = $this->input( array('remember'=>'Memorized','auth'=>'login') );
        $this->assertEquals( true, $input->authLogin() );
        $this->assertEquals( false, $input->getRemember() );
        $input->remember('remember');
        $this->assertEquals( true, $input->authLogin() );
        $this->assertEquals( 'Memorized', $input->getRemember() );
    }
}
