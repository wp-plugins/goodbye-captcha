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

final class GdbcModulesController extends MchWpModulesController
{
	CONST MODULE_DEFAULT        = 'GdbcDefaultModule'; 
	CONST MODULE_JETPACK        = 'GdbcJetPackModule';
	CONST MODULE_BUDDY_PRESS    = 'GdbcBuddyPressModule';
	CONST MODULE_POPULAR_FORMS  = 'GdbcPopularFormsModule'; //Gravity Forms, CForm7, Ninja Forms, Formidable Forms
	
	private static $arrModules = array(

		self::MODULE_DEFAULT =>array(
			'GdbcDefaultAdminModule'  => '/modules/default/GdbcDefaultAdminModule.php',
			'GdbcDefaultPublicModule' => '/modules/default/GdbcDefaultPublicModule.php',
		),
		
		self::MODULE_JETPACK =>array(
			'GdbcJetPackAdminModule'  => '/modules/jetpack/GdbcJetPackAdminModule.php',
			'GdbcJetPackPublicModule' => '/modules/jetpack/GdbcJetPackPublicModule.php',
		),
		
		self::MODULE_BUDDY_PRESS => array(
			'GdbcBuddyPressAdminModule'  => '/modules/buddy-press/GdbcBuddyPressAdminModule.php',
			'GdbcBuddyPressPublicModule' => '/modules/buddy-press/GdbcBuddyPressPublicModule.php',
		),
		
		self::MODULE_POPULAR_FORMS =>array(
			'GdbcPopularFormsAdminModule'  => '/modules/popular-forms/GdbcPopularFormsAdminModule.php',
			'GdbcPopularFormsPublicModule' => '/modules/popular-forms/GdbcPopularFormsPublicModule.php',
		),
		
	);
	
	protected function __construct(array $arrPluginInfo)
	{
		parent::__construct($arrPluginInfo);
		
	}
	
	
	public function getRegisteredModules()
	{
		static $isFilePathNormalized = false;
		
		if($isFilePathNormalized)
			return self::$arrModules;
		
		foreach (self::$arrModules as $moduleName => &$arrModuleClassesInfo)
		{
			if(empty($arrModuleClassesInfo))
				continue;
			
			foreach ($arrModuleClassesInfo as $className => &$filePath)
			{
				$dirPath = MchWpUtil::stripLeftSlashes(dirname($filePath));
				$filePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . $dirPath . DIRECTORY_SEPARATOR . basename($filePath);
				
				if(file_exists($filePath))
					continue;
				
				unset(self::$arrModules[$moduleName]);
			}
		}

		$isFilePathNormalized = true;
		
		return self::$arrModules;
	}

	/**
	 * 
	 * @staticvar null $instance
	 * @return \GdbcModulesController
	 */
	public static function getInstance(array $arrPluginInfo)
	{
		static $arrInstances = array();
		$instanceKey         = implode('', $arrPluginInfo);
		return isset($arrInstances[$instanceKey]) ? $arrInstances[$instanceKey] : $arrInstances[$instanceKey] = new self($arrPluginInfo);
	}

}