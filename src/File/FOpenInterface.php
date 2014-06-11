<?php
namespace WScore\Basic\File;

interface FOpenInterface
{
    /**
     * get the resource.
     *
     * @return resource
     */
    public function fp();

    /**
     * opens the resource.
     *
     * @param string $file
     * @param string $mode
     * @return $this
     */
    public function open( $file, $mode=null );

    /**
     * close the file resource.
     */
    public function close();

    /**
     * rewinds the resource, removes BOM if exists.
     *
     * @return $this
     */
    public function rewind();
}