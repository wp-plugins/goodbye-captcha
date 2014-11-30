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

final class GdbcAttemptsManager
{
	
	private function __construct()
	{}

	public static function getTopAttemptsList($totalItems)
	{
		global $wpdb;
		$attemptEntity = new GdbcAttemptEntity();
		$itemsQuery = $wpdb->prepare("SELECT * FROM " . $attemptEntity->getTableName() . " WHERE IsDeleted = 0 ORDER BY CreatedDate DESC LIMIT 0, %d", $totalItems);
		return MchWpDbManager::executePreparedQuery($itemsQuery);
	}

	/**
	 * Gets total of attempts grouped by Modules' default sections
	 * @param $arrPlugInfo - plugin arrPlugInfo
	 * @param $numberOfDays - number of days on which the query runs
	 * @param $dayOfReference - day of reference it's the last day of the interval
	 * Example 1: Current date is 31-01-2014 $numberOfDays = 30 and $dayOfReference = 0 --> the query runs from 02-01-2014 - 31-01-2014 (30 days)
	 * Example 2: Current date is 31-01-2014 $numberOfDays = 30 and $dayOfReference = -1 --> the query runs from 01-01-2014 - 30-01-2014 (30 days)
	 * @return results of the query
	 */
	public static function getTotalAttemptsPerModuleSection($moduleId, $moduleSectionOptionsInfo, $numberOfDays, $dayOfReference)
	{
		$attemptEntity = new GdbcAttemptEntity();

		$itemsQuery = 'SELECT ';
		foreach($moduleSectionOptionsInfo as $sectionOption => $sectionOptionInfo)
		{
			$itemsQuery .=  'SUM(CASE SectionId WHEN ' . $sectionOptionInfo['id'] . ' THEN 1 ELSE 0 END) AS \'' . $sectionOptionInfo['display-text'] . '\', ';
		}

        $curDate = current_time('mysql');
		$itemsQuery  = rtrim($itemsQuery, ', ') . ' FROM ' . $attemptEntity->getTableName();
		$itemsQuery .= ' WHERE IsDeleted = 0 AND ModuleId = ' . $moduleId ;
		$itemsQuery .= ' AND DATE_SUB(DATE_SUB(\'' . $curDate . '\', INTERVAL ' . $dayOfReference . ' DAY) ,INTERVAL ' . $numberOfDays .  ' DAY) < CreatedDate AND DATE_SUB(\'' . $curDate . '\', INTERVAL ' .$dayOfReference. ' DAY) >= CreatedDate' ;

		return MchWpDbManager::executePreparedQuery($itemsQuery);
	}

	/**
	 * Gest total of attempts grouped by module
	 * @param $arrPlugInfo - plugin arrPlugInfo
	 * @param $numberOfDays - number of days on which the query runs
	 * @param $dayOfReference - day of reference it's the last day of the interval
	 * Example 1: Current date is 31-01-2014 $numberOfDays = 30 and $dayOfReference = 0 --> the query runs from 02-01-2014 - 31-01-2014 (30 days)
	 * Example 2: Current date is 31-01-2014 $numberOfDays = 30 and $dayOfReference = -1 --> the query runs from 01-01-2014 - 30-01-2014 (30 days)
	 * @return results of the query
	 */
	public static function getTotalAttemptsPerModule($arrModulesIds, $numberOfDays, $dayOfReference)
	{
		$attemptEntity = new GdbcAttemptEntity();

		$itemsQuery = 'SELECT ';
		foreach($arrModulesIds as $moduleName => $moduleId)
		{
			//$moduleId = $moduleController->getModuleId($moduleKey);
			$itemsQuery .=  'SUM(CASE ModuleId WHEN ' . $moduleId . ' THEN 1 ELSE 0 END) AS \'' . $moduleName . '\', ';
		}

		$itemsQuery = rtrim($itemsQuery, ', ') . ' FROM ' . $attemptEntity->getTableName();
        $curDate = current_time('mysql');
		$itemsQuery .= ' WHERE IsDeleted = 0 AND DATE_SUB(DATE_SUB(\'' . $curDate . '\', INTERVAL ' . $dayOfReference . ' DAY) ,INTERVAL ' . $numberOfDays .  ' DAY) <= CreatedDate AND DATE_SUB(\'' . $curDate.   '\', INTERVAL ' .$dayOfReference. ' DAY) >= CreatedDate' ;
		return MchWpDbManager::executePreparedQuery($itemsQuery);


	}

	public static function getTotalNumberOfAttemptsPerModule($moduleId)
	{
		static $arrModuleAttempts = array();
		if(isset($arrModuleAttempts[$moduleId]))
			return $arrModuleAttempts[$moduleId];

		global $wpdb;
		$attemptEntity = new GdbcAttemptEntity();
		$query = $wpdb->prepare('SELECT COUNT(1) AS Total FROM ' . $attemptEntity->getTableName() . ' WHERE IsDeleted = 0 AND ModuleId = %d ', $moduleId);

		$queryResult =  MchWpDbManager::executePreparedQuery($query);

		return $arrModuleAttempts[$moduleId] = (isset($queryResult[0])) ? (int)$queryResult[0]->Total : 0;
	}

	public static function moduleHasAttempts($moduleId)
	{
		return (0 !== self::getTotalNumberOfAttemptsPerModule($moduleId));
	}

	public static function getAttemptsPerModule($moduleId, $shouldAddSections, $pageNumber, $recordsPerPage, $orderBy)
	{
		if ($pageNumber < 1)
			$pageNumber = 1;

//		$orderByQry = '';
//		if (null != $orderBy)
//			$orderByQry = 'ORDER BY ' . $orderBy;

		$orderByQry = 'ORDER BY CreatedDate';

		global $wpdb;
		$attemptEntity = new GdbcAttemptEntity();
		$itemsQuery = '';
		if ($shouldAddSections)
			$itemsQuery = $wpdb->prepare('SELECT IsIpBlocked, SectionId, CreatedDate, CountryId, ClientIp  FROM ' . $attemptEntity->getTableName() . ' WHERE IsDeleted = 0 AND ModuleId = %d ' . $orderByQry .  ' DESC LIMIT %d, %d', $moduleId, ($pageNumber-1) * $recordsPerPage, $recordsPerPage);
		else
			$itemsQuery = $wpdb->prepare('SELECT IsIpBlocked, CreatedDate, CountryId, ClientIp FROM ' . $attemptEntity->getTableName() . ' WHERE IsDeleted = 0 AND ModuleId = %d ' . $orderByQry .  '  DESC LIMIT %d, %d', $moduleId, ($pageNumber-1) * $recordsPerPage, $recordsPerPage);
		return MchWpDbManager::executePreparedQuery($itemsQuery);
	}

	public static function getTopAttemptsLocations($moduleSectionOptionsInfo, $itemsPerPage)
	{
		$attemptEntity = new GdbcAttemptEntity();

		$itemsQuery = 'SELECT CountryId, ';
		$sumQuery = '';
		$totalQuery = '';
		foreach($moduleSectionOptionsInfo as $sectionOption => $sectionOptionInfo)
		{
			$sumQuery .=  'SUM(CASE SectionId WHEN ' . $sectionOptionInfo['id'] . ' THEN 1 ELSE 0 END) AS \'' . str_replace(' ', '', $sectionOptionInfo['display-text']) . '\',';
			$totalQuery .= 'SUM(CASE SectionId WHEN ' . $sectionOptionInfo['id'] . ' THEN 1 ELSE 0 END)' . '+';
		}
		$sumQuery    = rtrim($sumQuery, ',');
		$totalQuery  = rtrim($totalQuery, '+');
		$totalQuery  = '(' . $totalQuery . ') AS Total';
		$itemsQuery .= $sumQuery. ', ' . $totalQuery;
		$itemsQuery .= ' FROM ' . $attemptEntity->getTableName() . ' WHERE IsDeleted = 0 GROUP BY CountryId ORDER BY Total DESC, CountryId ASC LIMIT 0, %d';

		global $wpdb;
		$query = $wpdb->prepare($itemsQuery, $itemsPerPage);
		return MchWpDbManager::executePreparedQuery($query);
	}

	public static function getAttemptsChartArray($numberOfAttemptsDays)
	{
		global $wpdb;
        $curDate = current_time('mysql');
		$attemptEntity = new GdbcAttemptEntity();
		$itemsQuery = $wpdb->prepare("SELECT
						DATE_FORMAT(DATE(CreatedDate),'%%Y-%%m-%%d') AS CreatedDate,
						COUNT(Id) AS AttemptsNumber
						FROM " . $attemptEntity->getTableName() .
			" WHERE IsDeleted = 0 AND DATEDIFF('$curDate',CreatedDate) <= %d
							GROUP BY DATE(CreatedDate)
						ORDER BY CreatedDate DESC", $numberOfAttemptsDays);
		return MchWpDbManager::executePreparedQuery($itemsQuery);
	}

	public static function getAttemptsArrayByModuleAndDay($startDate, $endDate)
	{
		global $wpdb;
		$startDate = date( 'Y-m-d', $startDate);
		$endDate = date( 'Y-m-d', $endDate);

		$attemptEntity = new GdbcAttemptEntity();
		$itemsQuery = $wpdb->prepare("SELECT ModuleId, SectionId,
						DATE_FORMAT(DATE(CreatedDate),'%%Y-%%m-%%d') AS CreatedDate,
						COUNT(Id) AS AttemptsNumber
						FROM " . $attemptEntity->getTableName() .
			" WHERE IsDeleted = 0 AND %s <= DATE(CreatedDate) AND %s >= DATE(CreatedDate)
							GROUP BY ModuleId, DATE(CreatedDate)
						ORDER BY ModuleId ASC, CreatedDate DESC", $startDate, $endDate);
		return MchWpDbManager::executePreparedQuery($itemsQuery);
	}

	public static function isClientIpBlocked($ipAddress)
	{
		global $wpdb;
		$attemptEntity = new GdbcAttemptEntity();

		$ipAddress = MchHttpUtil::ipAddressToBinary($ipAddress);
		if(null === $ipAddress)
			return false;

		$sqlStatement = $wpdb->prepare('SELECT 1 FROM ' . $attemptEntity->getTableName() . ' WHERE ClientIp = %s AND IsDeleted = 0 AND IsIpBlocked <> 0 LIMIT 1', $ipAddress);
		$queryResult  = MchWpDbManager::executePreparedQuery($sqlStatement);

		return !empty($queryResult);
	}

	public static function getAttemptById($attemptId)
	{
		global $wpdb;
		$attemptEntity = new GdbcAttemptEntity();
		$itemsQuery = $wpdb->prepare("SELECT * FROM " . $attemptEntity->getTableName() .
			" WHERE IsDeleted = 0 AND Id = %d", $attemptId);

		return MchWpDbManager::executePreparedQuery($itemsQuery);
	}

	public static function getModulesTotalAttempts()
	{
		$query = 'SELECT ModuleId As ModuleId, COUNT(1) as Total FROM wp_gdbc_attempts WHERE IsDeleted = 0 GROUP BY ModuleId';

		return MchWpDbManager::executePreparedQuery($query);
	}

	public static function getTopIpAttempts($numberOfItems)
	{
		global $wpdb;
		$query = $wpdb->prepare('SELECT ClientIp, COUNT(ClientIp) AS Total, MIN(CountryId) AS CountryId, MIN(IsIpBlocked) AS IsIpBlocked FROM wp_gdbc_attempts WHERE IsDeleted = 0 GROUP BY ClientIp ORDER BY COUNT(ClientIp) DESC, MIN(CountryId) LIMIT 0, %d', $numberOfItems);

		return MchWpDbManager::executePreparedQuery($query);
	}

	public static function manageIp($ip, $shouldBlock)
	{
		if(!MchHttpUtil::isPublicIpAddress($ip) || $ip === MchHttpUtil::getServerIPAddress())
			return false;

		global $wpdb;
		$attemptEntity = new GdbcAttemptEntity();
		$query = 'UPDATE ' . $attemptEntity->getTableName() . ' SET IsIpBlocked = %d WHERE ClientIp = %s';
		$preparedQuery = $wpdb->prepare($query, $shouldBlock, MchHttpUtil::ipAddressToBinary($ip));

		return  MchWpDbManager::executePreparedQuery($preparedQuery);
	}

	public static function attemptsTableExists()
	{
		global $wpdb;
		$attemptEntity = new GdbcAttemptEntity();
		$tableName = $attemptEntity->getTableName();

		return ($wpdb->get_var($wpdb->prepare("show tables like %s", $tableName)) === $tableName);
	}


	public static function getSoftDeletedAttempt()
	{
		global $wpdb;
		$attemptEntity = new GdbcAttemptEntity();
		$itemsQuery = $wpdb->prepare('SELECT * FROM ' . $attemptEntity->getTableName() . ' WHERE IsDeleted > 0  LIMIT %d', 1);

		$arrAttemptEntity = MchWpDbManager::executePreparedQuery($itemsQuery);
		if(!isset($arrAttemptEntity[0]))
			return null;

		$attemptEntity = new GdbcAttemptEntity();
		foreach($arrAttemptEntity[0] as $property => &$value)
		{
			$attemptEntity->$property = $value;
		}

		return $attemptEntity;
	}

	public static function createAttempt(GdbcAttemptEntity $attemptEntity)
	{
		if(!empty($attemptEntity->ClientIp))
			$attemptEntity->ClientIp = MchHttpUtil::ipAddressToBinary($attemptEntity->ClientIp);
		else
			unset($attemptEntity->ClientIp);

		return MchWpDbManager::create($attemptEntity, false);
	}

	public static function saveAttempt(GdbcAttemptEntity $attemptEntity)
	{
		!empty($attemptEntity->ClientIp) ? $attemptEntity->ClientIp = MchHttpUtil::ipAddressToBinary($attemptEntity->ClientIp) : null;

		return MchWpDbManager::save($attemptEntity, false);
	}

	public static function attemptExists()
	{
		static $attemptExists = null;
		if(null !== $attemptExists)
			return $attemptExists;

		global $wpdb;
		$attemptEntity = new GdbcAttemptEntity();

		return $attemptExists = (null !== $wpdb->get_row('SELECT Id FROM ' . $attemptEntity->getTableName() . ' WHERE IsDeleted = 0 LIMIT 1', ARRAY_N));
	}


}
