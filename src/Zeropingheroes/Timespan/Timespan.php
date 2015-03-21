<?php namespace Zeropingheroes\Timespan;

use ExpressiveDate;

class Timespan {

	/**
	 * The start date and time of the timespan.
	 *
	 * @var ExpressiveDate
	 */
	public $start;

	/**
	 * The end date and time of the timespan.
	 *
	 * @var ExpressiveDate
	 */
	public $end;

	/**
	 * The current date and time.
	 *
	 * @var ExpressiveDate
	 */
	public $now;

	/**
	 * The tense indicator of whether the timespan is past, present or future.
	 *
	 * @var str
	 */
	public $tense;

	/**
	 * Initialise the class with a start and end date and time.
	 *
	 * @param  string  $start
	 * @param  string  $end
	 */
	public function __construct($start = NULL, $end = NULL)
	{
		if( $start != NULL && $end != NULL )
		{
			$this->start = ExpressiveDate::make($start);
			$this->end = ExpressiveDate::make($end);
			$this->now = new ExpressiveDate;

			if ( $this->start->greaterThan($this->end) )
			{
				throw new \Exception('Timespan start date is after end date');
			}

			if ( $this->start->greaterThan($this->now) )
			{
				$this->tense = 'future';
			}

			if ( $this->start->lessOrEqualTo($this->now) && $this->end->greaterOrEqualTo($this->now) )
			{
				$this->tense = 'present';
			}

			if ( $this->end->lessThan($this->now) )
			{
				$this->tense = 'past';
			}

		}
	}

	/**
	 * Format the timespan to read naturally, e.g.
	 * Saturday 9am to 10.30pm
	 *
	 * @return string
	 */
	public function summarise($start, $end)
	{
		$this->__construct($start, $end);

		// if timespan start falls on the hour, dont display minutes
		if ( $this->start->getMinute() == 0)
		{
			$startFormat = 'l ga';
		}
		else
		{
			$startFormat = 'l g:ia';
		}

		// if timespan start falls on the hour, dont display minutes
		if ( $this->end->getMinute() == 0)
		{
			$endFormat = 'ga';
		}
		else
		{
			$endFormat = 'g:ia';
		}

		// if timespan does not start and end on the same day, display the end day
		if ( $this->start->getDay() != $this->end->getDay() )
		{
			$endFormat = 'l '.$endFormat;
		}

		return $this->start->format($startFormat).' to '. $this->end->format($endFormat);
	}

	/**
	 * Describe when:
	 * a timespan is beginning (future)
	 * a timespan began and is ending (present)
	 * a timespan ended (past)
	 *
	 * @return string
	 */
	public function relativeToNow($start, $end, $words = array())
	{
		$this->__construct($start, $end);

		if( empty($words) )
		{
			$words['starting'] = 'Starting';
			$words['ending'] = 'Ending';
			$words['ended'] = 'Ended';
 		}

		if ( $this->tense == 'future' )
		{
			return $words['starting'] . ' ' . $this->start->getRelativeDate();
		}

		if ( $this->tense == 'present' )
		{
			return $words['ending'] . ' ' . $this->end->getRelativeDate();
		}

		if ( $this->tense == 'past' )
		{
			return $words['ended'] . ' ' . $this->end->getRelativeDate();
		}
	}

	/**
	 * Return the tense of a timespan (past/present/future)
	 *
	 * @return string
	 */
	public function tense($start, $end)
	{
		$this->__construct($start, $end);

		return $this->tense;
	}

}