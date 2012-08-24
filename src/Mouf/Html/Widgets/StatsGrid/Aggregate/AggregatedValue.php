<?php
/*
 * Copyright (c) 2012 David Negrier
 *
 * See the file LICENSE.txt for copying permission.
 */

namespace Mouf\Html\Widgets\StatsGrid\Aggregate;

/**
 * Represents a single aggregated value cell displayed in StatsGrid
 * 
 * @author David Negrier
 */
class AggregatedValue {
	
	public $xCoord;
	public $yCoord;
	
	/**
	 * 
	 * @var StatsColumn
	 */
	public $statsRow;

	/**
	 *
	 * @var StatsColumn
	 */
	public $statsColumn;
	
	/**
	 * Whether this value is aggregated on column or on row.
	 * Can be "column" or "row".
	 * 
	 * @var string
	 */
	public $aggregateOn;
	
	private $values = array();
	
	public function addValue($value) {
		$this->values[] = $value;
	}
	
	public function setValues($values) {
		$this->values = $values;
	}
	
	public function getValues() {
		return $this->values;
	}
}