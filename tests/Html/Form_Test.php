<?php
namespace tests\EnumTest;

use WScore\Basic\Html\Form;

require_once( dirname( __DIR__ ) . '/autoload.php' );

class Form_Test extends \PHPUnit_Framework_TestCase
{
    function setup()
    {
        class_exists( 'WScore\Basic\Html\Form' );
    }

    function test0()
    {
        $form = Form::getElement();
        $this->assertEquals('WScore\Basic\Html\FormElement', get_class( $form ));
    }
}