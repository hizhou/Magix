<?php
namespace Magix\Tier\Common;

use Magix\Tier\Utility\ObjectContainer;

class BaseFactory {
	/**
	 * @var ObjectContainer
	 */
	protected $_container;

	protected function __construct() {
		$this->_container = ObjectContainer::instance();
	}

	/**
	 * @return ObjectContainer
	 */
	protected function getContainer() {
		return $this->_container;
	}

	protected function __clone() {
		throw new \RuntimeException('You can not copy this object.');
	}
}