<?php
namespace WScore\Basic\File;

/**
 * Class Upload
 * @package Cpe\Admin
 */
class Upload
{
    const FILE_LOC = 'file-loc';

    protected $name;

    protected $config;

    public function __construct()
    {
    }

    /**
     * @param string $name
     * @param string $idx
     */
    public function open( $name, $idx = null )
    {
        if ( !is_null( $idx ) ) {
            throw new \RuntimeException( "idx not supported" );
            /** @noinspection PhpUnreachableStatementInspection */
            if ( !isset( $_FILES[ $name ][ $idx ] ) ) {
                throw new \RuntimeException( "no such file: {$name}[{$idx}]" );
            }
            $this->config = $_FILES[ $name ][ $idx ];
        }
        if ( !isset( $_FILES[ $name ] ) ) {
            throw new \RuntimeException( "no such file: {$name}" );
        }
        $this->name   = $name;
        $this->config = $_FILES[ $name ];
        $this->config[ self::FILE_LOC ] = $this->config[ 'tmp_name' ];
        if ( $this->passes() && !is_uploaded_file( $this->getFileName() ) ) {
            throw new \RuntimeException( 'not a download file: ' . $name );
        }
    }

    /**
     * @return int
     */
    public function getErrorCode()
    {
        return $this->config[ 'error' ];
    }

    /**
     * @return bool
     */
    public function fails()
    {
        return $this->getErrorCode() != UPLOAD_ERR_OK;
    }

    /**
     * @return bool
     */
    public function passes()
    {
        return $this->getErrorCode() == UPLOAD_ERR_OK;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->config[ self::FILE_LOC ];
    }

    /**
     * @param string $key
     * @return null|string
     */
    public function getConfig( $key )
    {
        if ( isset( $this->config[ $key ] ) ) {
            return htmlspecialchars( $this->config[ $key ], ENT_QUOTES, 'UTF-8' );
        }
        return null;
    }

    /**
     * @param string $moveTo
     */
    public function move( $moveTo )
    {
        $from = $this->getFileName();
        if ( !move_uploaded_file( $from, $moveTo ) ) {
            throw new \RuntimeException( 'cannot move file to: ' . $moveTo );
        }
        $this->config[ self::FILE_LOC ] = $moveTo;
    }
}