<?php
namespace WScore\Basic\Ftp;

use InvalidArgumentException;

class Sync
{
    /**
     * @var Ftp
     */
    public $ftp;

    /**
     * @var string
     */
    public $root_dir;

    /**
     * @var
     */
    public $curr_dir;

    /**
     * @param Ftp $ftp
     */
    public function __construct( $ftp )
    {
        $this->ftp = $ftp;
    }

    /**
     * @param $dir
     * @throws InvalidArgumentException
     */
    public function setRootDir( $dir )
    {
        if( !is_dir( $dir ) ) {
            throw new InvalidArgumentException('FtpSync: cannot change dir to: '.$dir);
        }
        $this->root_dir = $this->dirSlash( $dir );
        $this->curr_dir = $this->root_dir;
    }

    /**
     * @param $dir
     * @return string
     */
    protected function dirSlash( $dir )
    {
        if( substr( $dir, -1, 1 ) !== '/' ) {
            $dir .= '/';
        }
        return $dir;
    }

    /**
     * @param $dir
     * @throws InvalidArgumentException
     */
    public function cd( $dir )
    {
        $this->ftp->cd( $dir );
        if( substr( $dir, 0, 1 ) === '/' ) {
            $this->curr_dir = $this->root_dir;
        } else {
            $this->curr_dir .= $dir;
        }
        $this->curr_dir = realpath( $this->curr_dir );
        $this->curr_dir = $this->dirSlash( $this->curr_dir );
        if( strlen( $this->curr_dir ) < strlen( $this->root_dir ) ) {
            throw new InvalidArgumentException( 'FtpSync: cannot cd to directory above the root. ' );
        }
    }

    /**
     * @param $file
     */
    public function get( $file )
    {
        $local_file = $this->curr_dir . $file;
        $this->ftp->get( $file, $local_file );
    }

    /**
     *
     */
    public function getAll()
    {
        $list = $this->ftp->ls();
        foreach( $list as $file ) {
            $this->get( $file );
        }
    }

    /**
     * @param $file
     */
    public function put( $file )
    {
        $local_file = $this->curr_dir . $file;
        $this->ftp->put( $local_file, $file );
    }

    /**
     *
     */
    public function putAll()
    {
        $list = $this->lsLocal();
        foreach( $list as $file ) {
            $this->put( $file );
        }
    }

    /**
     * @return array
     */
    public function lsLocal()
    {
        $list = array();
        $dir  = dir( $this->curr_dir );
        while( false !== ( $entry = $dir->read() ) ) {
            if( substr( $entry, 0, 1 ) === '.' ) continue;
            if( !is_file( $this->curr_dir . $entry ) ) continue;
            $list[] = $entry;
        }
        return $list;
    }

    /**
     * @return array
     */
    public function lsRemote()
    {
        return $this->ftp->ls();
    }

    /**
     *
     */
    public function clearRemote()
    {
        $list = $this->lsRemote();
        foreach( $list as $file ) {
            $this->ftp->rm( $file );
        }
    }

    /**
     *
     */
    public function clearLocal()
    {
        $list = $this->lsLocal();
        foreach( $list as $local_file ) {
            unlink( $this->curr_dir . $local_file );
        }
    }
}