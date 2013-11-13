<?php
namespace Magix\Tier\Common;

final class Runtime {
	const DEVLOPMENT = 'development';
	const PRODUCTION = 'production';
	const TEST = 'testing';

	private static $runLevel = null;

	public static function getLevel() {
		if (self::$runLevel === null)
			self::setLevel(defined('APPLICATION_ENV') ? APPLICATION_ENV : self::DEVLOPMENT);
		return self::$runLevel;
	}

	public static function setLevel($level) {
		if (!in_array($level, array(
				self::DEVLOPMENT,
				self::PRODUCTION,
				self::TEST
		))) {
			throw new \Exception('run Level Type error!');
		}
		self::$runLevel = $level;
	}
}

