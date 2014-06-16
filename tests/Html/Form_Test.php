<?php
namespace tests\EnumTest;

use WScore\Basic\Html\Form;
use WScore\Basic\Html\FormElement;

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

    /**
     * @param string $type
     * @param string $name
     * @param string $value
     * @param array  $option
     * @return FormElement
     */
    function getElement( $type, &$name, &$value, $option=array() )
    {
        $name = 'name-'.mt_rand(1000,9999);
        $value = 'type-'.mt_rand(1000,9999);
        $form = Form::$type( $name, $value, $option );
        return $form;
    }

    /**
     * @test
     */
    function static_method_mail_returns_element_type_mail()
    {
        /** @var FormElement $form */
        $form = Form::email();
        $this->assertEquals('WScore\Basic\Html\FormElement', get_class( $form ));
        $this->assertEquals( 'email', $form->getType() );
        $this->assertEquals( null, $form->getName() );
        $this->assertEquals( null, $form->getValue() );
    }

    /**
     * @test
     */
    function static_method_text_with_name_and_value()
    {
        $form = $this->getElement( 'text', $name, $value );
        $this->assertEquals( 'text', $form->getType() );
        $this->assertEquals( $name, $form->getName() );
        $this->assertEquals( $value, $form->getValue() );
    }

    /**
     * @test
     */
    function static_method_integer_with_options()
    {
        $form = $this->getElement( 'integer', $name, $value, ['class'=>'test-class', 'style'=>'test-style']  );
        $this->assertEquals( 'integer', $form->getType() );
        $this->assertEquals( $name, $form->getName() );
        $this->assertEquals( $value, $form->getValue() );
        $this->assertEquals( 'test-class', $form->getClass() );
        $this->assertEquals( 'test-style', $form->getStyle() );
    }

    /**
     * @test
     */
    function method_in_dot_is_ignored_as_in_class()
    {
        $form = $this->getElement( 'password', $name, $value, ['class.1'=>'test-class', 'style.2'=>'test-style']  );
        $this->assertEquals( 'password', $form->getType() );
        $this->assertEquals( $name, $form->getName() );
        $this->assertEquals( $value, $form->getValue() );
        $this->assertEquals( 'test-class', $form->getClass() );
        $this->assertEquals( 'test-style', $form->getStyle() );
    }

    /**
     * @test
     */
    function getId_return_id()
    {
        $form = $this->getElement( 'text', $name, $value, [ 'id'=>'test-id'] );
        $this->assertEquals( 'text', $form->getType() );
        $this->assertEquals( $name, $form->getName() );
        $this->assertEquals( 'test-id', $form->getId() );
    }

    /**
     * @test
     */
    function getId_return_the_name_if_not_set()
    {
        $form = $this->getElement( 'text', $name, $value );
        $this->assertEquals( 'text', $form->getType() );
        $this->assertEquals( $name, $form->getName() );
        $this->assertEquals( $name, $form->getId() );
    }
}