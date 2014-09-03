<?php
namespace WScore\Basic\Html;

class Html
{
    /**
     * @param string|array $html
     * @return array|string
     */
    public static function safe($html)
    {
        $safe = function($s) {
            if( is_string($s) ) {
                return htmlspecialchars( $s, ENT_QUOTES, 'UTF-8' );
            }
            return $s;
        };
        if( is_array($html)) {
            array_walk_recursive( $html, $safe );
            return $html;
        }
        return $safe($html);
    }
}