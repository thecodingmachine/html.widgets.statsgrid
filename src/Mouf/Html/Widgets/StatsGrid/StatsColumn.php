<?php
/*
 * Copyright (c) 2012 David Negrier
 *
 * See the file LICENSE.txt for copying permission.
 */

namespace Mouf\Html\Widgets\StatsGrid;

use RecursiveIterator;
use SplObjectStorage;

/**
 * An actual column or row.
 * It implements a recursive iterator so it can be browsed with a foreach to get this node and all children.
 * 
 * @author David
 */
class StatsColumn implements RecursiveIterator {
	
	public $value;
	
	/**
	 * The column descriptor for this value
	 * @var StatsColumnDescriptor
	 */
	public $columnDescriptor;
	
	/**
	 * The parent statsColumn (if any)
	 * @var StatsColumn
	 */
	public $parent;
	
	/**
	 * 
	 * @var array<StatsColumn>
	 */
	public $subcolumns = array();
	
	/**
	 * Coordinate of the column in the final table (only for the most children columns)
	 * @var int
	 */
	public $xCoord;
	/**
	 * Coordinate of the row in the final table (only for the most children rows)
	 * @var int
	 */
	public $yCoord;

	/**
	 * Coordinate of the column in the final table where the aggregate starts
	 * @var int
	 */
	public $xAggregateCoord;
	
	/**
	 * Coordinate of the row in the final table where the aggregate starts
	 * @var int
	 */
	public $yAggregateCoord;
	
	/**
	 * Fills this instance of statsColumn object with data from the $dataset parameter.
	 * Each subcolumn of this instance will be recursively filled according to the $rows parameter.
	 * At the end of the call to this function, the column and rows are ready to be displayed (but the 
	 * actual data in the table is not filled by this function call.
	 * 
	 * @param array $dataset
	 * @param array<StatsColumnDescriptorInterface> $rows
	 */
	public function fill(array $dataset, array $rows) {
		if (!empty($rows)) {
			$rowElem = array_shift($rows);
			/* @var $rowElem StatsColumnDescriptorInterface */
			
			
			$values = $rowElem->getDistinctValues($dataset);
			foreach ($values as $value) {
				$statColumn = new StatsColumn();
				$statColumn->value = $value;
				$statColumn->parent = $this;
				$statColumn->columnDescriptor = $rowElem;
				if (!empty($rows)) {
					$subDataSet = $this->getSubDataSet($dataset, $rowElem, $value);
					/*if ($rowElem->getKey() !== null) {
						$subDataSet = $this->getSubDataSet($dataset, array($rowElem->getKey()=>$value));
					} else {
						$subDataSet = $dataset;
					}*/
					$statColumn->fill($subDataSet, $rows);
				}
				$this->subcolumns[] = $statColumn;
			}
		}
	}
	
	/**
	 * Get the colspan or rowspan for this column
	 */
	public function getSpan(StatsGrid $statsGrid, $nbValues) {
		if (empty($this->subcolumns)) {
			return $nbValues;
		} else {
			$sum = 0;
			foreach ($this->subcolumns as $subcolumn) {
				$sum += $subcolumn->getSpan($statsGrid, $nbValues);
			}
			
			$aggregators = $statsGrid->getAggregatorsForDescriptor($subcolumn->columnDescriptor);
			$sum += count($aggregators); 
			
			return $sum;
		}
	}
	
	/**
	 * Filters the data set using the criterion passed in parameter.
	 *
	 * @param array $dataset
	 * @param StatsColumnDescriptorInterface $rowElem
	 * @param string $value
	 */
	private function getSubDataSet(array $dataset, StatsColumnDescriptorInterface $rowElem, $value) {
		return array_filter($dataset, function($row) use ($rowElem, $value) {
			/* @var $rowElem StatsColumnDescriptorInterface */
			return $rowElem->isFiltered($row, $value);
		});
	}
	
	/**
	 * Returns the list of filters to this column (including parent filters) 
	 * The key is the StatsColumnDescriptorInterface, the value is the value to filter upon.
	 * 
	 * @return SplObjectStorage
	 */
	public function getFilters() {
		if ($this->parent === null) {
			$filters = new SplObjectStorage();
		} else {
			$filters = $this->parent->getFilters();
		}
		if ($this->columnDescriptor) {
			$filters->attach($this->columnDescriptor, $this->value);
		}
		return $filters;
	}
	
	/**
	 * Returns all statscolumns which are representing the $columnDescriptor
	 * 
	 * @param StatsColumnDescriptor $columnDescriptor
	 * @return array<StatsColumn>
	 */
	/*public function getChildrenByDescriptor(StatsColumnDescriptor $columnDescriptor) {
		$children = array();
		foreach ($this->subcolumns as $subcolumn) {
			/* @var $subcolumn StatsColumn * /
			$children = array_merge($children, $subcolumn->getChildrenByDescriptor($columnDescriptor));
		}
		if ($this->columnDescriptor == $columnDescriptor) {
			$children = array_merge($children, array($this));
		}
		return $children;
	}*/
	
	/**
	 * Returns all statscolumns that are representing the aggregated column for descriptor $columnDescriptor
	 * 
	 * @param StatsColumnDescriptor $columnDescriptor
	 * @return array<StatsColumn>
	 */
	public function getStatsColumnByAggregatedDescriptor(StatsColumnDescriptor $columnDescriptor) {
		$children = array();
		foreach ($this->subcolumns as $subcolumn) {
			/* @var $subcolumn StatsColumn */
			$children = array_merge($children, $subcolumn->getStatsColumnByAggregatedDescriptor($columnDescriptor));
		}
		if (!empty($this->subcolumns) &&  $this->subcolumns[0]->columnDescriptor == $columnDescriptor) {
			$children = array_merge($children, array($this));
		}
		return $children;
	}
	
	
	/*RecursiveIterator*/
	
	public function current()
	{
		return current($this->subcolumns);
	}
	
	public function key()
	{
		return key($this->subcolumns);
	}
	
	public function next()
	{
		next($this->subcolumns);
	}
	
	public function rewind()
	{
		reset($this->subcolumns);
	}
	
	public function valid()
	{
		return (current($this->subcolumns) !== FALSE);
	}
	
	public function getChildren() {
		return current($this->subcolumns);
	}

	public function hasChildren() {
		return !empty(current($this->subcolumns)->subcolumns);
	}
}
