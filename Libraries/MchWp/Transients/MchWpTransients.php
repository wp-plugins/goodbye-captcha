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

class MchWpTransients
{

	public static function get($transientKey, $isSiteTransient = false)
	{
		$cachedData =  $isSiteTransient ? get_site_transient(md5($transientKey)) : get_transient(md5($transientKey));
		if(false === $cachedData)
			return null;

		if(!isset($cachedData['d']))
			return $cachedData;

		if(!isset($cachedData['e']))
			return $cachedData['d'];

		if((int)$cachedData['e'] > (isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time()))
		{
			return $cachedData['d'];
		}

		if($cachedData['t']->isRecurringTask())
		{
			$taskClass = get_class($cachedData['t']);
			$cachedData['t'] = new $taskClass($cachedData['t']->getRunningInterval(), false);
		}

		MchWpTaskScheduler::getInstance()->registerTask($cachedData['t']);
		MchWpTaskScheduler::getInstance()->scheduleRegisteredTasks();

		set_transient( $transientKey, $cachedData );

		return $cachedData['d'];
	}

	public static function set($transientKey, $expiration, MchWpITask $updateBackgroundTask, $isSiteTransient = false) // pass 0 for permanent
	{
		if(empty($transientKey))
			return;

		$data = $updateBackgroundTask->run();
		if(empty($data))
			return;

		$arrData = array('d' => $data);
		if(0 === $expiration)
		{
			$isSiteTransient ?  set_site_transient(md5($transientKey), $arrData, $expiration) : set_transient(md5($transientKey), $arrData, $expiration) ;
			return;
		}

		$arrData['e'] = $expiration + (isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time());
		$arrData['t'] = $updateBackgroundTask;

		$isSiteTransient ?  set_site_transient(md5($transientKey), $arrData, $expiration) : set_transient(md5($transientKey), $arrData, $expiration) ;
	}
}