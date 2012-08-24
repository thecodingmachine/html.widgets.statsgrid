<?php
/*
 * Copyright (c) 2012 David Negrier
 *
 * See the file LICENSE.txt for copying permission.
 */

namespace Mouf\Html\Widgets\StatsGrid;

use Mouf\Html\HtmlElement\HtmlElementInterface;
use Mouf\Html\Widgets\StatsGrid\Aggregate;

/**
 * A grid specially tailored at displaying statistics (like a pivot table)
 * 
 * @author David Negrier
 * @Component
 */
class StatsGrid implements HtmlElementInterface {
	
	/**
	 * The list of rows.
	 * 
	 * @var array<StatsColumnDescriptor>
	 */
	private $rows = array();
	
	/**
	 * The list of columns.
	 * 
	 * @var array<StatsColumnDescriptor>
	 */
	private $columns = array();
	
	/**
	 * The list of values.
	 * 
	 * @var array<StatsValueDescriptor>
	 */
	private $values = array();
	
	/**
	 * The list of aggregators.
	 *
	 * @var array<AggregatorDescriptorInterface>
	 */
	private $aggregators = array();
	
	const VALUES_DISPLAY_HORIZONTAL = 1;
	const VALUES_DISPLAY_VERTICAL = 2;
	
	/**
	 * If there are several value descriptor, values will be displayed horizontally, or vertically depending on this parameter.
	 * 
	 * @var int
	 */
	private $valuesDisplayMode = self::VALUES_DISPLAY_HORIZONTAL; 
	
	/**
	 * CSS class for the table tag.
	 * 
	 * @var string
	 */
	private $cssClass = "bluestatsgrid";
	
	private $data;
	
	public function setData($data) {
		$this->data = $data;
	}
	
	/**
	 * Sets the whole list of rows descriptors at once.
	 * 
	 * @Property
	 * @param array<StatsColumnDescriptorInterface> $rows
	 */
	public function setRows(array $rows) {
		$this->rows = $rows;
	}
	
	/**
	 * Adds a new row descriptor that will be used to organize data.
	 *
	 * @param StatsColumnDescriptorInterface $rowDescriptor
	 */
	public function addRow(StatsColumnDescriptorInterface $rowDescriptor) {
		$this->rows[] = $rowDescriptor;
	}
	
	/**
	 * Sets the whole list of columns at once.
	 * 
	 * @Property
	 * @param array<StatsColumnDescriptor> $rows
	 */
	public function setColumns(array $columns) {
		$this->columns = $columns;
	}
	
	/**
	 * Adds a new column descriptor that will be used to organize data.
	 *
	 * @param StatsColumnDescriptorInterface $columnDescriptor
	 */
	public function addColumn(StatsColumnDescriptorInterface $columnDescriptor) {
		$this->columns[] = $columnDescriptor;
	}
	
	/**
	 * Sets the whole list of values at once.
	 *
	 * @Property
	 * @param array<StatsValueDescriptorInterface> $rows
	 */
	public function setValues(array $values) {
		$this->values = $values;
	}
	
	/**
	 * Adds a new value descriptor (to display values in the grid).
	 *
	 * @param StatsValueDescriptorInterface $valueDescriptor
	 */
	public function addValue(StatsValueDescriptorInterface $valueDescriptor) {
		$this->values[] = $valueDescriptor;
	}
	
	/**
	 * Sets the list of aggregators (used to display sum/average...)
	 * 
	 * @param array<AggregatorDescriptorInterface> $aggregators
	 */
	public function setAggregators(array $aggregators) {
		$this->aggregators = $aggregators;
	}
	
	/**
	 * Adds a new aggregator descriptor (to display sums/means).
	 *
	 * @param AggregatorDescriptorInterface $aggregatorDescriptor
	 */
	public function addAggregator(AggregatorDescriptorInterface $aggregatorDescriptor) {
		$this->aggregators[] = $aggregatorDescriptor;
	}
	
	/**
	 * If there are several value descriptor, values will be displayed horizontally, or vertically depending on this parameter.
	 * Can be one of:
	 *  - StatsGrid::VALUES_DISPLAY_HORIZONTAL
	 *  - StatsGrid::VALUES_DISPLAY_VERTICAL
	 *
	 * @param int $valuesDisplayMode
	 */
	public function setValuesDisplayMode($valuesDisplayMode) {
		$this->valuesDisplayMode = $valuesDisplayMode;
	}
	
	
	/**
	 * Renders the object in HTML.
	 * The Html is echoed directly into the output.
	 *
	 */
	public function toHtml() {
		
		$dataset = $this->data;
		
		$statsRows = new StatsColumn();
		$statsRows->fill($this->data, $this->rows);

		$statsColumns = new StatsColumn();
		$statsColumns->fill($this->data, $this->columns);
		
		/*var_dump($statsRows);
		var_dump($statsColumns);*/

		// Xrow: StatsColumn, Yrow: StatsColumn, Value: AggregatedValue
		$aggregatedData = new BiDimensionalObjectStorage();
		// We must create an instance of AggregatedValue for each row/column needed
		// Then we must fill this with data from the dataset.
		foreach ($this->aggregators as $aggregator) {
			// TODO: we might aggregate aggregators by statsColumn (in order to compute the array of values only once if there are many aggregators for one column descriptor)
			/* @var $aggregator AggregatorDescriptorInterface */
			$aggregatorStatsColumnDescriptor = $aggregator->getStatsColumnDescriptor();
			$aggregatorFound = false;
			// If the aggregator is part of a row....
			foreach ($this->rows as $rowDescriptor) {
				/* @var $rowDescriptor StatsColumnDescriptorInterface */
				if ($rowDescriptor == $aggregatorStatsColumnDescriptor) {
					$aggregatorFound = true;
					
					// Let's get all the statsColumns object who represent a $aggregatorStatsColumnDescriptor
					 $rowStatsColumns = $statsRows->getStatsColumnByAggregatedDescriptor($rowDescriptor);
					 foreach ($rowStatsColumns as $rowStatsColumn) {
					 	/* @var $rowStatsColumn StatsColumn */

					 	$rowFilters = $rowStatsColumn->getFilters();
					 	
					 	// Before the "foreach", let's start with the top column
					 	$filters = $statsColumns->getFilters();
					 	$filters->addAll($rowFilters);
					 	$filteredData = $this->filterData($this->data, $filters);
					 	$aggregatedValue = new AggregatedValue();
					 	$aggregatedValue->statsColumn = $statsColumns;
					 	$aggregatedValue->statsRow = $rowStatsColumn;
					 	$aggregatedValue->aggregateOn = "row";
					 	$aggregatedValue->setValues($filteredData);
					 	$aggregatedData->put($statsColumns, $rowStatsColumn, $aggregatedValue);
					 	
					 	
					 	// For this row, for each column in the tree, let's compute the sum
					 	foreach (new RecursiveIteratorIterator($statsColumns, RecursiveIteratorIterator::SELF_FIRST) as $statsColumn) {
					 		/* @var $statsColumn StatsColumn */
					 		$filters = $statsColumn->getFilters();

					 		// Let's concatenate row and column filters
					 		$filters->addAll($rowFilters);
					 		
					 		// Let's now apply those filters to the data
					 		$filteredData = $this->filterData($this->data, $filters);
					 		$aggregatedValue = new AggregatedValue();
					 		$aggregatedValue->statsColumn = $statsColumn;
					 		$aggregatedValue->statsRow = $rowStatsColumn;
					 		$aggregatedValue->aggregateOn = "row";
					 		$aggregatedValue->setValues($filteredData);
					 		
					 		$aggregatedData->put($statsColumn, $rowStatsColumn, $aggregatedValue);
					 	}
					 }					
				}
			}
			
			// If the aggregator is part of a column....
			foreach ($this->columns as $columnDescriptor) {
				/* @var $columnDescriptor StatsColumnDescriptorInterface */
				if ($columnDescriptor == $aggregatorStatsColumnDescriptor) {
					$aggregatorFound = true;
						
					// Let's get all the statsColumns object who represent a $aggregatorStatsColumnDescriptor
					$columnStatsColumns = $statsColumns->getStatsColumnByAggregatedDescriptor($columnDescriptor);
					foreach ($columnStatsColumns as $columnStatsColumn) {
						/* @var $columnStatsColumn StatsColumn */
			
						$columnFilters = $columnStatsColumn->getFilters();
			
						// Before the "foreach", let's start with the top column
						$filters = $statsRows->getFilters();
						$filters->addAll($columnFilters);
						$filteredData = $this->filterData($this->data, $filters);
						$aggregatedValue = new AggregatedValue();
						$aggregatedValue->statsColumn = $columnStatsColumn;
						$aggregatedValue->statsRow = $statsRows;
						$aggregatedValue->aggregateOn = "column";
						$aggregatedValue->setValues($filteredData);
						$aggregatedData->put($columnStatsColumn, $statsRows, $aggregatedValue);
						
						// For this column, for each row in the tree, let's compute the sum
						foreach (new RecursiveIteratorIterator($statsRows, RecursiveIteratorIterator::SELF_FIRST) as $statsRow) {
							/* @var $statsColumn StatsColumn */
							$filters = $statsRow->getFilters();
			
							// Let's concatenate row and column filters
							$filters->addAll($columnFilters);
			
							// Let's now apply those filters to the data
							$filteredData = $this->filterData($this->data, $filters);
							$aggregatedValue = new AggregatedValue();
							$aggregatedValue->statsColumn = $columnStatsColumn;
							$aggregatedValue->statsRow = $statsRow;
							$aggregatedValue->aggregateOn = "column";
							$aggregatedValue->setValues($filteredData);
			
							$aggregatedData->put($columnStatsColumn, $statsRow, $aggregatedValue);
						}
					}
				}
			}
				
			if (!$aggregatorFound) {
				throw new Exception("Error while rendering grid. You tried to aggregate data on a column that is not part of the columns declared in the grid.");
			}
		}
		
		
		
		// A 2 dimensionnal array representing the table (first is y, second is x)
		// Each element is a table with 3 elements: array("value"=>XXX, "colspan"=>YYY, "rowspan"=>ZZZ)
		$table = array();
		
		$maxX = 0;
		$maxY = 0;
		
		$startX = count($this->rows);
		$startY = count($this->columns);
		if (count($this->values)>1) {
			if ($this->valuesDisplayMode == self::VALUES_DISPLAY_HORIZONTAL) {
				$startY++;
			} else {
				$startX++;
			}
		}
		
		$this->putColumnHeaderHtml($statsColumns, $table, $startX, 0, $maxX, $maxY);
		$this->putRowsHeaderHtml($statsRows, $table, 0, $startY, $maxX, $maxY);
		
		if (empty($this->rows) && (empty($this->values) || $this->valuesDisplayMode == self::VALUES_DISPLAY_HORIZONTAL)) {
			$maxY++;
		}
		if (empty($this->columns) && (empty($this->values) || $this->valuesDisplayMode == self::VALUES_DISPLAY_VERTICAL)) {
			$maxX++;
		}
		
		foreach ($this->data as $dataRow) {
			/* @var $value StatsValueDescriptor */
			$xCoord = $this->findCoord($statsColumns, $dataRow);
			$yCoord = $this->findCoord($statsRows, $dataRow);
			$i = 0;
			$j = 0;
			foreach ($this->values as $value) {
				$table[$yCoord+$j][$xCoord+$i]['value'] = $value->getValue($dataRow);
				if ($this->valuesDisplayMode == self::VALUES_DISPLAY_HORIZONTAL) {
					$i++;
				} else {
					$j++;
				}
			}
		}
		
		// Now, let's cycle through the aggregated data
		$aggregatedColumns = $aggregatedData->getColumns();
		foreach ($aggregatedColumns as $xStatsColumn) {
			/* @var $xStatsColumn StatsColumn */
			$aggregatedRows = $aggregatedColumns[$xStatsColumn];
			foreach ($aggregatedRows as $yStatsColumn) {
				/* @var $yStatsColumn StatsColumn */
				$aggregatedValue = $aggregatedRows[$yStatsColumn];
				/* @var $aggregatedValue AggregatedValue */
				
				if (!empty($xStatsColumn->subcolumns)) {
					$xCoord = $xStatsColumn->xAggregateCoord;
				} else {
					$xCoord = $xStatsColumn->xCoord;
				}
				if (!empty($yStatsColumn->subcolumns)) {
					$yCoord = $yStatsColumn->yAggregateCoord;
				} else {
					$yCoord = $yStatsColumn->yCoord;
				}
				
				if ($aggregatedValue->aggregateOn == "row") {
					// Let's find the aggregator back...
					$aggregators = $this->getAggregatorsForDescriptor($yStatsColumn->subcolumns[0]->columnDescriptor);
					$currentColumn = $yStatsColumn;
					
				} else {
					// Let's find the aggregator back...
					$aggregators = $this->getAggregatorsForDescriptor($xStatsColumn->subcolumns[0]->columnDescriptor);
					$currentColumn = $xStatsColumn;
				}
				
				// Let's find the "level" of the aggregator...( sum? subsum? subsubsum?...)
				$level = 0;
				while ($currentColumn->parent != null) {
					$currentColumn = $currentColumn->parent;
					$level++;
				}
				
				$i = 0;
				$j = 0;
				foreach ($aggregators as $aggregator) {
					// FIXME: positionning is bogus
					$table[$yCoord+$j][$xCoord+$i]['value'] = $aggregator->getAggregatedValue($aggregatedValue->getValues());
					$table[$yCoord+$j][$xCoord+$i]['class'] = "aggregate".$level;
					if ($this->valuesDisplayMode == self::VALUES_DISPLAY_HORIZONTAL) {
						$i++;
					} else {
						$j++;
					}
				}
			}
		}
		
		
		/*var_dump($table);
		var_dump($maxX);
		var_dump($maxY);*/
		
		// Finally, let's do a bit of CSS styling on values
		for ($j=$startY; $j<$maxY; $j++) {
			for ($i=$startX; $i<$maxX; $i++) {
				if (isset($table[$j][$i]["class"])) {
					$table[$j][$i]["class"] .= " value";
				} else {
					$table[$j][$i]["class"] = "value";
				}
				$table[$j][$i]["class"] .= ($i%2)?" rowodd":" roweven";
				$table[$j][$i]["class"] .= ($j%2)?" columnodd":" columneven";
			}
		} 
		
		$this->printTable($table, $maxX, $maxY);
	}

	
	/**
	 * Sets the CSS class of the table.
	 * If not set, defaults to "bluestatsgrid"
	 * 
	 * @param string $cssClass
	 */
	public function setCssClass($cssClass = "bluestatsgrid") {
		$this->cssClass = $cssClass;
	}
	
	/**
	 * This puts the column headers in the $table array representing the final table.
	 * 
	 * @param StatsColumn $statsColumns
	 * @param unknown_type $table
	 * @param unknown_type $startX
	 * @param unknown_type $startY
	 * @param unknown_type $maxX
	 * @param unknown_type $maxY
	 */
	private function putColumnHeaderHtml(StatsColumn $statsColumns, &$table, $startX, $startY, &$maxX, &$maxY) {
		if (!empty($statsColumns->subcolumns)) {
			$subSpan = 0;
			foreach ($statsColumns->subcolumns as $subcolumn) {
				/* @var $subcolumn StatsColumn */
				$nbValues = 1;
				if ($this->valuesDisplayMode == self::VALUES_DISPLAY_HORIZONTAL) {
					$nbValues = count($this->values);
				}
				$span = $subcolumn->getSpan($this, $nbValues);
				$table[$startY][$startX] = array("value"=>$subcolumn->value, "colspan"=>$span, "class"=>"header column".$startY);
				if ($span > 1) {
					for ($i=1; $i<$span; $i++) {
						$table[$startY][$startX+$i] = array("spanned"=>true);
					}
				}
				$subcolumn->xCoord = $startX;
				if (!empty($subcolumn->subcolumns)) {
					$this->putColumnHeaderHtml($subcolumn, $table, $startX, $startY+1, $maxX, $maxY);
				} else {
					if (count($this->values)>1 && $this->valuesDisplayMode == self::VALUES_DISPLAY_HORIZONTAL) {
						$i = 0;
						foreach ($this->values as $value) {
							/* @var $value StatsValueDescriptor */
							$table[$startY+1][$startX+$i]['value'] = $value->getTitle();
							$table[$startY+1][$startX+$i]['class'] = 'header column'.($startY+1);
							$i++;
						}
					}
				}
				$startX += $span;
			}
			$aggregators = $this->getAggregatorsForDescriptor($subcolumn->columnDescriptor);
			$statsColumns->xAggregateCoord = $startX;
			foreach ($aggregators as $aggregator) {
				/* @var $aggregator AggregatorDescriptorInterface */
				$table[$startY][$startX] = array("value"=>$aggregator->getTitle(), "class"=>"header column".$startY);
				$startX++;
			}
			
			$maxX = max($maxX, $startX);
			$maxY = max($maxY, $startY+1);
		} else {
			// We arrive here if there is absolutely no row declared
			$statsColumns->xCoord = $startX;
			
			if (count($this->values)>1 && $this->valuesDisplayMode == self::VALUES_DISPLAY_HORIZONTAL) {
				$i = 0;
				foreach ($this->values as $value) {
					/* @var $value StatsValueDescriptor */
					$table[$startY][$startX+$i]['value'] = $value->getTitle();
					$table[$startY][$startX+$i]['class'] = 'header column'.($startY+1);
					$i++;
				}
				$startX += $i;
			}
			
			$maxX = max($maxX, $startX);
			$maxY = max($maxY, $startY+1);
		}
	}
	
	private function putRowsHeaderHtml(StatsColumn $statsRows, &$table, $startX, $startY, &$maxX, &$maxY) {
		if (!empty($statsRows->subcolumns)) {
			$subSpan = 0;
			foreach ($statsRows->subcolumns as $subrow) {
				/* @var $subrow StatsColumn */
				$nbValues = 1;
				if ($this->valuesDisplayMode == self::VALUES_DISPLAY_VERTICAL) {
					$nbValues = count($this->values);
				}
				$span = $subrow->getSpan($this, $nbValues);
				$table[$startY][$startX] = array("value"=>$subrow->value, "rowspan"=>$span, "class"=>"header row".$startX);
				if ($span > 1) {
					for ($i=1; $i<$span; $i++) {
						$table[$startY+$i][$startX] = array("spanned"=>true);
					}
				}
				$subrow->yCoord = $startY;
				if (!empty($subrow->subcolumns)) {
					$this->putRowsHeaderHtml($subrow, $table, $startX+1, $startY, $maxX, $maxY);
				} else {
					if (count($this->values)>1 && $this->valuesDisplayMode == self::VALUES_DISPLAY_VERTICAL) {
						$i = 0;
						foreach ($this->values as $value) {
							/* @var $value StatsValueDescriptor */
							$table[$startY+$i][$startX+1]['value'] = $value->getTitle();
							$table[$startY+$i][$startX+1]['class'] = "header row".($startX+1);
							$i++;
						}
					}
				}
				$startY += $span;
			}
			$aggregators = $this->getAggregatorsForDescriptor($subrow->columnDescriptor);
			$statsRows->yAggregateCoord = $startY;
			foreach ($aggregators as $aggregator) {
				/* @var $aggregator AggregatorDescriptorInterface */
				$table[$startY][$startX] = array("value"=>$aggregator->getTitle(), "class"=>"header row$startX");
				$startY++;
			}
			
			$maxX = max($maxX, $startX+1);
			$maxY = max($maxY, $startY);
		} else {
			// We arrive here if there is absolutely no row declared
			$statsRows->yCoord = $startY;
			
			if (count($this->values)>1 && $this->valuesDisplayMode == self::VALUES_DISPLAY_VERTICAL) {
				$i = 0;
				foreach ($this->values as $value) {
					/* @var $value StatsValueDescriptor */
					$table[$startY+$i][$startX]['value'] = $value->getTitle();
					$table[$startY+$i][$startX]['class'] = 'header row'.($startX+1);
					$i++;
				}
				$startY += $i;
			}
			$maxX = max($maxX, $startX+1);
			$maxY = max($maxY, $startY);
		}
	}
	
	private function printTable($table, $maxX, $maxY) {
		echo "<table class='".$this->cssClass."'>";
		for ($y = 0; $y<$maxY; $y++) {
			if (!isset($table[$y])) {
				continue;
			}
			$row = $table[$y];
			echo "<tr>\n";
			$i = 0;
			$nbRow = 0;
			for ($x = 0; $x<$maxX; $x++) {
				if (!isset($row[$x])) {
					echo "<td></td>";
					continue;
				}
				$cell = $row[$x];
				if (isset($cell["spanned"])) {
					continue;
				}
				echo "<td";
				if (isset($cell['colspan']) && $cell['colspan']!=1) {
					echo " colspan='{$cell['colspan']}'";
				}
				if (isset($cell['rowspan']) && $cell['rowspan']!=1) {
					echo " rowspan='{$cell['rowspan']}'";
				}
				if (isset($cell['class'])) {
					echo " class='{$cell['class']}'";
				}
				echo ">";
				echo isset($cell["value"])?$cell["value"]:"";
				echo "</td>";
				$i++;
			}
			echo "\n</tr>";
		}
		
		echo "</table>";
	}
	
	/**
	 * Find the X or Y coordinate of a dataRow
	 * 
	 * @param StatsColumn $column
	 * @param array $dataRow
	 */
	private function findCoord(StatsColumn $column, $dataRow) {
		if ($column->subcolumns) {
			foreach ($column->subcolumns as $subColumn) {
				/* @var $subColumn StatsColumn */
				
				if ($subColumn->columnDescriptor->isFiltered($dataRow, $subColumn->value)) {
					if (empty($subColumn->subcolumns)) {
						if ($subColumn->xCoord !== null) {
							return $subColumn->xCoord;
						}
						if ($subColumn->yCoord !== null) {
							return $subColumn->yCoord;
						}
					}
					return $this->findCoord($subColumn, $dataRow);
				}
			}
		} else {
			// This happens if there is no row descriptor or no column descriptor
			if ($column->xCoord !== null) {
				return $column->xCoord;
			}
			if ($column->yCoord !== null) {
				return $column->yCoord;
			}
			return null;
		}
	}
	
	/**
	 * Filters the data according to the set of filters passed in parameter.
	 * Returns the part of the $data array that matches the filter criterions.
	 * 
	 * @param array $data
	 * @param SplObjectStorage $filters
	 * @return array
	 */
	private function filterData($data, SplObjectStorage $filters) {
		$finalData = array();
		foreach ($data as $dataRow) {
			$allFiltersApply = true;
			// Note: the iterator is over the keys of the array!
			foreach ($filters as $columnDescriptor) {
				$value =  $filters[$columnDescriptor];
				/* @var $columnDescriptor StatsColumnDescriptorInterface */
				$result = $columnDescriptor->isFiltered($dataRow, $value);
				if (!$result) {
					$allFiltersApply = false;
					break;
				}
			}
			if ($allFiltersApply) {
				$finalData[] = $dataRow;
			}
		}
		return $finalData;
	}
	
	/**
	 * Returns the list of aggregators on column $columnDescriptor.
	 * 
	 * @param StatsColumnDescriptorInterface $columnDescriptor
	 * @return array<AggregatorDescriptorInterface>
	 */
	public function getAggregatorsForDescriptor(StatsColumnDescriptorInterface $columnDescriptor) {
		$finalArray = array();
		foreach ($this->aggregators as $aggregator) {
			/* @var $aggregator AggregatorDescriptorInterface */
			if ($aggregator->getStatsColumnDescriptor() == $columnDescriptor) {
				$finalArray[] = $aggregator;
			}
		}
		return $finalArray;
	}
}