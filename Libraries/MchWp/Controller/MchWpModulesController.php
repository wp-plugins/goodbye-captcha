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

abstract class MchWpModulesController extends MchWpBase implements MchWpIController
{
	public abstract function getRegisteredModules();

	protected function __construct(array $arrPluginInfo) 
	{
		if(null === $this->getRegisteredModules())
		{
			throw new Exception('Please implement the getRegiesteredModulesList method');
		}
		
		parent::__construct($arrPluginInfo);
		
		spl_autoload_register(array($this, 'autoLoadModuleClass'), false);
		
		
	}

	public function activatePublicModule($moduleName, MchWpPublicPlugin $publicPlugin)
	{
		$module = $this->getModuleInstance($moduleName, MchWpModule::MODULE_TYPE_PUBLIC);
		
		return (null !== $module && !empty($publicPlugin)) ? $module->activate($publicPlugin) : null;
	}
	
	
	public function getModuleSettingOption($moduleName, $optionName)
	{
		$module = $this->getModuleInstance($moduleName, MchWpModule::MODULE_TYPE_ADMIN);
		return (null === $module) ? null : $module->getModuleSetting()->getSettingOption($optionName);
	}

	/**
	 * 
	 * @param string $moduleName
	 * @return \MchWpSetting | null
	 */
	public function getModuleSetting($moduleName)
	{
		$module = $this->getModuleInstance($moduleName, MchWpModule::MODULE_TYPE_ADMIN);
		return (null === $module) ? null : $module->getModuleSetting();
	}
	

	public function getModuleSettingSections($moduleName)
	{
		$module = $this->getModuleInstance($moduleName, MchWpModule::MODULE_TYPE_ADMIN);
		return (null === $module) ? null : $module->getModuleSetting()->getSettingSections();
	}
	
	
	public function autoLoadModuleClass($className)
	{ 
		foreach ($this->getRegisteredModules() as $moduleName => $arrClassesInfo)
		{
			foreach ($arrClassesInfo as $filePath)
			{
				if(!isset($arrClassesInfo[$className]))
					continue;

				return include_once $arrClassesInfo[$className];
			}
		}
		
		return null;
	}
	
	
	/**
	 * 
	 * @staticvar array $arrInstances
	 * @param string $moduleName
	 * @param int $moduleType
	 * @return \MchWpModule | null
	 */
	public function getModuleInstance($moduleName, $moduleType)
	{
		static $arrInstances = array();
		
		if( isset($arrInstances[$moduleName][$moduleType]) )
		{
			return $arrInstances[$moduleName][$moduleType];		
		}

		foreach ($this->getRegisteredModules() as $module => $arrClassesInfo)
		{
			if($moduleName !== $module)
				continue;

			foreach ($arrClassesInfo as $moduleClassName => $filePath)
			{
				$moduleInstance = call_user_func_array($moduleClassName . '::getInstance', array($this->ArrPluginInfo));
				if(false === $moduleInstance)
					continue;
				
				switch($moduleInstance instanceof MchWpAdminModule)
				{
					case true : if($moduleType === MchWpModule::MODULE_TYPE_ADMIN) 
									$arrInstances[$moduleName][$moduleType] = $moduleInstance;
								break ;
					
					case false : if($moduleType === MchWpModule::MODULE_TYPE_PUBLIC) 
									$arrInstances[$moduleName][$moduleType] = $moduleInstance;
								break;
				}
				
				if(!isset($arrInstances[$moduleName][$moduleType]))
					continue;
				
				return $arrInstances[$moduleName][$moduleType];
			}

		}
		
		return isset($arrInstances[$moduleName][$moduleType]) ? $arrInstances[$moduleName][$moduleType] : null;
		
	}

	/**
	 * @param string $moduleName Module name
	 *
	 * @return \MchWpAdminModule|null
	 */
	public function getAdminModuleInstance($moduleName)
	{
		return $this->getModuleInstance($moduleName, MchWpModule::MODULE_TYPE_ADMIN);
	}

	/**
	 * @param string $moduleName Module name
	 *
	 * @return \MchWpPublicModule|null
	 */
	public function getPublicModuleInstance($moduleName)
	{
		return $this->getModuleInstance($moduleName, MchWpModule::MODULE_TYPE_PUBLIC);
	}


	/**
	 * @param $moduleName Module name
	 *
	 * @return bool
	 */
	public function isModuleRegistered($moduleName)
	{
		return null === $this->getModuleInstance($moduleName, MchWpModule::MODULE_TYPE_PUBLIC) ? false : true;
	}
	
	
}