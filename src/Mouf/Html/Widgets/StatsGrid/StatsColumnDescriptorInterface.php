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
interface StatsColumnDescriptorInterface {
	
	//public function getKey();
	
	/**
	 * Returns a list of distinct values for the dataset passed in parameter.
	 * @return array<string>
	 */
	public function getDistinctValues($dataset);
	
	/**
	 * Returns the value of the column based on the row passed in parameter.
	 *
	 * @param array $row
	 */
	public function getValue(array $row);
	
	/**
	 * Returns true if the $datarow contains the $value.
	 * False otherwise.
	 *
	 * @param array $row
	 * @param string $value
	 * @return bool
	 */
	public function isFiltered(array $datarow, $value);
}