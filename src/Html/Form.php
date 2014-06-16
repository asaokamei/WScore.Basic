<?php
namespace WScore\Basic\Html;

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
            if( is_numeric( $key ) ) continue;
            $element->$key( $value );
        }
    }
}