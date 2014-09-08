<?php
namespace tests\Basic\Auth;

use WScore\Basic\Auth\Hash;

require_once( dirname( __DIR__ ) . '/autoload.php' );

class Hash_Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    function hash_make_hashes_value_and_can_be_verified()
    {
        $value = 'test';
        $hashed = Hash::make($value);
        $this->assertNotEquals( $value, $hashed );
        $this->assertTrue( Hash::verify( $value, $hashed ) );
    }

    /**
     * @test
     */
    function verify_with_raw_text()
    {
        $value = 'pwd-test';
        $this->assertTrue( Hash::verify( $value, $value ) );
    }

    /**
     * @test
     */
    function verify_with_raw_text_starting_dollar_fails()
    {
        $value = '$pwd-test';
        $this->assertFalse( Hash::verify( $value, $value ) );
    }

    /**
     * @test
     */
    function rehash_fails_for_raw_input()
    {
        $value = 'raw-pwd';
        $this->assertTrue( Hash::rehash( $value, $value ) );
    }
}
