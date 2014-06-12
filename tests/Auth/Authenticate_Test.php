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
        $this->session = new \ArrayObject();
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

    function getAuthSaveId()
    {
        return str_replace('\\','-',get_class($this->auth));
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
    function getAuth_login_successful_using_post_input()
    {
        $input  = $this->input(['user'=>'test','pass'=>'test-PW','auth'=>'login']);
        $authOK = $this->auth->getAuth( $input );

        // test auth status
        $this->assertEquals( true, $authOK );
        $this->assertEquals( true, $this->auth->isLogin() );

        /** @var UserSimple $user */
        $user = $this->auth->getUser();
        $this->assertEquals( true, $user->is('test') );

        // get loginInfo
        $loginInfo = $this->auth->getLoginInfo();
        $this->assertNotEmpty($loginInfo);
        $this->assertEquals('test', $loginInfo['id']);
        $this->assertArrayHasKey('time', $loginInfo);
        $this->assertEquals(Authenticate::BY_POST, $loginInfo['by']);
        $this->assertEquals(UserSimple::USER_TYPE, $loginInfo['user']);

        // test what's saved in the session.
        $this->assertNotEmpty($this->session);
        $saveId = $this->getAuthSaveId();
        $this->assertArrayHasKey( $saveId, $this->session );
        $saved = $this->session[$saveId];
        $this->assertEquals('test', $saved['id']);
        $this->assertArrayHasKey('time', $saved);
        $this->assertEquals(Authenticate::BY_POST, $saved['by']);
        $this->assertEquals(UserSimple::USER_TYPE, $saved['user']);
    }

    /**
     * @test
     */
    function getAuth_fails_for_bad_id()
    {
        $input  = $this->input(['user'=>'bad','pass'=>'bad-PW','auth'=>'login']);
        $authOK = $this->auth->getAuth( $input );

        // test auth status
        $this->assertEquals( false, $authOK );
        $this->assertEquals( false, $this->auth->isLogin() );

        /** @var UserSimple $user */
        $user = $this->auth->getUser();
        $this->assertEquals( false, $user->is('bad') );

        // get loginInfo
        $loginInfo = $this->auth->getLoginInfo();
        $this->assertEmpty($loginInfo);

        // test what's saved in the session.
        $this->assertEquals(0, count($this->session));
    }

    /**
     * @test
     */
    function getAuth_fails_for_bad_pw()
    {
        $input  = $this->input(['user'=>'test','pass'=>'bad-PW','auth'=>'login']);
        $authOK = $this->auth->getAuth( $input );

        // test auth status
        $this->assertEquals( false, $authOK );
        $this->assertEquals( false, $this->auth->isLogin() );

        /** @var UserSimple $user */
        $user = $this->auth->getUser();
        $this->assertEquals( false, $user->is('bad') );

        // get loginInfo
        $loginInfo = $this->auth->getLoginInfo();
        $this->assertEmpty($loginInfo);

        // test what's saved in the session.
        $this->assertEquals(0, count($this->session));
    }
}