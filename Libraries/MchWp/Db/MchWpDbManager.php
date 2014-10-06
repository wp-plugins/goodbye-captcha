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

class MchWpDbManager implements MchWpIDb
{
	CONST OUTPUT_TYPE_OBJECT    = 1;//OBJECT;
	CONST OUTPUT_TYPE_OBJECT_K  = 2;//OBJECT_K;
	CONST OUTPUT_TYPE_ARRAY_A   = 3;//ARRAY_A;
	CONST OUTPUT_TYPE_ARRAY_N   = 4;//ARRAY_N;


	/**
	 * @param string $tableName Table name to be created
	 * @param string $sqlStatement The CREATE TABLE sql statement
	 *
	 * @return bool
	 */
	public static function createTable($tableName, $sqlStatement)
	{
		global $wpdb;

		if($wpdb->get_var($wpdb->prepare("show tables like %s", $tableName)) == $tableName)
			return false;

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		$result = dbDelta($sqlStatement);
		return !empty($result) ? true : false;
	}

	public static function executePreparedQuery($sqlQuery)
	{
		if(empty($sqlQuery))
			return null;

		global $wpdb;
		return false !== ($queryResult = $wpdb->query($sqlQuery)) ? $queryResult : null;

	}

	/**
	 * @param \MchWpIEntity $mchWpEntity
	 * @param int           $outputType
	 *
	 * @return \MchWpIEntity|null
	 */
	public static function retrieveByPK(MchWpIEntity $mchWpEntity, $outputType = self::OUTPUT_TYPE_OBJECT)
	{
		$pkValue = $mchWpEntity->{$mchWpEntity->getPrimaryKey()};
		if(!isset($pkValue))
			return null;

		global $wpdb;

		$resultObject = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $mchWpEntity->getTableName() . ' WHERE ' . $mchWpEntity->getPrimaryKey() .' = %d', $pkValue), self::getQueryOutputType($outputType));

		if(null === $resultObject)
			return null;

		return null !== $resultObject ? $resultObject : null;
		//return self::castResultToNewObject($resultObject, get_class($mchWpEntity));

	}

	public static function retrieveByEntityProperties(MchWpIEntity $mchWpEntity, $outputType = self::OUTPUT_TYPE_OBJECT, $shouldCast = false)
	{
		$arrProperties = array_filter((array)$mchWpEntity, 'strlen');

		if(empty($arrProperties))
			return array();

		global $wpdb;

		$selectStatement  = 'SELECT * FROM ' . $mchWpEntity->getTableName() . ' WHERE ';
		$selectStatement .= implode('= %s,', array_keys($arrProperties));
		$selectStatement .= '= %s';

		$results = $wpdb->get_results($wpdb->prepare($selectStatement, array_values($arrProperties)), self::getQueryOutputType($outputType));

		return null !== $results ? $results : array();
	}

	/**
	 * @param \MchWpIEntity $mchWpEntity
	 * @param bool          $shouldFilterValues
	 *
	 * @return int Number of affected rows or zero
	 */
	public static function save(MchWpIEntity $mchWpEntity, $shouldFilterValues = true)
	{
		if(empty($mchWpEntity))
			return 0;

		$arrEntity = (array)$mchWpEntity;
		(bool)$shouldFilterValues ? $arrEntity = array_filter($arrEntity, 'strlen') : null;

		if(!isset($arrEntity[$mchWpEntity->getPrimaryKey()]))
			return 0;

		global $wpdb;

		$updateResult = $wpdb->update($mchWpEntity->getTableName(), $arrEntity, array($mchWpEntity->getPrimaryKey() => $arrEntity[$mchWpEntity->getPrimaryKey()]));

		return false === $updateResult ? 0 : $updateResult;

	}

	/**
	 * @param \MchWpIEntity $mchWpEntity
	 * @param bool          $shouldFilterValues
	 *
	 * @return int Last inserted ID or zero
	 */
	public static function create(MchWpIEntity $mchWpEntity, $shouldFilterValues = true)
	{
		if(empty($mchWpEntity))
			return 0;

		$arrEntity = (array)$mchWpEntity;
		(bool)$shouldFilterValues ? $arrEntity = array_filter($arrEntity, 'strlen') : null;

		if(isset($arrEntity[$mchWpEntity->getPrimaryKey()]))
			return 0;

		global $wpdb;

		return false === $wpdb->insert($mchWpEntity->getTableName(), $arrEntity) ? 0 : $wpdb->insert_id;
	}

	/**
	 * @param \MchWpIEntity $mchWpEntity
	 *
	 * @return int Number of deleted rows or zero
	 */
	public static function delete(MchWpIEntity $mchWpEntity)
	{
		if(empty($mchWpEntity))
			return 0;

		$arrEntity = (array)$mchWpEntity;
		if(!isset($arrEntity[$mchWpEntity->getPrimaryKey()]))
			return 0;

		global $wpdb;
		return false === $wpdb->delete($mchWpEntity->getTableName(), array($mchWpEntity->getPrimaryKey() => $arrEntity[$mchWpEntity->getPrimaryKey()])) ? 0 : 1;
	}


	private static function getQueryOutputType($outputType)
	{
		if(self::OUTPUT_TYPE_OBJECT === $outputType)
			return OBJECT;

		if(self::OUTPUT_TYPE_OBJECT_K === $outputType)
			return OBJECT_K;

		if(self::OUTPUT_TYPE_ARRAY_A === $outputType)
			return ARRAY_A;

		if(self::OUTPUT_TYPE_ARRAY_N === $outputType)
			return ARRAY_N;

		return OBJECT;
	}



//	/**
//	 * @param \stdObject $resultObject The result object
//	 * @param string $newObjectClassName The class name of the new object we want to cast to
//	 *
//	 * @return \MchWpIEntity An instance of the $newObjectClassName
//	 */
//	private static function castResultToNewObject($resultObject, $newObjectClassName)
//	{
//		$object = new $newObjectClassName;
//		foreach($resultObject as $property => &$value)
//		{
//			$object->$property = $value;
//		}
//
//		return $object;
//	}


	private function __clone(){}
	private function __construct(){}

}