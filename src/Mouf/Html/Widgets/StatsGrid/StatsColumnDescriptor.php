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
	
	/**
	 * Whether to sort or not the column.
	 * A comparison function can be passed instead of true or false.
	 * 
	 * @var bool|callable
	 */
	private $sort = true;
	
	public function __construct($key = null, $title = null) {
		$this->key = $key;
		$this->title = $title;		
	}
		
	/**
	 * Returns a list of distinct values for the dataset passed in parameter.
	 * @return array<string>
	 */
	public function getDistinctValues($dataset) {
		$values = array();
		foreach ($dataset as $row) {
			$values[$row[$this->key]] = 1;
		}
		// Note: warning: with this method, we loose the type of the value (int or string)
		$vals = array_keys($values);
		if ($this->sort !== false) {
			if (is_callable($this->sort)) {
				usort($vals, $this->sort);
			} else {
				sort($vals);
			}
		}
		return $vals;
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
		if (!isset($datarow[$this->key]) || $datarow[$this->key] != $value) {
			return false;
		}
		return true;
	}
	
	/**
	 * Sets whether the column/rows values should be sorted or not.
	 * - true: the column is sorted
	 * - false: the column is not sorted (it depends on the dataset passed in parameter, which can be quite unreliable)
	 * - function: you can pass a comparison function to provide a specific sorting order.
	 * 
	 * @param bool|callable $sort
	 */
	public function setSort($sort) {
		if (!is_bool($sort) && !is_int($sort) && !is_callable($sort)) {
			throw new \Exception("The \$sort parameter of StatsColumnDescriptor::setSort must be either a boolean or a sortable.");
		}
		$this->sort = $sort;
	}
	
}