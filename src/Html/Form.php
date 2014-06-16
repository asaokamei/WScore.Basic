<?php
namespace WScore\Basic\Html;

use WScore\Basic\Enum\EnumInterface;

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
     * @param array  $args
     * @return FormElement
     */
    static function __callStatic( $method, $args )
    {
        $element = clone( static::getElement() );
        $element->type( $method );
        if( isset( $args[0] ) ) {
            $element->name( $args[0] );
        }
        static::apply( $element, $args );
        return $element;
    }

    /**
     * @param string $name
     * @param mixed  $value
     * @param array  $options
     * @return FormElement
     */
    static function get( $name, $value, $options )
    {
        $element = clone( static::getElement() );
        if( $value instanceof EnumInterface ) {
            $element->enum( $value );
        } else {
            $element->value( $value );
        }
        $element->name( $name );
        static::apply( $element, $options );
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