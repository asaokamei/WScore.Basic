<?php
namespace WScore\Basic\File;

class File
{
    /**
     * get file statistics.
     *
     * @param $file
     * @return array|bool
     */
    static function stat( $file )
    {
        $stat = FALSE;
        if( file_exists( $file ) ) {
            $stat = lstat( $file );
            $stat[ 'acc_time' ] = date( 'Y-m-d H:i:s', $stat[ 'atime' ] );
            $stat[ 'mod_time' ] = date( 'Y-m-d H:i:s', $stat[ 'mtime' ] );
            $stat[ 'chg_time' ] = date( 'Y-m-d H:i:s', $stat[ 'ctime' ] );
            $stat[ 'size_kb' ]  = sprintf( '%0.0f', $stat[ 'size' ] / 1024 );
            $stat[ 'size_mb' ]  = sprintf( '%0.2f', $stat[ 'size' ] / 1024 / 1024 );
            $stat[ 'size_gb' ]  = sprintf( '%0.2f', $stat[ 'size' ] / 1024 / 1024 / 1024 );
        }
        return $stat;
    }

    /**
     * opens a temporary file, and returns its file pointer.
     *
     * @return resource
     */
    static function openTemp()
    {
        $fp = tmpfile();
        return new OpenForWrite( $fp );
    }

    /**
     * @param string $file
     * @param string $mode
     * @return OpenForRead
     * @throws \RuntimeException
     */
    static function openForRead( $file, $mode=null )
    {
        if( !file_exists( $file ) ) {
            throw new \RuntimeException( "cannot find file: " . $file );
        }
        return new OpenForRead( $file, $mode );
    }

    /**
     * @param string $file
     * @param string $mode
     * @return OpenForWrite
     * @throws \RuntimeException
     */
    static function openForLock( $file, $mode=null )
    {
        if( !file_exists( $file ) ) {
            throw new \RuntimeException( "cannot find file: " . $file );
        }
        return new OpenForWrite( $file, $mode );
    }

    /**
     * @param string $file
     * @param string $from
     * @return Csv
     */
    static function openCsv( $file, $from=null )
    {
        $fp = self::openForRead( $file );
        if( $from ) {
            $fp->reOpenAsUtf8( $from );
        }
        return new Csv( $fp );
    }
}