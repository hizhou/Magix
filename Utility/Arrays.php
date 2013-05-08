<?php
namespace Magix\Utility;

class Arrays {
	public static function sortByKey($datas, $key, $keepIndex = false) {
		$sorted = array();
		
		foreach ($datas as $index => $row) {
			if (!isset($sorted[$row[$key]])) $sorted[$row[$key]] = array();
			!$keepIndex ? ($sorted[$row[$key]][] = $row) : ($sorted[$row[$key]][$index] = $row);
		}
		return $sorted;
	}
	
	public static function makeMap($multiArr, $keyKey, $valueKey = null) {
		if (!is_array($multiArr)) return array();
		
		$map = array();
		foreach($multiArr as $v) {
			if (isset($v[$keyKey])) $map[$v[$keyKey]] = null === $valueKey ? $v : $v[$valueKey];
		}
		return $map;
	}
	
	public static function cascade($datas, $primaryKey, $parentKey, $start = 0, $depth = 0, $depthKey = '__depth') {
		if (!$datas || !is_array($datas)) return array();
		
		$groupedRows = array();
		foreach ($datas as $row) {
			$parent = $row[$parentKey];
			if (!isset($groupedRows[$parent])) $groupedRows[$parent] = array();
			$groupedRows[$parent][$row[$primaryKey]] = $row;
		}
		unset($datas);
		
		$cascadeKeeper = array();
		return self::_recurrentGroupedRows($cascadeKeeper, $groupedRows, $start, $depth, $depthKey);
	}
	
	private static function _recurrentGroupedRows($cascadeKeeper, $groupedRows, $groupId, $depth, $depthKey) {
		if (!isset($groupedRows[$groupId])) return $cascadeKeeper;
		
		foreach ($groupedRows[$groupId] as $rowId => $row) {
			$row[$depthKey] = $depth;
			$cascadeKeeper[$rowId] = $row;
			$cascadeKeeper = self::_recurrentGroupedRows($cascadeKeeper, $groupedRows, $rowId, $depth+1, $depthKey);
		}
		return $cascadeKeeper;
	}
}