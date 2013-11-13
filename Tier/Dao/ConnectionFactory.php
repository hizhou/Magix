<?php
namespace Magix\Tier\Dao;

use Magix\Tier\Dao\PropelPDO;

abstract class ConnectionFactory {
	/**
	 * @var \PDO arrays
	 */
	protected $_connections = array();
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
	
	public function getReadConnection() {
		return $this->getConnection('read', $this->_config['read']);
	}
	
	public function getWriteConnection() {
		return $this->_config['write'] 
			? $this->getConnection('write', $this->_config['write']) 
			: $this->getReadConnection();
	}

	private function getConnection($name, $config) {
		if (!isset($this->_connections[$name])) {
			$this->_connections[$name] = new PropelPDO($config['dsn'], $config['dbuser'], $config['dbpass'], array(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));
			
			$this->_connections[$name]->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			
			//$this->_connection->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,FALSE);
			$this->_connections[$name]->exec('set names utf8');
		}
		return $this->_connections[$name];
	}

	public function closeConnection() {
		$this->_connections = null;
	}

	abstract protected function loadDbConfig();
}


