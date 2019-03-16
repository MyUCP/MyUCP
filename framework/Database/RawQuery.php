<?php

namespace MyUCP\Database;

class RawQuery
{
    /**
     * @var string
     */
    protected $query;

    /**
     * RawQuery constructor.
     *
     * @param $query
     */
    public function __construct($query)
    {
        $this->query = $query;
    }

    /**
     * @param $query
     *
     * @return RawQuery
     */
    public static function raw($query)
    {
        return new self($query);
    }

    /**
     * @param string $query
     *
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
