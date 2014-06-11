<?php
namespace WScore\Basic\File;

class OpenForRead extends FOpenAbstract
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
     * @param null   $file
     * @param string $mode
     */
    public function __construct( $file=null, $mode='rb' )
    {
        if( $file ) {
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
     * open a file.
     *
     * @param string $file
     * @param string $mode
     * @return $this
     * @throws \RuntimeException
     */
    public function open( $file, $mode=null )
    {
        if( !$mode ) $mode = 'rb';
        return parent::open( $file, $mode );
    }

    /**
     * re-opens file contents as UTF-8.
     * may use a lot of memory.
     *
     * @param string $from
     * @param string $to
     * @return $this
     */
    public function reOpenAsUtf8( $from, $to='UTF-8' )
    {
        // get all contents.
        $this->rewind();
        $data = stream_get_contents( $this->fp );
        fclose( $this->fp );

        // convert to the new charset and store it in the memory.
        $data = mb_convert_encoding( $data, $to, $from );
        $this->fp   = fopen( 'php://temp', 'r+' );
        fwrite( $this->fp, $data );
        rewind( $this->fp );
        return $this;
    }

    /**
     * re-opens as a temp file as UTF-8 contents.
     * may slower but use less memory compared to reOpenAsUtf8.
     *
     * @param string $from
     * @param string $to
     * @return $this
     */
    public function tempAsUtf8( $from, $to='UTF-8' )
    {
        $this->rewind();
        $tempFp   = tmpfile();
        while( $text = fgets( $this->fp ) ) {
            fwrite( $tempFp, mb_convert_encoding( $tempFp, $to, $from ) );
        }
        fclose( $this->fp );
        $tempFp->fp = $tempFp;
        return $this;
    }

    /**
     *
     */
    public function close()
    {
        if( $this->fp ) {
            fclose( $this->fp );
        }
    }
}