<?php
/*
 * Copyright (c) 2012 David Negrier
 *
 * See the file LICENSE.txt for copying permission.
 */

namespace Mouf\Html\Widgets\StatsGrid\Aggregate;

use Mouf\Html\Widgets\StatsGrid;

/**
 * The most simple aggregator: it performs sums of the values and displays the total.
 * 
 * @author David Négrier
 * @Component
 */
class SumAggregator implements AggregatorDescriptorInterface {
	
	private $statsColumnDescriptor;
	private $statsValueDescriptor;
	private $title;
	
	public function __construct(StatsColumnDescriptor $statsColumnDescriptor, StatsValueDescriptor $statsValueDescriptor, $title) {
		$this->statsColumnDescriptor = $statsColumnDescriptor;
		$this->statsValueDescriptor = $statsValueDescriptor;
		$this->title = $title;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see AggregatorDescriptorInterface::getTitle()
	 */
	public function getTitle() {
		return $this->title;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see AggregatorDescriptorInterface::getValueDescriptor()
	 */
	public function getValueDescriptor() {
		return $this->statsValueDescriptor;
	}

	/**
	 * (non-PHPdoc)
	 * @see AggregatorDescriptorInterface::getStatsColumnDescriptor()
	 */
	public function getStatsColumnDescriptor() {
		return $this->statsColumnDescriptor;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see AggregatorDescriptorInterface::getAggregatedValue()
	 */
	public function getAggregatedValue(array $values) {
		$self = $this;
		return array_sum(array_map(function($value) use($self) {
			return $self->getValueDescriptor()->getValue($value);	
		}, $values));
	}
}