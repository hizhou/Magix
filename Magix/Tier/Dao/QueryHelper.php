<?php
namespace Magix\Tier\Dao;

class QueryHelper {
	
	/**
	 * @var \PDO
	 */
	private $_connection;
	
	private $_queryKey = null;

	public function __construct(\PDO $connection) {
		$this->_connection = $connection;
	}

	/**
	 * @param string $queryKey index field of fetch-result-array
	 * @return QueryHelper
	 */
	public function setQueryKey($queryKey) {
		$queryKey = trim($queryKey);
		if ('' != $queryKey)
			$this->_queryKey = $queryKey;
		
		return $this;
	}

	private function _unsetQueryKey() {
		$this->_queryKey = null;
		return $this;
	}

	public function query($tableName, $columns = null, $selection = null, $selectionArgs = null, $groupBy = null, $having = null, $orderBy = null, $limit = null, $offset = null) {
		$sql = $this->_buildQuerySql($tableName, $columns, $selection, $groupBy, $having, $orderBy, $limit, $offset);
		$stmt = $this->_connection->prepare($sql);
		$this->_bindParams($stmt, $selectionArgs);
		$stmt->execute();
		$records = $stmt->fetchAll(\PDO::FETCH_ASSOC);
		if (!$this->_queryKey)
			return $records;
		if (!isset($records[0]) || !isset($records[0][$this->_queryKey]))
			return $records;
		
		$newRecords = array();
		foreach ($records as $record) {
			$newRecords[$record[$this->_queryKey]] = $record;
		}
		
		$this->_unsetQueryKey();
		return $newRecords;
	}
	
	public function listColumn($tableName, $columns = null, $selection = null, $selectionArgs = null, $groupBy = null, $having = null, $orderBy = null, $limit = null, $offset = null) {
		$sql = $this->_buildQuerySql($tableName, $columns, $selection, $groupBy, $having, $orderBy, $limit, $offset);
		$stmt = $this->_connection->prepare($sql);
		$this->_bindParams($stmt, $selectionArgs);
		$stmt->execute();
		return $stmt->fetchAll(\PDO::FETCH_COLUMN);
	}

	public function getColumn($tableName, $columns = null, $selection = null, $selectionArgs = null) {
		$sql = $this->_buildQuerySql($tableName, $columns, $selection, null, null, null, 1);
		$stmt = $this->_connection->prepare($sql);
		$this->_bindParams($stmt, $selectionArgs);
		$stmt->execute();
		return $stmt->fetchColumn();
	}

	public function get($tableName, $columns = null, $selection = null, $selectionArgs = null, $orderBy = null) {
		$sql = $this->_buildQuerySql($tableName, $columns, $selection, null, null, $orderBy, 1);
		$stmt = $this->_connection->prepare($sql);
		$this->_bindParams($stmt, $selectionArgs);
		$stmt->execute();
		$row = $stmt->fetch(\PDO::FETCH_ASSOC);
		return !empty($row) ? $row : null;
	}

	public function update($tableName, $values, $whereClause = null, $whereArgs = null, $incrementedValues = null) {
		$sql = $this->_buildUpdateSql($tableName, $values, $incrementedValues, $whereClause);
		
		$stmt = $this->_connection->prepare($sql);
		$this->_bindParams($stmt, $values);
		$this->_bindParams($stmt, $whereArgs, count($values));
		if (!$stmt->execute())
			return -1;
		return $stmt->rowCount();
	}

	public function insert($table, Array $values) {
		$sql = $this->_buildInsertSql($table, $values);
		$stmt = $this->_connection->prepare($sql);
		$this->_bindParams($stmt, $values);
		$stmt->execute();
		$lastInsertId = $this->_connection->lastInsertId();
		return $lastInsertId ? $lastInsertId : $stmt->rowCount();
	}

	public function delete($table, $whereClause = null, $whereArgs = null) {
		$sql = $this->_buildDeleteSql($table, $whereClause);
		$stmt = $this->_connection->prepare($sql);
		$this->_bindParams($stmt, $whereArgs);
		$stmt->execute();
		return $stmt->rowCount();
	}

	public function count($tableName, $column = null, $whereClause = null, $whereArgs = null) {
		$sql = $this->_buildCountSql($tableName, $column, $whereClause);
		$stmt = $this->_connection->prepare($sql);
		$this->_bindParams($stmt, $whereArgs);
		$stmt->execute();
		return intval($stmt->fetchColumn());
	}
	
	/**
	 * column自增/自减
	 * 
	 * @param string $tableName
	 * @param array $incrementedValues array('columnName1' => 1, 'columnName2' => -1)
	 * @param string $whereClause
	 * @param string $whereArgs
	 * @return int
	 */
	public function increase($tableName, array $incrementedValues, $whereClause = null, $whereArgs = null) {
		$sql = $this->_buildUpdateSql($tableName, array(), $incrementedValues, $whereClause);
		$stmt = $this->_connection->prepare($sql);
		$this->_bindParams($stmt, $whereArgs);
		$stmt->execute();
		return $stmt->rowCount();
	}

	/**
	 * 验证某个表是否存在
	 * 
	 * @param string $tableName
	 * @return boolean
	 */
	public function isTableExist($tableName) {
		$sql = "SHOW TABLES LIKE " . $this->_connection->quote($tableName);
		$stmt = $this->_connection->prepare($sql);
		$stmt->execute();
		return (bool) $stmt->fetchColumn();
	}

	private function _buildQuerySql($tableName, $columns = null, $selection = null, $groupBy = null, $having = null, $orderBy = null, $limit = null, $offset = null) {
		$sql = 'SELECT ';
		$sql .= empty($columns) ? '* ' : $columns . ' ';
		$sql .= 'FROM ' . $tableName . ' ';
		$sql .= empty($selection) ? '' : 'WHERE ' . $selection . ' ';
		$sql .= empty($groupBy) ? '' : 'GROUP BY ' . $groupBy . ' ';
		//TODO HAVING
		$sql .= empty($having) ? '' : 'HAVING ' . $having . ' ';
		$sql .= empty($orderBy) ? '' : 'ORDER BY ' . $orderBy . ' ';
		if (!empty($limit) && $limit > 0) {
			$offset = (empty($offset) || $offset < 0) ? 0 : $offset;
			$sql .= 'LIMIT ' . $offset . ', ' . $limit;
		}
		return $sql;
	}

	private function _buildUpdateSql($tableName, $values, $incrementedValues, $whereClause) {
		$sql = 'UPDATE ' . $tableName . ' ';
		$sql .= 'SET ';
		
		$fields = array();
		foreach (array_keys($values) as $key) {
			$fields[] = '`' . $key . '`=?';
		}
		
		foreach ((array) $incrementedValues as $columnName => $increment) {
			$increment = intval($increment);
			if ($increment != 0) {
				if ($increment > 0)
					$increment = '+' . $increment;
				$increment = strval($increment);
				$fields[] = $columnName . '=' . $columnName . $increment; //TODO CHECK IS SAFE
			} else {
				$fields[] = $columnName . '=' . $columnName;
			}
		}
		
		$sql .= implode(',', $fields) . ' ';
		$sql .= empty($whereClause) ? '' : 'WHERE ' . $whereClause;
		
		return $sql;
	}

	private function _buildInsertSql($table, $values) {
		$sql = "INSERT INTO {$table} (";
		foreach (array_keys($values) as $key) {
			$sql .= "`{$key}`,";
		}
		$sql = rtrim($sql, ',') . ') VALUES (';
		$sql .= join(array_fill(0, count($values), '?'), ',');
		$sql .= ')';
		return $sql;
	}

	private function _buildDeleteSql($table, $whereClause) {
		$sql = 'DELETE FROM ' . $table . ' ';
		$sql .= empty($whereClause) ? '' : 'WHERE ' . $whereClause;
		return $sql;
	}

	private function _buildCountSql($tableName, $column, $whereClause) {
		$sql = 'SELECT COUNT(';
		$sql .= empty($column) ? '*' : $column;
		$sql .= ') FROM ' . $tableName . ' ';
		$sql .= empty($whereClause) ? '' : 'WHERE ' . $whereClause;
		return $sql;
	}

	private function _bindParams(&$stmt, $values, $startIndex = 0) {
		$values = empty($values) ? array() : array_values($values);
		for ($i = 0; $i < count($values); $i++) {
			$stmt->bindParam($startIndex + $i + 1, $values[$i]);
		}
	}
}
