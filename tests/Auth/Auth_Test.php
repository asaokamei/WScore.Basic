<?php
namespace tests\Basic\Auth;

use tests\Auth\Mocks\UserSimple;
use tests\Auth\Mocks\UserSimple2;
use WScore\Basic\Auth\Auth;
use WScore\Basic\Auth\Authenticate;
use WScore\Basic\Auth\UserInterface;

require_once( dirname( __DIR__ ) . '/autoload.php' );

class Auth_Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Auth
     */
    var $auth;

    /**
     * @var UserInterface
     */
    var $user1;

    /**
     * @var UserInterface
     */
    var $user2;

    var $user1Info = [ 'test' => 'test-PW' ];
    var $user2Info = [ 'more' => 'more-PW' ];

    function setup()
    {
        $this->init();
    }

    function init($session=array('dummy'=>'value'))
    {
        $auth = new Authenticate($session);
        $this->auth = Auth::forgeAuth( $auth );
        $this->user1 = new UserSimple( $this->user1Info );
        $this->user2 = new UserSimple2( $this->user2Info );
    }

    function input($input=array())
    {
        return Auth::forgeInput($input);
    }

    function getAuthSaveId()
    {
        return str_replace('\\','-','WScore\Basic\Auth\Authenticate');
    }

    function test0()
    {
        $this->assertEquals( 'WScore\Basic\Auth\Auth', get_class($this->auth) );
        $this->assertEquals( 'WScore\Basic\Auth\Input', get_class($this->input()) );
    }

    /**
     * @test
     */
    function getAuth_login_to_user1_by_input()
    {
        $input = $this->input(['auth'=>'login','user'=>'test','pass'=>'test-PW']);
        $this->auth->setUsers( $this->user1, $this->user2 );
        $auth = $this->auth->login( $input );
        $this->assertTrue( $auth->isLogin() );
        $this->assertEquals( 'WScore\Basic\Auth\Authenticate', get_class($auth) );
        $this->assertEquals( 'simple-user', $auth->getUser()->getUserTypeId() );
    }

    /**
     * @test
     */
    function getAuth_login_to_user2_by_input()
    {
        $input = $this->input(['auth'=>'login','user'=>'more','pass'=>'more-PW']);
        $this->auth->setUsers( $this->user1, $this->user2 );
        $auth = $this->auth->login( $input );
        $this->assertEquals( 'WScore\Basic\Auth\Authenticate', get_class($auth) );
        $this->assertTrue( $auth->isLogin() );
        $this->assertEquals( 'simple-more', $auth->getUser()->getUserTypeId() );
        $this->assertTrue( $auth->isLoginBy(Authenticate::BY_POST) );
    }

    /**
     * @test
     */
    function getAuth_login_to_user1_by_getSession()
    {
        // first, login to UserSimple.
        $input = $this->input(['auth'=>'login','user'=>'test','pass'=>'test-PW']);
        $this->auth->setUsers( $this->user1, $this->user2 );
        $auth = $this->auth->login( $input );
        $this->assertEquals( 'simple-user', $auth->getUser()->getUserTypeId() );
        $this->assertTrue( $auth->isLogin() );
        $this->assertTrue( $auth->isLoginBy(Authenticate::BY_POST) );

        // set loginInfo to session
        $saved = $auth->getLoginInfo();
        $saveID= $this->getAuthSaveId();

        // try login without session.
        $this->init();
        $this->auth->setUsers( $this->user1, $this->user2 );
        $auth = $this->auth->login();
        $this->assertFalse( $auth ); // login fails!

        // set loginInfo to session
        $this->init([$saveID => $saved ]);

        $this->auth->setUsers( $this->user1, $this->user2 );
        $auth = $this->auth->login();
        $this->assertEquals( 'simple-user', $auth->getUser()->getUserTypeId() );
        $this->assertTrue( $auth->isLoginBy(Authenticate::BY_POST) );
    }

    /**
     * @test
     */
    function getAuth_login_to_user2_by_getSession()
    {
        // first, login to UserSimple.
        $input = $this->input(['auth'=>'login','user'=>'more','pass'=>'more-PW']);
        $this->auth->setUsers( $this->user1, $this->user2 );
        $auth = $this->auth->login( $input );
        $this->assertEquals( 'simple-more', $auth->getUser()->getUserTypeId() );
        $this->assertTrue( $auth->isLogin() );
        $this->assertTrue( $auth->isLoginBy(Authenticate::BY_POST) );

        // set loginInfo to session
        $saved = $auth->getLoginInfo();
        $saveID= $this->getAuthSaveId();

        // try login without session.
        $this->init();
        $this->auth->setUsers( $this->user1, $this->user2 );
        $auth = $this->auth->login();
        $this->assertFalse( $auth ); // login fails!

        // set loginInfo to session
        $this->init([$saveID => $saved ]);

        $this->auth->setUsers( $this->user1, $this->user2 );
        $auth = $this->auth->login();
        $this->assertEquals( 'simple-more', $auth->getUser()->getUserTypeId() );
        $this->assertTrue( $auth->isLoginBy(Authenticate::BY_POST) );
    }


    /**
     * @test
     */
    function static_user_to_start_chain()
    {
        $auth = Auth::user($this->user1, $this->user2)
            ->login(['auth'=>'login','user'=>'more','pass'=>'more-PW']);
        $this->assertEquals( 'WScore\Basic\Auth\Authenticate', get_class($auth) );
        $this->assertTrue( $auth->isLogin() );
        $this->assertEquals( 'simple-more', $auth->getUser()->getUserTypeId() );
        $this->assertTrue( $auth->isLoginBy(Authenticate::BY_POST) );

        $auth2 = Auth::auth();
        $this->assertSame( $auth, $auth2 );
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    function setting_no_users_will_throw_exception()
    {
        Auth::user();
    }
}
