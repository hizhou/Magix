<?php
namespace Magix\Tier\Common;

final class Runtime {
	const DEVLOPMENT = 'dev';
	const PRODUCTION = 'prod';
	const TEST = 'test';

	private static $runLevel = null;

	public static function getLevel() {
		if (self::$runLevel === null)
			self::$runLevel = defined('APPLICATION_ENV') ? APPLICATION_ENV : self::DEVLOPMENT;
		return self::$runLevel;
	}

	public static function setLevel($level) {
		if (!in_array($level, array(
				self::DEVLOPMENT,
				self::PRODUCTION,
				self::TEST
		))) {
			throw new Exception('run Level Type error!');
		}
		self::$runLevel = $level;
	}
}

