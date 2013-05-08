<?php
namespace Magix\Tier\Dao;

use Magix\Tier\Dao\QueryHelper;
use Magix\Tier\Dao\SqlHelper;

abstract class BaseDao {
	private $_queryHelper;
	private $_sqlHelper;
	
	protected $_connection;

	/**
	 * @return PDO
	 */
	abstract public function getConnection();

	public function setConnection($conn) {
		$this->_connection = $conn;
	}

	/**
	 * @return QueryHelper
	 */
	public function getQueryHelper() {
		if (!$this->_queryHelper) {
			$this->_queryHelper = new QueryHelper($this->getConnection());
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

