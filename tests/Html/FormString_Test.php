<?php
namespace tests\EnumTest;

use WScore\Basic\Html\Form;
use WScore\Basic\Html\FormElement;

require_once( dirname( __DIR__ ) . '/autoload.php' );

class FormString_Test extends \PHPUnit_Framework_TestCase
{
    function setup()
    {
        class_exists( 'WScore\Basic\Html\Form' );
        class_exists( 'WScore\Basic\Html\FormElement' );
        class_exists( 'WScore\Basic\Html\FormString' );
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
        $value = 'value-'.mt_rand(1000,9999);
        $form = Form::$type( $name, $value, $option );
        return $form;
    }

    function test0()
    {
        $form = Form::getElement();
        $this->assertEquals('WScore\Basic\Html\FormElement', get_class( $form ));
    }

    /**
     * @test
     */
    function input_element()
    {
        $form = $this->getElement( 'text', $name, $value, ['required'] );
        $html = $form->toString();
        $this->assertEquals("<type=\"text\" name=\"$name\" value=\"$value\" id=\"$name\" required />\n", $html );
    }

    /**
     * @test
     */
    function radio_element()
    {
        $form = $this->getElement( 'radio', $name, $value );
        $this->assertEquals( "$name-$value", $form->getId() );
        $html = $form->toString();
        $this->assertEquals("<type=\"radio\" name=\"$name\" value=\"$value\" id=\"$name-$value\" />\n", $html );
    }

    /**
     * @test
     */
    function radio_element_with_list()
    {
        $form = $this->getElement( 'radio', $name, $value );
        $val2 = 'value-not';
        $list = [
            $value => 'Checked Value',
            $val2  => 'Not Checked',
        ];
        $form->lists( $list );
        $this->assertEquals( "$name-$value", $form->getId() );
        $html = $form->toString();
        $this->assertEquals(
            "<ul>\n" .
            "  <li><label><type=\"radio\" name=\"{$name}\" value=\"$value\" id=\"$name-$value\" checked />\n" .
            " Checked Value</label></li>\n" .
            "  <li><label><type=\"radio\" name=\"{$name}\" value=\"$val2\" id=\"$name-$val2\" />\n" .
            " Not Checked</label></li>\n" .
            "</ul>", $html );
    }

    /**
     * @test
     */
    function checkbox_element()
    {
        $form = $this->getElement( 'checkbox', $name, $value );
        $this->assertEquals( "$name-$value", $form->getId() );
        $html = $form->toString();
        $this->assertEquals("<type=\"checkbox\" name=\"$name\" value=\"$value\" id=\"$name-$value\" />\n", $html );
    }

    /**
     * @test
     */
    function checkbox_element_with_list()
    {
        $form = $this->getElement( 'checkbox', $name, $value );
        $val2 = 'value-not';
        $list = [
            $value => 'Checked Value',
            $val2  => 'Not Checked',
        ];
        $form->lists( $list );
        $this->assertEquals( "$name-$value", $form->getId() );
        $html = $form->toString();
        $this->assertEquals(
            "<ul>\n" .
            "  <li><label><type=\"checkbox\" name=\"{$name}[]\" value=\"$value\" id=\"$name-$value\" checked />\n" .
            " Checked Value</label></li>\n" .
            "  <li><label><type=\"checkbox\" name=\"{$name}[]\" value=\"$val2\" id=\"$name-$val2\" />\n" .
            " Not Checked</label></li>\n" .
            "</ul>", $html );
    }

    /**
     * @test
     */
    function select_element()
    {
        $form = $this->getElement( 'select', $name, $value );
        $val2 = 'value-not';
        $list = [
            $value => 'Selected Value',
            $val2  => 'Not Selected',
        ];
        $form->lists( $list );
        $html = $form->toString();
        $this->assertEquals( "<select name=\"$name\" id=\"$name\">\n" .
            "  <option value=\"$value\" selected>Selected Value</option>\n" .
            "  <option value=\"$val2\">Not Selected</option>\n" .
            "</select>", $html );
    }

    /**
     * @test
     */
    function textArea_element()
    {
        $form = $this->getElement( 'textArea', $name, $value );
        $html = $form->toString();
        $this->assertEquals( "<textarea name=\"$name\" id=\"$name\">$value</textarea>", $html );
    }
}