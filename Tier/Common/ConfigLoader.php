<?php
namespace Magix\Tier\Common;

use Symfony\Component\Yaml\Yaml;
class ConfigLoader {
	private static $_instance;
	
	private $_basePath;
	private $_configs = array();
	
	/**
	 * @return ConfigLoader
	 */
	public static function getInstance() {
		if (!self::$_instance) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	private function __construct()  {
		$this->_basePath = realpath(__DIR__ . '/../../../../app/config/') . '/';
	}
	
	public function setConfigBasePath($path) {
		if (file_exists($path)) $this->_basePath = rtrim($path, "/\\") . '/';
	}
	
	public function getConfig($configFile, $option = null) {
		$configFile = trim($configFile, "/\\");
		if ('' == $configFile) return null;
		
		$configFile = $configFile . "_" . Runtime::getLevel() . ".yml";
		
		if (!isset($this->_configs[$configFile])) {
			$file = $this->_basePath . $configFile;
			$this->_configs[$configFile] = file_exists($file) ? Yaml::parse($file) : null;
		}
		return null === $option ? $this->_configs[$configFile] : (
			isset($this->_configs[$configFile][$option]) ? $this->_configs[$configFile][$option] : null
		);
	}
}
