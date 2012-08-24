<?php
/*
 * Copyright (c) 2012 David Negrier
 *
 * See the file LICENSE.txt for copying permission.
 */

namespace Mouf\Html\Widgets\StatsGrid;

/**
 * A source of value in StatsGrid
 * 
 * @author David Negrier
 */
interface StatsValueDescriptorInterface {
	
	/**
	 * The title for these values (only displayed if there are many values in the table)
	 * 
	 * @return string
	 */
	public function getTitle();
	
	/**
	 * Returns the value based on the row passed in parameter.
	 * 
	 * @param array $row
	 * @return string
	 */
	public function getValue(array $row);
}