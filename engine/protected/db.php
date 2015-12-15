<?php
/*
* MyUCP
*/

class DB {
	private $driver;
	private  $defaults = [
		// Драйвер для работы с БД.
		// По умолчанию MySQL (mysqli).
		'driver'		=>		'mysql',

		// Тип СУБД.
		// По умолчанию поддерживается только СУБД MySQL (mysql).
		'type'			=>		'mysql',
		
		// Хост БД.
		// Пример: localhost, 127.0.0.1, db.example.com и пр.
		'hostname'		=>		'localhost',
		
		// Имя пользователя СУБД.
		'username'		=>		'root',
		
		// Пароль пользователя СУБД.
		'password'		=>		'',
		
		// Название БД.
		'database'		=>		'myucp',

		// Испльзуемая кодировка
		'charset'   	=> 		'utf8',
	];

	const RESULT_ASSOC = MYSQLI_ASSOC;
	const RESULT_NUM   = MYSQLI_NUM;

	public function __construct($options) {
		
		$options = array_merge($this->defaults, $options);

		if($options['driver'] == "mysql" or $options['driver'] == 'pdo'){
			$class = $options['driver'] . 'Driver';
		} else {
			new Debug("Не удалось найти драйвер для работы с базой данных!", 1);
		}

		$this->driver = new $class($options);
	}

	/**
	 * Выполнение составленного запроса
	 * 
	 * Пример:
	 * $this->db->query("DELETE FROM table WHERE id=?i", $id);
	 *
	 * @param string $query - SQL-запрос с плейсхолдерами
	 * @param mixed  $arg,... неограниченное количество агрументов которое соответсвует плейсхолдерам в запросе
	 * @return resource|FALSE
	 */
	public function query() {	
		return $this->rawQuery($this->prepareQuery(func_get_args()));
	}
	
	/**
	 * Функция для выборки одной строки
	 * 
	 * @param resource $result - результат
	 * @param int $mode - необзятальный режим вывода, RESULT_ASSOC|RESULT_NUM, стандартно RESULT_ASSOC
	 * @return array|FALSE
	 */
	public function fetch($result, $mode = self::RESULT_ASSOC) {
		return $this->driver->fetch($result, $mode);
	}

	/**
	 * Функция для получения количества затронутых строк
	 * 
	 * @return int
	 */
	public function affectedRows() {
		return $this->driver->affected_rows();
	}

	/**
	 * Функция для получения ID последней вставки
	 * 
	 * @return int
	 */
	public function insertId() {
		return $this->driver->getLastId();
	}

	/**
	 * Возвращает количество рядов результата запроса
	 * 
	 * @param resource $result - результат выполнения запроса
	 * @return int
	 */
	public function numRows($result) {
		return $this->driver->num_rows($result);
	}

	/**
	 * Освобождает память от результата запроса
	 */
	public function free($result) {
		$this->driver->free($result);
	}

	/**
	 * Воспомагательная функция пользовает получить значение одного поля с аргументами
	 * 
	 * Примеры:
	 * $name = $this->db->getOne("SELECT name FROM table WHERE id=1");
	 * $name = $this->db->getOne("SELECT name FROM table WHERE id=?i", $id);
	 *
	 * @param string $query - SQL-запрос с плейсхолдерами
	 * @param mixed  $arg,... неограниченное количество агрументов которое соответсвует плейсхолдерам в запросе
	 * @return string|FALSE
	 */
	public function getOne() {
		$query = $this->prepareQuery(func_get_args());
		if ($res = $this->rawQuery($query)) {
			$row = $this->fetch($res);
			if (is_array($row)) {
				return reset($row);
			}
			$this->free($res);
		}
		return FALSE;
	}

	/**
	 * Воспомагательная функция что бы получить одну строку с параметрами
	 * 
	 * Примеры:
	 * $data = $this->db->getRow("SELECT * FROM table WHERE id=1");
	 * $data = $this->db->getOne("SELECT * FROM table WHERE id=?i", $id);
	 *
	 * @param string $query - SQL-запрос с плейсхолдерами
	 * @param mixed  $arg,... неограниченное количество агрументов которое соответсвует плейсхолдерам в запросе
	 * @return array|FALSE
	 */
	public function getRow() {
		$query = $this->prepareQuery(func_get_args());
		if ($res = $this->rawQuery($query)) {
			$ret = $this->fetch($res);
			$this->free($res);
			return $ret;
		}
		return FALSE;
	}

	/**
	 * Воспомагательная функция для получения одного столбца с параметрами
	 * 
	 * Примеры:
	 * $ids = $this->db->getCol("SELECT id FROM table WHERE cat=1");
	 * $ids = $this->db->getCol("SELECT id FROM tags WHERE tagname = ?s", $tag);
	 *
	 * @param string $query - SQL-запрос с плейсхолдерами
	 * @param mixed  $arg,... неограниченное количество агрументов которое соответсвует плейсхолдерам в запросе
	 * @return array|FALSE
	 */
	public function getCol() {
		$ret   = array();
		$query = $this->prepareQuery(func_get_args());
		if ( $res = $this->rawQuery($query) ) {
			while($row = $this->fetch($res)) {
				$ret[] = reset($row);
			}
			$this->free($res);
		}
		return $ret;
	}

	/**
	 * Воспомагательная функция для получения всех строк которые выдаст запрос
	 * 
	 * Примеры:
	 * $data = $this->db->getAll("SELECT * FROM table");
	 * $data = $this->db->getAll("SELECT * FROM table LIMIT ?i,?i", $start, $rows);
	 *
	 * @param string $query - SQL-запрос с плейсхолдерами
	 * @param mixed  $arg,... неограниченное количество агрументов которое соответсвует плейсхолдерам в запросе
	 * @return array двуменрный массив содержащий результаты. Пустой если строки не найдены. 
	 */
	public function getAll() {
		$ret   = array();
		$query = $this->prepareQuery(func_get_args());
		if ( $res = $this->rawQuery($query) ) {
			while($row = $this->fetch($res)) {
				$ret[] = $row;
			}
			$this->free($res);
		}
		return $ret;
	}

	/**
	 * Воспомагательная функция для получения всех строк из результата запроса в индексирующий массив
	 * 
	 * Примеры:
	 * $data = $this->db->getInd("id", "SELECT * FROM table");
	 * $data = $this->db->getInd("id", "SELECT * FROM table LIMIT ?i,?i", $start, $rows);
	 *
	 * @param string $index - название поля по которому будет использовтся для индексирования массива
	 * @param string $query - SQL-запрос с  плейсхолдерами
	 * @param mixed  $arg,... неограниченное количество агрументов которое соответсвует плейсхолдерам в запросе
	 * @return array - двумерный массив, индексированный значениями поля, указанного первым параметром
	 */
	public function getInd() {
		$args  = func_get_args();
		$index = array_shift($args);
		$query = $this->prepareQuery($args);
		$ret = array();
		if ( $res = $this->rawQuery($query) ) {
			while($row = $this->fetch($res)) {
				$ret[$row[$index]] = $row;
			}
			$this->free($res);
		}
		return $ret;
	}

	/**
	 * Воспомагательная функция для получения словарь-массив.
	 * 
	 * Примеры:
	 * $data = $this->db->getIndCol("name", "SELECT name, id FROM cities");
	 *
	 * @param string $index - название поля по которому будет использовтся для индексирования массива
	 * @param string $query - SQL-запрос с  плейсхолдерами
	 * @param mixed  $arg,... неограниченное количество агрументов которое соответсвует плейсхолдерам в запросе
	 * @return array - массив скаляров, индексированный полем из первого параметра. Пустой если поля не найдено. 
	 */
	public function getIndCol() {
		$args  = func_get_args();
		$index = array_shift($args);
		$query = $this->prepareQuery($args);
		$ret = array();
		if ( $res = $this->rawQuery($query) ) {
			while($row = $this->fetch($res)) {
				$key = $row[$index];
				unset($row[$index]);
				$ret[$key] = reset($row);
			}
			$this->free($res);
		}
		return $ret;
	}
	/**
	 * Функция ползволяет подготовить запрос раннее и вставить его в основной
	 * 
	 * Примеры:
	 * $query = $this->db->parse("SELECT * FROM table WHERE foo=?s AND bar=?s", $foo, $bar);
	 * echo $query;
	 * 
	 * if ($foo) {
	 *     $qpart = $this->db->parse(" AND foo=?s", $foo);
	 * }
	 * $data = $this->db->getAll("SELECT * FROM table WHERE bar=?s ?p", $bar, $qpart);
	 *
	 * @param string $query - любое выражение с плейсхолдерами
	 * @param mixed  $arg,... неограниченное количество агрументов которое соответсвует плейсхолдерам в запросе
	 * @return string
	 */
	public function parse() {
		return $this->prepareQuery(func_get_args());
	}

	/**
	 * Функция для создания белого списка значений которые могут попасть в запрос
	 * 
	 * Примеры:
	 *
	 * $order = $this->db->whiteList($_GET['order'], array('name','price'));
	 * $dir   = $this->db->whiteList($_GET['dir'],   array('ASC','DESC'));
	 * if (!$order || !dir) {
	 *     throw new http404(); //non-expected values should cause 404 or similar response
	 * }
	 * $sql  = "SELECT * FROM table ORDER BY ?p ?p LIMIT ?i,?i"
	 * $data = $this->db->getArr($sql, $order, $dir, $start, $per_page);
	 * 
	 * @param string $iinput   - название поля для проверки
	 * @param  array  $allowed - массив с разрешенными значенями
	 * @param  string $default - если значение не найденно, то... (опционально)
	 * @return string|FALSE 
	 */
	public function whiteList($input,$allowed,$default=FALSE) {
		$found = array_search($input,$allowed);
		return ($found === FALSE) ? $default : $allowed[$found];
	}

	/**
	 * Функция для фильтрации массивов
	 * 
	 * Примеры:
	 * $allowed = array('title','url','body','rating','term','type');
	 * $data    = $this->db->filterArray($_POST, $allowed);
	 * $sql     = "INSERT INTO ?n SET ?u";
	 * $this->db->query($sql,$table,$data);
	 * 
	 * @param  array $input   - исходный массив
	 * @param  array $allowed - массив с разрешенными названиями полей
	 * @return array filtered out массив
	 */
	public function filterArray($input,$allowed) {
		foreach(array_keys($input) as $key ) {
			if ( !in_array($key,$allowed) ) {
				unset($input[$key]);
			}
		}
		return $input;
	}
	/**
	 * Функция для получения последнего выполненного запроса
	 * 
	 * @return string|NULL последний выполнений запрос или NULL если не один
	 */
	public function lastQuery() {
		$last = end($this->stats);
		return $last['query'];
	}

	/**
	 * Функция для получения статистики выполненного запроса
	 * 
	 * @return array
	 */
	public function getStats() {
		return $this->stats;
	}

	/**
	 * Функция которая отправляет сформированный запрос к MySQL серверу
	 * 
	 * @param string $query - SQL запрос
	 * @return resource result or FALSE on error
	 */
	private function rawQuery($query) {
		$start = microtime(TRUE);
		$res   = $this->driver->query($query);
		$timer = microtime(TRUE) - $start;
		$this->stats[] = array(
			'query' => $query,
			'start' => $start,
			'timer' => $timer,
		);
		if (!$res) {
			$error = $this->driver->getError();
			
			end($this->stats);
			$key = key($this->stats);
			$this->stats[$key]['error'] = $error;
			$this->cutStats();
			
			$this->error("$error. Full query: [$query]");
		}
		$this->cutStats();
		return $res;
	}

	private function prepareQuery($args) {
		$query = '';
		$raw   = array_shift($args);
		$array = preg_split('~(\?[nsiuap])~u',$raw,null,PREG_SPLIT_DELIM_CAPTURE);
		$anum  = count($args);
		$pnum  = floor(count($array) / 2);
		if ( $pnum != $anum ) {
			$this->error("Number of args ($anum) doesn't match number of placeholders ($pnum) in [$raw]");
		}
		foreach ($array as $i => $part) {
			if ( ($i % 2) == 0 ) {
				$query .= $part;
				continue;
			}
			$value = array_shift($args);
			switch ($part) {
				case '?n':
					$part = $this->escapeIdent($value);
					break;
				case '?s':
					$part = $this->escapeString($value);
					break;
				case '?i':
					$part = $this->escapeInt($value);
					break;
				case '?a':
					$part = $this->createIN($value);
					break;
				case '?u':
					$part = $this->createSET($value);
					break;
				case '?p':
					$part = $value;
					break;
			}
			$query .= $part;
		}
		return $query;
	}

	private function escapeInt($value) {
		if ($value === NULL) {
			return 'NULL';
		}
		if(!is_numeric($value)) {
			$this->error("Integer (?i) placeholder expects numeric value, ".gettype($value)." given");
			return FALSE;
		}
		if (is_float($value)) {
			$value = number_format($value, 0, '.', ''); // may lose precision on big numbers
		} 
		return $value;
	}

	private function escapeString($value) {
		if ($value === NULL) {
			return 'NULL';
		}
		return	"'".$this->driver->escape($value)."'";
	}

	private function escapeIdent($value) {
		if ($value) {
			return "`".str_replace("`", "``", $value)."`";
		} else {
			$this->error("Empty value for identifier (?n) placeholder");
		}
	}

	private function createIN($data) {
		if (!is_array($data)) {
			$this->error("Value for IN (?a) placeholder should be array");
			return;
		}
		if (!$data) {
			return 'NULL';
		}
		$query = $comma = '';
		foreach ($data as $value) {
			$query .= $comma.$this->escapeString($value);
			$comma  = ",";
		}
		return $query;
	}
	
	private function createSET($data) {
		if (!is_array($data)) {
			$this->error("SET (?u) placeholder expects array, ".gettype($data)." given");
			return;
		}
		if (!$data) {
			$this->error("Empty array for SET (?u) placeholder");
			return;
		}
		$query = $comma = '';
		foreach ($data as $key => $value) {
			$query .= $comma.$this->escapeIdent($key).'='.$this->escapeString($value);
			$comma  = ",";
		}
		return $query;
	}
	
	private function error($err) {
		new Debug("Ошибка: ".$err, "1");
	}

	private function cutStats() {
		if ( count($this->stats) > 100 ) {
			reset($this->stats);
			$first = key($this->stats);
			unset($this->stats[$first]);
		}
	}
}
?>
