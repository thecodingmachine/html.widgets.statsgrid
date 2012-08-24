<?php
/*
 * Copyright (c) 2012 David Negrier
*
* See the file LICENSE.txt for copying permission.
*/

namespace Mouf\Html\Widgets\StatsGrid\Aggregate;

use Mouf\Html\Widgets\StatsGrid;

/**
 * Aggregated values descriptors.
 * 
 * @author David Negrier
 */
interface AggregatorDescriptorInterface {
	
	public function getTitle();
	
	/**
	 * Returns the value this aggregator acts upon.
	 * 
	 * @return StatsValueDescriptor
	 */
	public function getValueDescriptor();

	/**
	 * Returns the row/column descriptor this aggregator acts upon.
	 *
	 * @return StatsColumnDescriptor
	 */
	public function getStatsColumnDescriptor();

	/**
	 * Returns the aggregated value to be displayed.
	 */
	public function getAggregatedValue(array $values);
}