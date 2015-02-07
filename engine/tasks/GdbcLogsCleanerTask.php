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
class GdbcLogsCleanerTask extends MchWpTask
{
	public function __construct($runningInterval, $isRecurring)
	{
		parent::__construct($runningInterval, $isRecurring);
	}

	public function run()
	{
		global $wpdb;

		$maxLogDays = GoodByeCaptcha::getModulesControllerInstance()->getModuleSettingOption(GdbcModulesController::MODULE_SETTINGS, GdbcSettingsAdminModule::OPTION_MAX_LOGS_DAYS);
		if(null === $maxLogDays)
			return;

		$maxDate = date('Y-m-d H:i:s',  strtotime(((-1) * (abs($maxLogDays))) . ' days', current_time( 'timestamp' )));

		$attemptEntity = new GdbcAttemptEntity();

		$sqlQuery = 'SELECT ModuleId, COUNT(ModuleId) AS BlockedAttempts FROM ' . $attemptEntity->getTableName() . ' WHERE IsDeleted = 0 AND CreatedDate < %s GROUP BY ModuleId';
		$preparedQuery = $wpdb->prepare($sqlQuery, $maxDate);
		$result = MchWpDbManager::executePreparedQuery($preparedQuery);

		$arrSavedBlockedAttempts = get_site_option( 'gdbc-blocked-attempts', array() );
		$currentBlogId = get_current_blog_id();

		foreach($result as $moduleBlockedAttempts)
		{
			isset($arrSavedBlockedAttempts[$currentBlogId][$moduleBlockedAttempts->ModuleId]) ?
				$arrSavedBlockedAttempts[$currentBlogId][$moduleBlockedAttempts->ModuleId] += $moduleBlockedAttempts->BlockedAttempts :
				$arrSavedBlockedAttempts[$currentBlogId][$moduleBlockedAttempts->ModuleId]  = $moduleBlockedAttempts->BlockedAttempts;
		}

		update_site_option('gdbc-blocked-attempts', $arrSavedBlockedAttempts);

		$sqlQuery = 'UPDATE ' . $attemptEntity->getTableName() . ' SET IsDeleted = 1
					WHERE Id NOT IN (SELECT Id FROM (SELECT MAX(Id) AS Id
					FROM ' . $attemptEntity->getTableName() . '
					WHERE IsDeleted = 0 AND
					IsIpBlocked = 1 AND CreatedDate < %s
					GROUP BY ClientIp) AllClientIps)';

		$preparedQuery = $wpdb->prepare($sqlQuery, $maxDate);

		MchWpDbManager::executePreparedQuery($preparedQuery);

		$sqlQuery = 'Select Count(*) As SoftDeleted FROM ' . $attemptEntity->getTableName() . ' WHERE IsDeleted <> 0';
		$result = MchWpDbManager::executePreparedQuery($sqlQuery);

		if(isset($result[0]) && (int)$result[0]->SoftDeleted > 1000)
		{
			$sqlQuery = 'DELETE FROM ' . $attemptEntity->getTableName() . ' WHERE IsDeleted <> 0 ORDER BY Id DESC LIMIT %d';
			$preparedQuery = $wpdb->prepare($sqlQuery, (int)$result[0]->SoftDeleted - 1000);
			MchWpDbManager::executePreparedQuery($preparedQuery);
		}

	}
}