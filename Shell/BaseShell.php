<?php
namespace Magix\Shell;

abstract class BaseShell {
	protected $timeLimit = 0;
	protected $memoryLimit = 0;
	protected $processNumberLimit = 1;
	protected $args;
	protected $rawArgs;
	
	public function __construct($timeLimit = null, $memoryLimit = null) {
		$this->setTimeLimit($timeLimit)
			->setMemoryLimit($memoryLimit);
	}
	
	public function println($message = '') {
		echo $message . "\r\n";
	}
	
	public function error($message = '') {
		$this->println($message);
		exit;
	}
	
	public function getOption($optionKey = null, $default = null) {
		if (null === $this->args) $this->initArgs();
		
		if (null === $optionKey) return $this->args;
		return isset($this->args[$optionKey]) ? $this->args[$optionKey] : $default;
	}
	
	public function getRawOptioins() {
		if (null === $this->rawArgs) {
			$this->rawArgs = isset($_SERVER['argv']) ? $_SERVER['argv'] : array();
			if (isset($this->rawArgs[0])) unset($this->rawArgs[0]);
		}
		return $this->rawArgs;
	}
	
	public function setTimeLimit($timeLimit = null) {
		if (null === $timeLimit) return $this;
		if ($timeLimit < 0) throw new ShellException('timeLimit should >= 0');
		$this->timeLimit = $timeLimit;
		
		return $this;
	}
	
	public function setMemoryLimit($memoryLimit = null) {
		if (null === $memoryLimit) return $this;
		if ($memoryLimit < 0) throw new ShellException('memoryLimit should >= 0');
		$this->memoryLimit = $memoryLimit;
		
		return $this;
	}
	
	
	public function execute() {
		$this->beforeRun();
		$this->run();
		$this->afterRun();
	}
	
	
	
	protected function beforeRun() {
		$this->applyTimeLimit()
			->applyMemoryLimit();
	}
	
	abstract protected function run();
	
	protected function afterRun() {
	}
	
	

	
	protected function applyTimeLimit($timeLimit = null) {
		$this->setTimeLimit($timeLimit);
		
		set_time_limit($this->timeLimit);
		
		return $this;
	}
	
	protected function applyMemoryLimit($memoryLimit = null) {
		$this->setMemoryLimit($memoryLimit);
		
		if ($this->memoryLimit) {
			ini_set('memory_limit', $this->memoryLimit);
		}
		
		return $this;
	}
	
	protected function initArgs() {
		if (!$this->getRawOptioins()) {
			$this->args = array();
			return null;
		}
		
		foreach ($this->getRawOptioins() as $optionPair) {
			if (strpos($optionPair, '--') !== 0) continue;
			$optionPair = substr($optionPair, 2);
			list($key, $value) =  strpos($optionPair, '=') !== false ? explode('=', $optionPair) : array($optionPair, true);
			$this->args[$key] = $value;
		}
	}
	
}

