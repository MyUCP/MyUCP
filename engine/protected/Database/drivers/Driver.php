<?php

/**
 * MyUCP
 */
interface Driver
{
    public function __construct($options);
    public function query($sql);
    public function fetch($result, $mode);
    public function affected_rows();
    public function num_rows($result);
    public function free($result);
    public function escape($value);
    public function getLastId();
    public function getError();
    public function getErrno();
    public function close();
    public function __destruct();
}