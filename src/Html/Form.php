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
 * @method static FormElement submit( $value = null, $option = [ ] )
 * @method static FormElement reset( $value = null, $option = [ ] )
 * @method static FormElement button( $value = null, $option = [ ] )
 */
class Form
{
    /**
     * @var FormElement
     */
    static $element;

    protected static $types = array(
        'text', 'hidden', 'password', 'checkbox', 'radio',
        'color', 'date', 'datetime', 'datetime-local',
        'email', 'month', 'number', 'range',
        'search', 'tel', 'time', 'url',
        'week',
    );
    
    protected static $buttons = array(
        'submit', 'reset', 'button',
    );
    
    protected static $tags = array(
        'select', 'textarea',
    );
    
    /**
     * @param FormString $toString
     * @return FormElement
     */
    static function forgeElement( $toString=null )
    {
        return new FormElement( $toString );
    }

    /**
     * always return a brand new FormElement.
     *
     * @param string $type
     * @return FormElement
     */
    static function getElement( $type='input' )
    {
        if( in_array( $type, static::$types ) || 
            in_array( $type, static::$buttons ) ) {
            $element = new FormElement();
            $element->type($type);
            return $element;
        }
        $element = new FormElement($type);
        return $element;
    }

    /**
     * @param string $method
     * @param array $args
     * @return FormElement
     */
    static function __callStatic( $method, $args )
    {
        $element = static::getElement($method);

        if( in_array( $method, static::$buttons ) ) {
            return static::formButton( $element, $args );
        }
        return static::formInput( $element, $args );
    }

    /**
     * @param FormElement $element
     * @param array $args
     * @return FormElement
     */
    static function formInput( $element, $args )
    {
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
     * @param array $args
     * @return FormElement
     */
    static function formButton( $element, $args )
    {
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