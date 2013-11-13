<?php
namespace Magix\Tier\Dao;

class SqlHelper {
	private static $_instance;

	/**
	 * @return SqlHelper
	 */
	public static function getInstance() {
		if (!self::$_instance) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	
	public function offset($page, $perpage) {
		$page = ($page <= 0) ? 1 : $page;
		return ($page - 1) * $perpage;
	}

	/**
	 * help building in sql
	 * 
	 * @param $dataArr array
	 * @return string
	 */
	public function in($dataArr) {
		return str_repeat('?,', count($dataArr) - 1) . '?';
	}
	
	/**
	 * help building update sql
	 * 
	 * Remember to use SqlHelper::bindValues() to bind params
	 * 
	 * @param array $array
	 * @return string
	 */
	public function buildUpdateClause(Array $array) {
		$updateSect = ' SET ';
		foreach (array_keys($array) as $key) {
			$updateSect .= '`' . $key . '`=:' . $key . ',';
		}
		return substr_replace($updateSect, '', -1);
	}

	/**
	 * help building insert sql
	 * 
	 * Remember to use SqlHelper::bindValues() to bind params
	 * 
	 * @param array $data
	 * @return string
	 */
	public function buildInsertClause(Array $data) {
		$fields = ' (';
		$values = ' VALUES (';
		foreach (array_keys($data) as $key) {
			$fields = $fields . '`' . $key . '`,';
			$values = $values . ':' . $key . ',';
		}
		$fields = substr_replace($fields, ')', -1);
		$values = substr_replace($values, ')', -1);
		
		return $fields . $values;
	}

	public function buildWhereClause($conditions) {
		if (!$conditions || !is_array($conditions) || !count($conditions)) return '';
		
		$wheres = array();
		foreach (array_keys($conditions) as $key) {
			$wheres[] = "`{$key}` = ?";
		}
		return implode(" AND ", $wheres);
	}

	public function bindValues(\PDOStatement $stmt, $array) {
		foreach ($array as $key => $value) {
			$stmt->bindValue(':' . $key, $value);
		}
	}
}
