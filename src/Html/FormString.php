<?php
namespace WScore\Basic\Html;

/**
 * Class FormString
 *
 * @package WScore\Html
 */
class FormString
{
    /**
     * @var FormString
     */
    static $self;

    /**
     * @return FormString
     */
    public static function getInstance()
    {
        if( !static::$self ) {
            static::$self = new self();
        }
        return static::$self;
    }

    /**
     * @param FormElement $element
     * @return string
     */
    public function toString( $element )
    {
        $tag = $element->getTagName();
        if( $tag == 'input' ) {
            return $this->inputToString($element);
        }
        if( $tag == 'select' ) {
            return $this->select($element);
        }
        return $this->tagToString($element);
    }
    
    /**
     * @param FormElement $element
     * @return string
     */
    protected function inputToString( $element )
    {
        if( in_array( $element->getType(), ['radio', 'checkbox'] ) ) {
            if( $element->getList() ) {
                if( $element->getType() == 'checkbox' ) {
                    $element->asArray();
                }
                return $this->lists( $element );
            }
        }
        return $this->input( $element );
    }
    
    /**
     * @param FormElement $element
     * @return string
     */
    protected function input( $element )
    {
        $html = $this->htmlProperty( $element, 'type', 'name', 'value', 'id', 'class', 'style' );
        $html = '<input ' . $html . ' />' . "\n";
        $html = $this->addLabel( $html, $element );
        return $html;
    }

    /**
     * @param FormElement $element
     * @return string
     * @internal param $type
     * @internal param $name
     * @internal param $id
     */
    protected function htmlProperty( $element )
    {
        $args = func_get_args();
        array_shift( $args );
        $property = [];
        foreach( $args as $key ) {
            $getter = 'get' . ucwords( $key );
            if( method_exists( $element, $getter ) ) {
                $value = $element->$getter();
            } else {
                $value = $element->get( $key );
            }
            if( "$value"!="" ) {
                $property[] = $key . "=\"{$value}\"";
            }
        }
        if( $attribute = $element->getAttribute() ) {
            $property[] = $attribute;
        }
        $property = array_values( $property );
        $html = implode( ' ', $property );
        return $html;
    }

    /**
     * @param string $html
     * @param FormElement $element
     * @return string
     */
    protected function addLabel( $html, $element )
    {
        if( $label = $element->getLabel() ) {
            $html = "<label>{$html} {$label}</label>";
        }
        return $html;
    }

    /**
     * @param FormElement $element
     * @return string
     */
    protected function lists( $element )
    {
        $lists = $element->getList();
        $checkedValue = $element->getValue();
        $element->lists(null);
        $html  = '';
        foreach( $lists as $value => $label ) {
            if( $checkedValue == $value ) {
                $html .= '  <li>'.$element->value($value)->label($label)->checked()->toString() . "</li>\n";
            } else {
                $html .= '  <li>'.$element->value($value)->label($label)->checked(false)->toString() . "</li>\n";
            }
        }
        if( $html ) {
            $html = "<ul>\n{$html}</ul>";
        }
        return $html;
    }


    /**
     * @param FormElement $element
     * @return string
     */
    protected function select( $element )
    {
        $lists = $element->getList();
        $selectedValue = $element->getValue();
        $element->lists(null);
        $html  = '';
        foreach( $lists as $value => $label ) {
            if( $selectedValue == $value ) {
                $html .= "  <option value=\"{$value}\" selected>{$label}</option>\n";
            } else {
                $html .= "  <option value=\"{$value}\">{$label}</option>\n";
            }
        }
        if( $html ) {
            $prop = $this->htmlProperty( $element, 'name', 'id', 'class', 'style' );
            $html = "<select {$prop}>" . "\n" . $html . '</select>';
        }
        $html = $this->addLabel( $html, $element );
        return $html;
    }

    /**
     * @param FormElement $element
     * @return string
     */
    protected function tagToString( $element )
    {
        $tag  = $element->getTagName();
        $value = $element->getValue();
        $prop = $this->htmlProperty( $element, 'name', 'id', 'class', 'style' );
        $html = "<{$tag} " . "{$prop}>{$value}</{$tag}>";
        return $html;
    }
}