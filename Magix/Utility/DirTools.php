<?php
namespace Magix\Utility;

class DirTools {
	public static function makeDirectory($base, $path) {
		if (!is_dir($base)) mkdir($base);
		if (!is_writable($base)) return false;
		
		$parts = explode('/', trim(str_replace("\\", '/', $path), '/'));
		
		$fullpath = rtrim($base, "/\\");
		foreach ($parts as $segment) {
			$fullpath .= '/' . $segment;
			if (is_dir($fullpath)) {continue;}
			if (!mkdir($fullpath)) return false;
		}
		return true;
	}
}