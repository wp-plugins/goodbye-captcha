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


spl_autoload_register('MchWp::loadLibrary');

final class MchWp
{
	public static function loadLibrary($className)
	{
		static $arrClassMap = array(
			
			'MchWpIBase'              => '/Base/MchWpIBase.php',
			'MchWpBase'               => '/Base/MchWpBase.php',

			'MchWpIModule'            => '/Module/MchWpIModule.php',
			'MchWpModule'             => '/Module/MchWpModule.php',
			'MchWpAdminModule'        => '/Module/MchWpAdminModule.php',
			'MchWpPublicModule'       => '/Module/MchWpPublicModule.php',
			
			'MchWpISetting'           => '/Setting/MchWpISetting.php',
			'MchWpSetting'            => '/Setting/MchWpSetting.php',
			'MchWpSettingSection'     => '/Setting/MchWpSettingSection.php',
			'MchWpSettingField'       => '/Setting/MchWpSettingField.php',

			
			'MchWpIPlugin'            => '/Plugin/MchWpIPlugin.php',
			'MchWpPlugin'             => '/Plugin/MchWpPlugin.php',
			'MchWpAdminPlugin'        => '/Plugin/MchWpAdminPlugin.php',
			'MchWpPublicPlugin'       => '/Plugin/MchWpPublicPlugin.php',
			
			'MchWpIController'        => '/Controller/MchWpIController.php',
			'MchWpModulesController'  => '/Controller/MchWpModulesController.php',
			'MchWpSettingsController' => '/Controller/MchWpSettingsController.php',
			
			'MchWpUtil'               => '/Util/MchWpUtil.php',
			'MchWpUtilHtml'           => '/Util/MchWpUtilHtml.php',
			
		);
		
		return isset($arrClassMap[$className]) ? file_exists($filePath = dirname(__FILE__) . $arrClassMap[$className]) 
											   ? include_once ($filePath) 
											   : null 
											   : null;
	}
	
	
	public static function isUserLoggedIn()
	{
		return MchWpBase::isUserLoggedIn();
	}
	
	
	public static function isAdminLoggedIn()
	{
		return MchWpBase::isAdminLoggedIn();
	}
	
	public static function isUserInDashboad()
	{
		return MchWpBase::isUserInDashboad();
	}
	
	public static function isAdminInDashboard()
	{
		return MchWpBase::isAdminInDashboard();
	}
	

	private function __clone()
	{}
	private function __construct()
	{}
	
}
