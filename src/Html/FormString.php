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
     * @param FormElement $element
     * @return string
     */
    public function toString( $element )
    {
        if( in_array( $element->getType(), ['radio', 'checkbox'] ) ) {
            if( $element->getList() ) {
                return $this->lists( $element );
            }
        }
        if( $element->getType() == 'select' ) {
            return $this->select( $element );
        }
        return $this->input( $element );
    }
    
    /**
     * @param FormElement $element
     * @return string
     */
    public function input( $element )
    {
        $all = [
            $element->getType(),
            $element->getName(),
            $element->getId(),
            $element->getClass(),
            $element->getAttribute(),
            $element->getStyle(),
        ];
        $html = '<' . implode( ' ', $all ) . ' />' . "\n";
        $html = $this->addLabel( $html, $element );
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
    public function lists( $element )
    {
        $lists = $element->getList();
        $html  = '';
        foreach( $lists as $value => $label ) {
            $html .= '  <li>'.$element->value($value)->label($label)->toString() . "</li>\n";
        }
        if( $html ) {
            $html = "<ul>\n{$html}</ul>";
        }
        $html = $this->addLabel( $html, $element );
        return $html;
    }


    /**
     * @param FormElement $element
     * @return string
     */
    public function select( $element )
    {
        $lists = $element->getList();
        $html  = '';
        foreach( $lists as $value => $label ) {
            $html .= '  <option value="'.$element->value($value).'">'.$element->getlabel() . "</option>\n";
        }
        if( $html ) {
            $all = [
                $element->getName(),
                $element->getId(),
                $element->getClass(),
                $element->getAttribute(),
                $element->getStyle(),
            ];
            $html = '<select ' . implode( ' ', $all ) . '>' . "\n" . $html . '</select>';
        }
        $html = $this->addLabel( $html, $element );
        return $html;
    }
}