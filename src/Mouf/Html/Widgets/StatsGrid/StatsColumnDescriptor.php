<?php
/*
 * Copyright (c) 2012 David Negrier
*
* See the file LICENSE.txt for copying permission.
*/

namespace Mouf\Html\Widgets\StatsGrid;

/**
 * A column or row of a StatsGrid
 * 
 * @author David
 * @Component
 */
class StatsColumnDescriptor implements StatsColumnDescriptorInterface {
	
	/**
	 * The title for this column.
	 * 
	 * @Property
	 * @var string
	 */
	public $title;
	
	/**
	 * The key for this column.
	 * 
	 * @Property
	 * @var string
	 */
	public $key;
	
	public function __construct($key = null, $title = null) {
		$this->key = $key;
		$this->title = $title;		
	}
	
	/*public function getKey() {
		return $this->key;
	}*/
	
	/**
	 * Returns a list of distinct values for the dataset passed in parameter.
	 * @return array<string>
	 */
	public function getDistinctValues($dataset) {
		$values = array();
		foreach ($dataset as $row) {
			$values[$row[$this->key]] = 1;
		}
		return array_keys($values);
	}
	
	/**
	 * Returns the value of the column based on the row passed in parameter.
	 *
	 * @param array $row
	 */
	public function getValue(array $row) {
		return $row[$this->key];
	}
	
	/**
	 * Returns true if the $datarow contains the $value.
	 * False otherwise.
	 *  
	 * @param array $row
	 * @param string $value
	 */
	public function isFiltered(array $datarow, $value) {
		if (!isset($datarow[$this->key]) || $datarow[$this->key] !== $value) {
			return false;
		}
		return true;
	}
	
}