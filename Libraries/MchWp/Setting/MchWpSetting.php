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

class MchWpSetting implements MchWpISetting
{
	private $arrSettingSections = array();
	private $hasErrors = false;

	public $SettingKey   = null;
	public $SettingGroup = null;
	
	private $arrDefaultOptions = array();
	
	public function __construct($moduleName, array $arrDefaultOptions)
	{
		$this->SettingKey    = MchWpUtil::replaceNonAlphaNumericCharacters(strtolower($moduleName), '-');
		$this->SettingGroup  = $this->SettingKey . '-group';
		$this->SettingKey   .= '-settings';
		
		foreach ($arrDefaultOptions as $optionName => $arrOptionInfo)
		{
			$this->arrDefaultOptions[$optionName] = $arrOptionInfo['Value'];
		}

		if(MchWpBase::isUserInDashboad())
		{
			add_action('admin_notices', array($this, 'registerAdminNotices'));
		}
	}

	public function registerAdminNotices()
	{
		settings_errors($this->SettingKey, false, true);
	}

	public function getDefaultOptions()
	{
		return $this->arrDefaultOptions;
	}
	
	public function getAllSavedOptions()
	{
		return (false !== ($arrSavedOptions = get_option($this->SettingKey))) ? $arrSavedOptions : array();		
	}
	
	public function setSettingOption($optionName, $optionValue)
	{
		if(!MchWpBase::isAdminLoggedIn())
			return;

		if(!array_key_exists($optionName, $this->arrDefaultOptions))
			return;

		$arrSavedOptions = $this->getAllSavedOptions();

		$arrSavedOptions[$optionName] = $optionValue;

		update_option($this->SettingKey, $arrSavedOptions);
	}
	
	public function getSettingOption($optionName)
	{
		$arrSavedOptions = $this->getAllSavedOptions();
		return isset($arrSavedOptions[$optionName]) ? $arrSavedOptions[$optionName] : null;
	}

	public function getSettingDefaultOption($optionName)
	{
		return array_key_exists($optionName, $this->arrDefaultOptions) ? $this->arrDefaultOptions[$optionName] : null;
	}

	public function deleteSettingOption($optionName)
	{
		$arrSavedOptions = $this->getAllSavedOptions();
		unset($arrSavedOptions[$optionName]);
		update_option($this->SettingKey, $arrSavedOptions);
	}

	public function deleteAllSettingOptions()
	{
		delete_option($this->SettingKey);
	}

	/**
	 * 
	 * @param MchWpSettingSection $settingSection
	 * @return type boolean
	 */
	public function addSettingSection(MchWpSettingSection $settingSection)
	{
		return !empty($settingSection) ? $this->arrSettingSections[$settingSection->SectionTagId] = $settingSection : false;
	}
	
	public function getSettingSections()
	{
		return $this->arrSettingSections;
	}

	public function hasErrors()
	{
		return $this->hasErrors;
	}
	public function addErrorMessage($message)
	{

		add_settings_error($this->SettingKey, $this->SettingGroup, $message, 'error');
		$this->hasErrors = true;
	}

	public function addSuccessMessage($message)
	{
		add_settings_error($this->SettingKey, $this->SettingGroup, $message, 'updated');
	}

}