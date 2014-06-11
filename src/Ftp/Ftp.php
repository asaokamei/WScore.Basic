<?php
namespace WScore\Basic\Ftp;

use InvalidArgumentException;
use RuntimeException;

class Ftp
{
    /**
     * @var resource
     */
    public $conn;

    /**
     * host name to connect.
     *
     * @var string
     */
    public $host;

    /**
     * sets passive mode.
     *
     * @var bool
     */
    public $passive = false;

    /**
     * default transfer mode (binary).
     *
     * @var int
     */
    public $mode = FTP_BINARY;

    // +----------------------------------------------------------------------+
    //  construction and connection to ftp server.
    // +----------------------------------------------------------------------+
    /**
     * @param string $host
     * @throws InvalidArgumentException
     */
    public function connect( $host=null )
    {
        if( !$host ) $host = $this->host;
        if ( !$host ) {
            throw new InvalidArgumentException( "FTP not specified" );
        }
        $this->conn = ftp_connect( $host );
        if ( !$this->conn ) {
            throw new InvalidArgumentException( "cannot connect to FTP: " . $this->host );
        }
    }

    /**
     *
     */
    public function close()
    {
        if( $this->conn ) {
            ftp_close( $this->conn );
        }
    }
    /**
     * @param $user
     * @param $pass
     * @throws InvalidArgumentException
     */
    public function login( $user, $pass )
    {
        if ( !ftp_login( $this->conn, $user, $pass ) ) {
            throw new InvalidArgumentException( "cannot login to ftp server: " . $this->host );
        }
    }

    /**
     * Set the transfer method to passive mode
     *
     * @access public
     * @return void
     */
    function setPassive()
    {
        $this->passive = true;
        ftp_pasv( $this->conn, true );
    }

    // +----------------------------------------------------------------------+
    //  managing directories.
    // +----------------------------------------------------------------------+
    /**
     * @param $dir
     * @throws FtpActionException
     */
    public function cd( $dir )
    {
        if ( !ftp_chdir( $this->conn, $dir ) ) {
            throw new FtpActionException( 'ftp: cannot change dir: ' . $dir );
        }
    }

    /**
     * lists the contents of the directory.
     * files and directories staring with dot (.) are ignored.
     * returns array of file names.
     *
     * @param null $dir
     * @return array
     * @throws FtpActionException
     */
    public function ls( $dir = null )
    {
        $dir = $dir ? $dir : ftp_pwd( $this->conn );
        if ( !$dir ) {
            throw new FtpActionException( 'ftp: dir not specified for ls.' );
        }
        $dir_list = ftp_rawlist( $this->conn, $dir );
        $list     = array();
        foreach ( $dir_list AS $v ) {
            // ignore list starting with "total: ".
            if ( strncmp( $v, 'total: ', 7 ) == 0 ) {
                continue;
            }
            // ignore list not starting with '-'.
            // usually, 'd' for dir, 'l' for linked file.
            if( substr( $v, 0, 1 ) !== '-' ) {
                continue;
            }
            // find filename.
            $file = substr( $v, strrpos( $v, ' ' ) + 1 );
            if ( substr( $file, 0, 1 ) === '.' ) {
                // ignore files starting with dot(.).
                continue;
            }
            $list[ ] = $file;
        }
        return $list;
    }

    // +----------------------------------------------------------------------+
    //  managing files.
    // +----------------------------------------------------------------------+
    /**
     * @param $file
     * @return bool|string
     * @throws FtpActionException
     */
    public function getModifiedDate( $file )
    {
        if ( $unix_time = ftp_mdtm( $this->conn, $file ) === -1 ) {
            throw new FtpActionException( 'ftp: cannot get modified date for: ' . $file );
        }
        if( $unix_time === false ) {
            return $this->getRawMdtm( $file );
        }
        return date( 'Y-m-d H:i:s', $unix_time );
    }

    /**
     * @param $file
     * @return bool|string
     */
    public function getRawMdtm( $file )
    {
        $raw = ftp_raw( $this->conn, "MDTM $file" );
        if( substr( $raw[0], 0, 3 ) === '213' ) {
            $time = substr( $raw[0], 4 );
            if( $t = sscanf( $time, '%4d%2d%2d%2d%2d%2d' ) ) {
                $mdtm = "{$t[0]}-{$t[1]}-{$t[2]} {$t[3]}:{$t[4]}:{$t[5]}";
                return $mdtm;
            }
        }
        return false;
    }

    /**
     * @param $file
     * @throws RuntimeException
     */
    public function rm( $file )
    {
        if ( !ftp_delete( $this->conn, $file ) ) {
            throw new RuntimeException( 'ftp: cannot remove file: ' . $file );
        }
    }

    /**
     * @param $remote_file
     * @param null $local_file
     * @throws RuntimeException
     */
    public function get( $remote_file, $local_file )
    {
        if ( !ftp_get( $this->conn, $local_file, $remote_file, $this->mode ) ) {
            throw new RuntimeException( 'ftp: cannot get remote file: ' . $remote_file );
        }
    }

    /**
     * @param $local_file
     * @param null $remote_file
     * @throws RuntimeException
     */
    public function put( $local_file, $remote_file )
    {
        if ( !ftp_put( $this->conn, $remote_file, $local_file, $this->mode ) ) {
            throw new RuntimeException( 'ftp: cannot put local file: ' . $local_file );
        }
    }
    // +----------------------------------------------------------------------+
}