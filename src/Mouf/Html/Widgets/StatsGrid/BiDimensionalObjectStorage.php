<?php 
/*
 * Copyright (c) 2012 David Negrier
 *
 * See the file LICENSE.txt for copying permission.
 */

namespace Mouf\Html\Widgets\StatsGrid;

use SplObjectStorage;

/**
 * A bidimensional map whose both keys are objects.
 * This object lets you access an iterator (actually a SplObjectStorage object) representing rows and/or columns.
 * 
 * @author David Négrier
 */
class BiDimensionalObjectStorage {

	/**
	 * @var SplObjectStorage
	 */
	private $xyMap;

	/**
	 * @var SplObjectStorage
	 */
	private $yxMap; 
	
	public function __construct() {
		$this->xyMap = new SplObjectStorage();
		$this->yxMap = new SplObjectStorage();
	}
	

	/**
	 * Attach some data to the $xKey, $yKey where both keys can be objects.
	 * 
	 * @param object $xKey
	 * @param object $yKey
	 * @param mixed $value
	 */
	public function put($xKey, $yKey, $value) {
		if (!isset($this->xyMap[$xKey])) {
			$this->xyMap[$xKey] = new SplObjectStorage();
		}
		$this->xyMap[$xKey][$yKey] = $value;

		
		if (!isset($this->yxMap[$yKey])) {
			$this->yxMap[$yKey] = new SplObjectStorage();
		}
		$this->yxMap[$yKey][$xKey] = $value;
	}
	
	/**
	 * Returns some data associated to the $xKey, $yKey
	 * 
	 * @param object $xKey
	 * @param object $yKey
	 */
	public function get($xKey, $yKey) {
		return $this->xyMap[$xKey][$yKey];
	}
	
	/**
	 * Returns a SplObjectStorage for the row whose index is the $yKey.
	 * 
	 * @param object $yKey
	 * @return SplObjectStorage
	 */
	public function getRow($yKey) {
		return $this->yxMap[$yKey];
	}
	
	/**
	 * Returns a SplObjectStorage for the column whose index is the $xKey.
	 * 
	 * @param object $xKey
	 * @return SplObjectStorage
	 */
	public function getColumn($xKey) {
		return $this->xyMap[$xKey];
	}
	
	/**
	 * Returns a SplObjectStorage for all the rows.
	 * 
	 * @return SplObjectStorage
	 */
	public function getRows() {
		return $this->yxMap;
	}
	
	/**
	 * Returns a SplObjectStorage for all the columns.
	 *
	 * @return SplObjectStorage
	 */
	public function getColumns() {
		return $this->xyMap;
	}
}
?>