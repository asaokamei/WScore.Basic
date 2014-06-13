<?php
namespace tests\Basic\Auth;

use WScore\Basic\Auth\Input;
use WScore\Basic\Auth\RememberMe;

require_once( dirname( __DIR__ ) . '/autoload.php' );

class Remember_Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Input
     */
    var $input;

    /**
     * @var RememberMe
     */
    var $remember;

    var $post = ['user'=>'test-ID','pass'=>'test-PW','auth'=>'login','remember'=>'yes'];

    var $cookie = ['remember-id'=>'rem', 'remember-me'=>'berMe'];

    function setup()
    {
        $this->input = $this->input();
        $this->remember = $this->remember();
        if( !function_exists('tests\Basic\Auth\setCookie_in_RememberMe_Test') ) {
            function setCookie_in_RememberMe_Test( $key )
            {
                $args = func_get_args();
                $GLOBALS[$key] = $args;
            }
        }
    }

    /**
     * @param null $input
     * @return Input
     */
    function input($input=null)
    {
        if( !$input ) $input = $this->post;
        return new Input($input);
    }

    /**
     * @return RememberMe
     */
    function remember()
    {
        $remember =  new RememberMe( $this->cookie );
        $remember->setSetCookie('tests\Basic\Auth\setCookie_in_RememberMe_Test');
        return $remember;
    }

    function test0()
    {
        $this->assertEquals( 'WScore\Basic\Auth\RememberMe', get_class($this->remember) );
        $this->assertEquals( 'WScore\Basic\Auth\Input', get_class($this->input) );
    }

    /**
     * Input test
     *
     * @test
     */
    function setting_remember_to_Input_finds_remember_data()
    {
        // without remember()
        $input = $this->input;
        $this->assertEquals( true, $input->authLogin() );
        $this->assertEquals( 'test-ID', $input->getId() );
        $this->assertEquals( 'test-PW', $input->getPw() );
        $this->assertEquals( false, $input->getRemember() );

        // with remember()
        $input->remember();
        $this->assertEquals( true, $input->authLogin() );
        $this->assertEquals( 'test-ID', $input->getId() );
        $this->assertEquals( 'test-PW', $input->getPw() );
        $this->assertEquals( 'yes', $input->getRemember() );
    }

    /**
     * RememberMe Test
     *
     * @test
     */
    function rememberMe_gets_id_in_cookie_data()
    {
        $this->assertEquals( $this->cookie['remember-id'], $this->remember->getId() );
        $this->assertEquals( $this->cookie['remember-me'], $this->remember->getToken() );
    }

    /**
     * RememberMe Test
     *
     * @test
     */
    function rememberMe_sets_id_and_token()
    {
        $this->assertEquals( $this->cookie['remember-id'], $this->remember->getId() );
        $this->assertEquals( $this->cookie['remember-me'], $this->remember->getToken() );
        $this->remember->set( 'my-test', 'my-token' );
        $this->assertTrue( isset( $GLOBALS['remember-id'] ) );
        $this->assertTrue( isset( $GLOBALS['remember-me'] ) );

        $remembered = $GLOBALS['remember-id'];
        $this->assertEquals( 'remember-id', $remembered[0] );
        $this->assertEquals( 'my-test', $remembered[1] );
        $this->assertTrue( is_numeric( $remembered[2] ) );
        $this->assertEquals( '/', $remembered[3] );

        $remembered = $GLOBALS['remember-me'];
        $this->assertEquals( 'remember-me', $remembered[0] );
        $this->assertEquals( 'my-token', $remembered[1] );
        $this->assertTrue( is_numeric( $remembered[2] ) );
        $this->assertEquals( '/', $remembered[3] );
    }
}
