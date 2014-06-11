<?php
namespace WScore\Basic\File;

use RuntimeException;

class OpenForWrite extends FOpenAbstract
{
    /**
     * @var string
     */
    public $file;

    /**
     * @var resource
     */
    public $fp = null;

    /**
     * @var bool
     */
    public $lock = false;

    /**
     * @param string|resource   $file
     * @param string $mode
     */
    public function __construct( $file=null, $mode=null )
    {
        if( is_resource( $file ) ) {
            $this->fp = $file;
        }
        elseif( $file ) {
            $this->open( $file, $mode );
        }
    }

    /**
     * @return resource
     */
    public function fp()
    {
        return $this->fp;
    }

    /**
     * open a file with lock.
     *
     * @param string $file
     * @param string $mode
     * @return $this
     * @throws RuntimeException
     */
    public function open( $file, $mode=null )
    {
        if( !$mode ) $mode = 'rb+';
        return parent::open( $file, $mode );
    }

    /**
     * locks this file.
     *
     * @return $this
     * @throws \RuntimeException
     */
    public function lock()
    {
        if( !flock( $this->fp, LOCK_EX ) ) {
            throw new RuntimeException( 'cannot lock file: '.$this->file );
        }
        rewind( $this->fp );
        $this->lock = true;
        return $this;
    }

    /**
     * close file pointer.
     * unlocks the file if locked.
     */
    public function close()
    {
        if( !$this->fp ) return;
        if( $this->lock ) {
            fflush( $this->fp );
            flock( $this->fp, LOCK_UN );
        }
        fclose( $this->fp );
        $this->fp = null;
    }

    /**
     *
     */
    public function __destruct()
    {
        $this->close();
    }
}