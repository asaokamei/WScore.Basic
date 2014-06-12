<?php
namespace tests\Basic\Auth;

use WScore\Basic\Auth\Input;

require_once( dirname( __DIR__ ) . '/autoload.php' );

class Input_Test extends \PHPUnit_Framework_TestCase
{
    var $input;

    function i($input=array())
    {
        return new Input($input);
    }

    function test0()
    {
        $this->assertEquals( 'WScore\Basic\Auth\Input', get_class($this->i()) );
    }

    /**
     * @test
     */
    function getId_returns_id()
    {
        $input = $this->i( array('user'=>'test-ID','auth'=>'login') );
        $this->assertEquals( 'test-ID', $input->getId() );
    }
}
