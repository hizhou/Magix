<?php
namespace Magix\Tier\Dao;

use Magix\Tier\Dao\PropelPDO;

abstract class ConnectionFactory {
	protected $_connection = null;
	protected $_config = array();

	protected function __construct() {
		$this->loadDbConfig();
	}

	public static function getInstance() {
		return null;
	}

	public function __clone() {
		throw new Exception('deny Clone.');
	}

	public function getConnection() {
		if (!$this->_connection) {
			$this->_connection = new PropelPDO($this->_config['dsn'], $this->_config['dbuser'], $this->_config['dbpass'], array(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));
			
			$this->_connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			
			//$this->_connection->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,FALSE);
			$this->_connection->exec('set names utf8');
		}
		return $this->_connection;
	}

	public function closeConnection() {
		$this->_connection = null;
	}

	abstract protected function loadDbConfig();
}


