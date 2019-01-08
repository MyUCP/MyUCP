<?php
/*
* MyUCP
*/

class RawQuery
{
    /**
     * @var string
     */
    protected $query;

    /**
     * RawQuery constructor.
     * @param $query
     */
    public function __construct($query)
    {
        $this->query = $query;
    }

    /**
     * @param string $query
     * @return $this
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getQuery();
    }
}