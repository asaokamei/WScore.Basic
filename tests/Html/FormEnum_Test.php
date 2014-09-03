<?php
namespace tests\EnumTest;

use WScore\Basic\Enum\EnumAbstract;
use WScore\Basic\Html\Form;
use WScore\Basic\Html\FormElement;

require_once( dirname( __DIR__ ) . '/autoload.php' );

class EnumStatus extends EnumAbstract
{
    const OK   = '1';
    const BAD  = '-1';
    const NONE = '0';

    static $choices = [
        self::OK   => 'Great!',
        self::NONE => 'Just OK',
        self::BAD  => 'Error',
    ];
}

class FormEnum_Test extends \PHPUnit_Framework_TestCase
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
    function radio_list_using_enum()
    {
        $enum = new EnumStatus( EnumStatus::OK );
        /** @var FormElement $form */
        $form = Form::radio( 'enum', $enum );
        $this->assertEquals( "enum-1", $form->getId() );
        $html = $form->toString();
        $this->assertEquals(
            "<ul>\n" .
            "  <li><label><input type=\"radio\" name=\"enum\" value=\"1\" id=\"enum-1\" checked />\n" .
            " Great!</label></li>\n" .
            "  <li><label><input type=\"radio\" name=\"enum\" value=\"0\" id=\"enum-0\" />\n" .
            " Just OK</label></li>\n" .
            "  <li><label><input type=\"radio\" name=\"enum\" value=\"-1\" id=\"enum--1\" />\n" .
            " Error</label></li>\n" .
            "</ul>", $html );
    }

    /**
     * @test
     */
    function select_using_enum()
    {
        $enum = new EnumStatus( EnumStatus::OK );
        /** @var FormElement $form */
        $form = Form::select( 'enum', $enum );
        $this->assertEquals( "enum", $form->getId() );
        $html = $form->toString();
        $this->assertEquals( '<select name="enum" id="enum">
  <option value="1" selected>Great!</option>
  <option value="0">Just OK</option>
  <option value="-1">Error</option>
</select>', $html );
    }

}