<?php
/** 
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

final class GdbcTaskScheduler
{
	private static function getGdbcTasks()
	{
		static $arrGdbcTasks = array();
		if(!empty($arrGdbcTasks))
			return $arrGdbcTasks;

		$arrGdbcTasks[] = new GdbcLogsCleanerTask(MchWpTaskScheduler::SECONDS_IN_DAY, true);

		return $arrGdbcTasks;
	}

	public static function scheduleGdbcTasks()
	{
		foreach(self::getGdbcTasks() as $gdbcTask)
		{
			MchWpTaskScheduler::getInstance()->registerTask($gdbcTask);
		}

		MchWpTaskScheduler::getInstance()->scheduleRegisteredTasks();
	}

	public static function unscheduleGdbcTasks()
	{
		foreach(self::getGdbcTasks() as $gdbcTask)
			MchWpTaskScheduler::getInstance()->registerTask($gdbcTask);

		MchWpTaskScheduler::getInstance()->unscheduleRegisteredTasks();
	}

	private function __construct(){}
	private function __clone(){}
} 