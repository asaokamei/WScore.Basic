<?php
namespace WScore\Basic\File;

use InvalidArgumentException;

abstract class FOpenAbstract implements FOpenInterface
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
     * open a file.
     *
     * @param string $file
     * @param string $mode
     * @throws InvalidArgumentException
     * @return $this
     */
    public function open( $file, $mode=null )
    {
        if( !file_exists( $file ) ) {
            throw new InvalidArgumentException( "cannot find file: " . $file );
        }
        $this->file = $file;
        $this->fp   = fopen( $file, $mode );
        return $this;
    }

    /**
     * rewinds the resource. removes BOM if exists.
     */
    public function rewind()
    {
        rewind($this->fp);
        if( $bom = fread( $this->fp, 3 ) ) {
            if( 0 !== strncmp( $bom, pack('CCC', 0xEF, 0xBB, 0xBF ), 3 ) ) {
                rewind( $this->fp );
            }
        }
    }
}