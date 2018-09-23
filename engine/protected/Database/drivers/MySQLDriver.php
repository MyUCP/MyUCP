<?php
/*
* MyUCP
*/

final class MySQLDriver implements Driver
{
    /**
     * @var mysqli
     */
	private $connection;

    /**
     * mysqlDriver constructor.
     * @param $options
     * @throws Debug
     */
	public function __construct($options)
	{
	    if(!$this->tryConnect($options)) {
	        throw new DebugException(mysqli_connect_errno()." ".mysqli_connect_error(), "1");
        }

        $this->setCharset($options['charset']);
  	}

    /**
     * Try to connect
     *
     * @param $options
     * @return bool
     */
  	public function tryConnect($options)
    {
        $this->connection = mysqli_connect($options['hostname'], $options['username'], $options['password'], $options['database']);

        if (!$this->connection) {
            return false;
        }

        return true;
    }

    /**
     * @param string $charset
     */
    public function setCharset($charset)
    {
        mysqli_set_charset($this->connection, $charset) or exit(mysqli_error($this->connection));
    }

    /**
     * @param $sql
     * @return bool|mysqli_result
     */
	public function query($sql)
    {
		return mysqli_query($this->connection, $sql);
	}

    /**
     * @param mysqli_result $result
     * @param $mode
     * @return array|null
     */
	public function fetch($result, $mode)
    {
		return mysqli_fetch_array($result, $mode);
	}

    /**
     * @return int
     */
	public function affected_rows()
    {
		return mysqli_affected_rows($this->connection);
	}

    /**
     * @param mysqli_result $result
     * @return int
     */
	public function num_rows($result)
    {
		return mysqli_num_rows($result);
	}

    /**
     * @param mysqli_result $result
     */
	public function free($result)
    {
		mysqli_free_result($result);
	}

    /**
     * @param mixed $value
     * @return string
     */
	public function escape($value)
    {
		return mysqli_real_escape_string($this->connection, $value);
	}

    /**
     * @return int|string
     */
	public function getLastId()
    {
    	return mysqli_insert_id($this->connection);
	}

    /**
     * @return string
     */
	public function getError()
    {
		return mysqli_error($this->connection);
	}

    /**
     * @return int
     */
	public function getErrno()
    {
		return mysqli_errno($this->connection);
	}

    /**
     * return void
     */
	public function close()
    {
		mysqli_close($this->connection);
	}

    /**
     * return void
     */
	public function __destruct()
    {
		@mysqli_close($this->connection);
	}
}