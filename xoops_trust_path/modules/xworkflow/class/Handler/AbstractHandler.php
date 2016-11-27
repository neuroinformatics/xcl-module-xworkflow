<?php

namespace Xworkflow\Handler;

/**
 * abstract handler.
 */
abstract class AbstractHandler
{
    /**
     * database object.
     *
     * @var \XoopsDatabase
     */
    protected $mDB;

    /**
     * dirname.
     *
     * @var string
     */
    protected $mDirname;

    /**
     * constractor.
     *
     * @param \XoopsDatabase &$db
     * @param string         $dirname
     */
    public function __construct(&$db, $dirname)
    {
        $this->mDB = &$db;
        $this->mDirname = $dirname;
    }
}
