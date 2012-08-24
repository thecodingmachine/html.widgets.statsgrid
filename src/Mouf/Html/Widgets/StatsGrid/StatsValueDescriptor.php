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
 * @author David Negrier
 * @Component
 */
class StatsValueDescriptor implements StatsValueDescriptorInterface {
	
	/**
	 * The title for this column.
	 * 
	 * @Property
	 * @var string
	 */
	private $title;
	
	/**
	 * The key for this column.
	 * 
	 * @Property
	 * @var string
	 */
	private $key;
	
	/**
	 * 
	 * @param string $key The key that will be displayed as a value
	 * @param string $title The title for these values (only displayed if there are many values in the table)
	 */
	public function __construct($key = null, $title = null) {
		$this->key = $key;
		$this->title = $title;		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see StatsValueDescriptorInterface::getTitle()
	 */
	public function getTitle() {
		return $this->title;
	}
	
	/**
	 * Returns the value based on the row passed in parameter.
	 *
	 * @param array $row
	 * @return string
	 */
	public function getValue(array $row) {
		return $row[$this->key];
	}
	
}