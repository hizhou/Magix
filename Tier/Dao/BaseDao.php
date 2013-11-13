<?php
namespace Magix\Tier\Dao;

use Magix\Tier\Dao\QueryHelper;
use Magix\Tier\Dao\SqlHelper;

abstract class BaseDao {
	private $_queryHelper;
	private $_sqlHelper;
	
	/**
	 * @return \PDO
	 */
	abstract public function getReadConnection();

	/**
	 * @return \PDO
	 */
	abstract public function getWriteConnection();

	/*
	public function setConnection($conn) {
		$this->_connection = $conn;
	}
	*/

	/**
	 * @return QueryHelper
	 */
	public function getQueryHelper() {
		if (!$this->_queryHelper) {
			$this->_queryHelper = new QueryHelper($this->getReadConnection(), $this->getWriteConnection());
		}
		return $this->_queryHelper;
	}
	
	/**
	 * @return SqlHelper
	 */
	public function getSqlHelper() {
		if (!$this->_sqlHelper) {
			$this->_sqlHelper = SqlHelper::getInstance();
		}
		return $this->_sqlHelper;
	}
}

