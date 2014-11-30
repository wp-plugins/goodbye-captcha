<?php
/*
 * Copyright (C) 2014 Mihai Chelaru
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

class MchWpTaskScheduler
{
	const SECONDS_IN_DAY     = 86400;
	const SECONDS_IN_WEEK    = 604800;
	const SECONDS_IN_MONTH   = 2592000;
	const SECONDS_IN_YEAR    = 31536000;


	private $arrTasks = null;

	protected function __construct()
	{
		$this->arrTasks = array();

		add_filter('cron_schedules', array($this, 'generateCustomCronSchedules'), 10);

	}

	public function registerTask(MchWpITask $mchTask)
	{
		$this->arrTasks[] = $mchTask;
	}

	public function scheduleRegisteredTasks()
	{

		foreach($this->arrTasks as $mchTask)
		{
			add_action($mchTask->getTaskCronActionHookName(), array($mchTask, 'run'));

			if(false !== wp_next_scheduled($mchTask->getTaskCronActionHookName()))
				continue;

			$mchTask->isRecurringTask() ? wp_schedule_event( time(), $this->getFormattedRecurrence($mchTask->getRunningInterval()), $mchTask->getTaskCronActionHookName() )
										: wp_schedule_single_event($mchTask->getRunningInterval(), $mchTask->getTaskCronActionHookName() );

		}
	}

	public function unscheduleRegisteredTasks()
	{
		foreach($this->arrTasks as $mchTask)
		{
			$timestamp = wp_next_scheduled($mchTask->getTaskCronActionHookName());
			(false !== $timestamp) ? wp_unschedule_event($timestamp, $mchTask->getTaskCronActionHookName()) : null;
		}
	}


	public function generateCustomCronSchedules($arrCronSchedules)
	{
		$className = get_class();
		foreach($this->arrTasks as $mchTask)
		{
			if(!$mchTask->isRecurringTask())
				continue;

			$arrCronSchedules[$this->getFormattedRecurrence($mchTask->getRunningInterval())] = array('interval' => $mchTask->getRunningInterval(),
																					     'display'  =>  __('Every ' . $mchTask->getRunningInterval() . ' seconds'));
		}

		return $arrCronSchedules;
	}


	private function getFormattedRecurrence($interval)
	{
		return "mch-wp-$interval";
	}

	public static function getInstance()
	{
		static $taskSchedulerInstance = null;
		return null !== $taskSchedulerInstance ? $taskSchedulerInstance : $taskSchedulerInstance = new self();
	}
}

