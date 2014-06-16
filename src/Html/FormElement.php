<?php
namespace WScore\Basic\Html;

use WScore\Basic\Enum\EnumInterface;

/**
 * Class FormElement
 *
 * @package WScore\Html
 */
class FormElement
{
    /**
     * @var FormString
     */
    protected $toString;
    
    protected $type = 'text';
    
    protected $name;
    
    protected $value;
    
    protected $id;
    
    protected $label;
    
    protected $class = array();
    
    protected $style = array();
    
    protected $attributes = array();
    
    protected $list = array();

    // +----------------------------------------------------------------------+
    //  construction 
    // +----------------------------------------------------------------------+
    /**
     * @param FormString $string
     */
    public function __construct( $string )
    {
        $this->toString = $string;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @return string
     */
    public function toString() 
    {
        return $this->toString->toString( $this );
    }
    // +----------------------------------------------------------------------+
    //  setting up
    // +----------------------------------------------------------------------+
    /**
     * @param string $type
     * @return $this
     */
    public function type( $type ) {
        $this->type = strtolower( $type );
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function name( $name ) {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $id
     * @return $this
     */
    public function id( $id ) {
        $this->id = $id;
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function value( $value ) {
        if( $value instanceof EnumInterface ) {
            $this->enum( $value );
        } else {
            $this->value = $value;
        }
        return $this;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function label( $label ) {
        $this->label = $label;
        return $this;
    }

    /**
     * @param string $class
     * @return $this
     */
    public function class_( $class ) {
        if( $class === false ) {
            $this->class[] = array();
        } else {
            $this->class[] = $class;
        }
        return $this;
    }

    /**
     * @param string $key
     * @param string $style
     * @return $this
     */
    public function style( $key, $style=null ) {
        if( $key === false ) {
            $this->style[] = array();
        } elseif( $style ) {
            $this->style[] = "{$key}=\"$style\"";
        } else {
            $this->style[] = $key;
        }
        return $this;
    }

    /**
     * @param string $method
     * @param array  $args
     * @return $this
     */
    public function __call( $method, $args ) {
        $method = $this->cleanMethod( $method );
        if( $method === 'class' ) $method = 'class_';
        if( method_exists( $this, $method ) ) {
            return call_user_func_array( [$this,$method], $args );
        }
        return $this->setAttribute( $method, $args[0]);
    }

    /**
     * @param string $key
     * @param string $value
     * @return $this
     */
    protected function setAttribute( $key, $value ) {
        $this->attributes[$key] = $value;
        return $this;
    }

    protected function cleanMethod( $method ) {
        if( false === ( $pos = strpos( $method, '.' ) ) ) {
            return $method;
        }
        return substr( $method, 0, $pos );
    }

    /**
     * @param array $lists
     * @return $this
     */
    public function lists( $lists ) {
        $this->list = $lists;
        return $this;
    }

    /**
     * @param string $width
     * @return $this
     */
    public function width( $width ) {
        return $this->style( 'width', $width );
    }

    /**
     * @param string $height
     * @return $this
     */
    public function height( $height ) {
        return $this->style( 'height', $height );
    }

    /**
     * @param EnumInterface $enum
     * @return $this
     */
    public function enum( EnumInterface $enum ) {
        $this->lists( $enum::getChoices() );
        $this->value( $enum->get() );
        return $this;
    }
    // +----------------------------------------------------------------------+
    //  getting information
    // +----------------------------------------------------------------------+
    /**
     * @param string $key
     * @return null|string
     */
    public function get( $key ) {
        return array_key_exists( $key, $this->attributes ) ? $this->attributes[$key] : null;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getClass() {
        return implode( ' ', $this->class );
    }

    /**
     * @return string
     */
    public function getStyle() {
        return implode( '; ', $this->style );
    }

    /**
     * @return string
     */
    public function getAttribute() {
        $attribute = '';
        foreach( $this->attributes as $key => $val ) {
            $attribute .= $key."=\"{$val}\" ";
        }
        return $attribute;
    }

    /**
     * @return string
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * @return array
     */
    public function getList() {
        return $this->list;
    }
    
    public function getId() {
        if( $this->id ) return $this->id;
        if( in_array( $this->type, ['radio', 'checkbox'] ) ) {
            return $this->name . '-' . $this->value;
        }
        return $this->name;
    }
    // +----------------------------------------------------------------------+
}