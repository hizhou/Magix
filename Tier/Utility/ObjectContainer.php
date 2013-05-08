<?php
namespace Magix\Tier\Utility;

class ObjectContainer {
	
	private $_objs = array();
	
	private static $_inst = null;

	public static function instance() {
		if (self::$_inst === null) {
			self::$_inst = new self();
		}
		return self::$_inst;
	}

	/**
	 * 
	 * 创建对象的单例
	 * 
	 * @param string $scope
	 * @param string $class
	 * @param string $classFilePath
	 */
	public function createObjectInstance($scope, $class, $classFilePath) {
		$instance = $this->getObject($scope, $class);
		if ($instance !== null) {
			return $instance;
		}
		
		$class = "\\" . $classFilePath . "\\" . $class;
		$instance = new $class();
		$this->setObject($scope, $class, $instance);
		return $instance;
	}

	private function setObject($scope, $class, $obj) {
		$this->_objs[$this->getClassKey($scope, $class)] = $obj;
	}

	private function getObject($scope, $class) {
		if ($this->hasCreated($scope, $class)) {
			return $this->_objs[$this->getClassKey($scope, $class)];
		}
		return null;
	}

	private function hasCreated($scope, $class) {
		return isset($this->_objs[$this->getClassKey($scope, $class)]) && !is_null($this->_objs[$this->getClassKey($scope, $class)]);
	}

	private function getClassKey($scope, $class) {
		return $scope . '_' . $class;
	}

	private function __clone() {

	}

	private function __construct() {

	}
}

