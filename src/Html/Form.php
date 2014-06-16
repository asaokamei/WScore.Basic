<?php
namespace WScore\Basic\Html;

/**
 * Class Form
 * @package WScore\Basic\Html
 *
 * @method static FormElement text( $name = null, $value = null, $option = [ ] )
 * @method static FormElement hidden( $name = null, $value = null, $option = [ ] )
 * @method static FormElement search( $name = null, $value = null, $option = [ ] )
 * @method static FormElement tel( $name = null, $value = null, $option = [ ] )
 * @method static FormElement url( $name = null, $value = null, $option = [ ] )
 * @method static FormElement email( $name = null, $value = null, $option = [ ] )
 * @method static FormElement password( $name = null, $value = null, $option = [ ] )
 * @method static FormElement datetime( $name = null, $value = null, $option = [ ] )
 * @method static FormElement date( $name = null, $value = null, $option = [ ] )
 * @method static FormElement month( $name = null, $value = null, $option = [ ] )
 * @method static FormElement week( $name = null, $value = null, $option = [ ] )
 * @method static FormElement time( $name = null, $value = null, $option = [ ] )
 * @method static FormElement number( $name = null, $value = null, $option = [ ] )
 * @method static FormElement range( $name = null, $value = null, $option = [ ] )
 * @method static FormElement color( $name = null, $value = null, $option = [ ] )
 * @method static FormElement file( $name = null, $value = null, $option = [ ] )
 * @method static FormElement radio( $name = null, $value = null, $option = [ ] )
 * @method static FormElement checkbox( $name = null, $value = null, $option = [ ] )
 * @method static FormElement select( $name = null, $value = null, $option = [ ] )
 * @method static FormElement textArea( $name = null, $value = null, $option = [ ] )
 */
class Form
{
    /**
     * @var FormElement
     */
    static $element;

    /**
     * @param FormString $toString
     * @return FormElement
     */
    static function forgeElement( $toString=null )
    {
        if( !$toString ) $toString = new FormString();
        return new FormElement( $toString );
    }

    /**
     * always return a brand new FormElement. 
     * 
     * @return FormElement
     */
    static function getElement()
    {
        if( !static::$element ) {
            static::$element = static::forgeElement();
        }
        return static::$element;
    }

    /**
     * @param string $method
     * @param array $args
     * @return FormElement
     */
    static function __callStatic( $method, $args )
    {
        $element = clone( static::getElement() );
        $element->type( $method );

        if( $arg = array_shift( $args ) ) {
            $element->name( $arg );
        }
        if( $arg = array_shift( $args ) ) {
            $element->value( $arg );
        }
        if( $arg = array_shift( $args ) ) {
            static::apply( $element, $arg );
        }
        return $element;
    }

    /**
     * @param FormElement $element
     * @param array $options
     */
    static function apply( $element, $options )
    {
        foreach( $options as $key => $value ) {
            if( is_numeric( $key ) ) {
                $element->$value();
            } else {
                $element->$key( $value );
            }
        }
    }
}