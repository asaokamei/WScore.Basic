<?php
namespace WScore\Basic\Auth;

class Hash
{
    /**
     * @param string $value
     * @return bool|string
     */
    public static function make( $value )
    {
        if ( function_exists( 'password_hash' ) ) {
            return \password_hash( $value, PASSWORD_DEFAULT );
        } else {
            return \sha1( $value );
        }
    }

    /**
     * @param string $raw
     * @param string $hashed
     * @return bool
     */
    public static function verify( $raw, $hashed )
    {
        if ( substr( $hashed, 0, 1 ) !== '$' ) { // 文字列比較でチェック
            if ( $raw === $hashed ) return true;
        }
        if ( function_exists( 'password_verify' ) ) {
            return \password_verify( $raw, $hashed );
        }
        return $hashed === Hash::make( $raw );
    }

    /**
     * @param string $pw
     * @param string $raw
     * @return bool|string
     */
    public static function rehash( $pw, $raw=null )
    {
        if ( function_exists( 'password_needs_rehash' ) ) {
            return \password_needs_rehash( $pw, PASSWORD_DEFAULT );
        }
        return $pw !== Hash::make($raw);
    }
}