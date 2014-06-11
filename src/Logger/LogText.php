<?php

/**
 * A really simple logger class which writes messages to files.
 * probably good for logging events happening once a day.
 *
 * Class LogText
 */
class LogText
{
    /**
     * directory to save logs.
     * @var string
     */
    public $dir = '.';

    /**
     * file name for successful logs.
     * @var string
     */
    public $success_file = 'success.log';

    /**
     * specify the directory to store the log files.
     *
     * @param null $dir
     */
    public function __construct( $dir=null )
    {
        if( $dir ) $this->dir = $dir;
        if( substr( $this->dir, -1 ) !== '/' ) {
            $this->dir .= '/';
        }
    }

    /**
     * general log method.
     *
     * @param $file
     * @param $message
     */
    protected function write( $file, $message )
    {
        $file = $this->dir . $file;
        $message = date('Ymd-His') . '|' . $message . "\n";
        file_put_contents( $file, $message, FILE_APPEND|LOCK_EX );
    }

    /**
     * log normal (i.e. success) message.
     *
     * @param $message
     */
    public function info( $message )
    {
        $this->write( $this->success_file, $message );
    }

    /**
     * log log error message.
     * error log files are created as 'error-Ymd.log'.
     *
     * @param $message
     */
    public function error( $message )
    {
        $file = 'error-'.date('Ymd').'.log';
        $this->write( $file, $message );
    }
}