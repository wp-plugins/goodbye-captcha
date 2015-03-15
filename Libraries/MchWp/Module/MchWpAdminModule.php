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

abstract class MchWpAdminModule extends MchWpModule
{
	/**
	 *
	 * @var array Holds Module Setting 
	 */
	private $arrModuleSettings = null;
	
	public abstract function validateModuleSetting($arrSettingOptions);
	public abstract function renderModuleSettingSection(array $arrSectionInfo);
	public abstract function renderModuleSettingField(array $arrSettingField);

	
	
	/**
	 * 
	 * @return \MchWpSetting
	 */
	public abstract function getModuleSetting();
	public abstract function getModuleSettingTabCaption();
	
	protected abstract function getModuleSettingSections();
	
	
	protected function __construct(array $arrPluginInfo)
	{
		if(null === ($moduleSetting = $this->getModuleSetting()))
			throw new Exception ('Please implement getModuleSetting method! ');
		
		if( ! ($moduleSetting instanceof MchWpSetting) )
			throw new Exception ('The getModuleSetting method should return an instance of MchWpSetting class! ');

		parent::__construct($arrPluginInfo);
	}	

	public function activateAdminSetting()
	{
		$moduleSetting = $this->getModuleSetting();
		
		$this->registerSetting($moduleSetting);
		
		add_filter('pre_update_option_' . $moduleSetting->SettingKey, array($this, 'filterOptionsBeforeSave'), 10, 2);	

	}

	public function setSettingOption($settingOptionName, $settingOptionValue)
	{
		foreach($this->filterOptionsBeforeSave(array($settingOptionName => $settingOptionValue), $this->getModuleSetting()->getAllSavedOptions()) as $optionName => $optionValue)
		{
			$this->getModuleSetting()->setSettingOption($optionName, $optionValue);
		}
	}

	public function getSettingOption($settingOptionName)
	{
		return $this->getModuleSetting()->getSettingOption($settingOptionName);
	}

	public function filterOptionsBeforeSave($arrNewSettings, $arrOldSettings)
	{
		if($this->getModuleSetting()->hasErrors())
			return $arrOldSettings;

		$arrNewSettings = !empty($arrNewSettings) ? (array)$arrNewSettings : array();
		$arrOldSettings = !empty($arrOldSettings) ? (array)$arrOldSettings : $this->getModuleSetting()->getDefaultOptions(); 

		$arrSettings = array_merge($this->getModuleSetting()->getDefaultOptions(), $arrNewSettings);
		
		$arrSettings = array_merge($arrOldSettings, $arrSettings);

		return $arrSettings;
	}

	
	
	/**
	 * 
	 * @param MchWpSetting $setting
	 * @throws Exception
	 */
	private function registerSetting(MchWpSetting $setting)
	{
		if(empty($setting->SettingKey) || isset($this->arrModuleSettings[$setting->SettingKey]))
		{
			throw new Exception("Invalid Module Setting Received !");
		}	
		
		$this->arrModuleSettings[$setting->SettingKey] = $setting;
		
		register_setting($setting->SettingGroup, $setting->SettingKey, array($this, 'validateModuleSetting'));
		
		$arrSettingSections = $setting->getSettingSections();
		
		if(empty($arrSettingSections))
		{
			foreach ($this->getModuleSettingSections() as $settingSection)
			{
				$setting->addSettingSection($settingSection);
			}
		}
		
		foreach ($setting->getSettingSections() as $settingSection)
		{
			add_settings_section($settingSection->SectionTagId, $settingSection->SectionTitle, 
							 array($this, 'renderModuleSettingSection'), $setting->SettingGroup);
			
			
			foreach ($settingSection->getSettingFields() as $settingField)
			{
				
				add_settings_field( $settingField->Name, 
									$settingField->HTMLLabelText, 
									array($this, 'renderModuleSettingField'),
									$setting->SettingGroup,
									$settingSection->SectionTagId,
									array($settingField)
								  ); 
				
			}
		}
		
	}
	
}