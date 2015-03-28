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

final class GdbcUltimateMemberAdminModule extends GdbcBaseAdminModule
{

	CONST ULTIMATE_MEMBER_LOGIN_FORM         = 'IsUMLoginActivated';
	CONST ULTIMATE_MEMBER_REGISTER_FORM      = 'IsUMRegisterActivated';
	CONST ULTIMATE_MEMBER_LOST_PASSWORD_FORM = 'IsUMLostPasswordActivated';

	private $arrDefaultSettingOptions = array(

			self::ULTIMATE_MEMBER_LOGIN_FORM    => array(
				'Id'         => 1,
				'Value'      => NULL,
				'LabelText' => 'Login Form',
				'InputType'  => MchWpUtilHtml::FORM_ELEMENT_INPUT_CHECKBOX
			),
		
			self::ULTIMATE_MEMBER_REGISTER_FORM  => array(
				'Id'         => 2,
				'Value'      => NULL,
				'LabelText' => 'Register Form',
				'InputType'  => MchWpUtilHtml::FORM_ELEMENT_INPUT_CHECKBOX
			),

			self::ULTIMATE_MEMBER_LOST_PASSWORD_FORM  => array(
				'Id'         => 3,
				'Value'      => NULL,
				'LabelText' => 'Reset Password Form',
				'InputType'  => MchWpUtilHtml::FORM_ELEMENT_INPUT_CHECKBOX
			),

	);

	/**
	 *
	 * @var \MchWpSetting 
	 */
	private $moduleSetting = null;
	
	protected function __construct(array $arrPluginInfo)
	{
		$this->moduleSetting = new MchWpSetting(__CLASS__, $this->arrDefaultSettingOptions);

		parent::__construct($arrPluginInfo);
		
	}
	
	public function getModuleSetting()
	{
		return $this->moduleSetting;
	}
	
	public function getModuleSettingTabCaption()
	{
		return GdbcPluginUtils::isUltimateMemberActivated() ?  __('Ultimate Member', $this->PLUGIN_SLUG) : null;
	}


	protected function getModuleSettingSections()
	{
		$settingSection = new MchWpSettingSection($this->moduleSetting->SettingKey . '-section', "Ultimate Member");
		
		foreach ($this->arrDefaultSettingOptions as $fieldName => $fieldInfo)
		{
			if(empty($fieldInfo['LabelText']) || empty($fieldInfo['InputType']))
				continue;
			
			$settingField = new MchWpSettingField($fieldName, $fieldInfo['Value']);
			
			$settingField->HTMLLabelText = 'Ultimate Member ' . $fieldInfo['LabelText'];
			$settingField->HTMLInputType = $fieldInfo['InputType'];
			
			$settingSection->addSettingField($settingField);
		}
		
		return array($settingSection);
	}
	
	public function renderModuleSettingSection(array $arrSectionInfo)
	{
		echo '<h4 id = "' . $arrSectionInfo['id'] . '">Enable GoodBye Captcha with the following:</h4>';
	}


	public function validateModuleSetting($arrSettingOptions)
	{
		return $arrSettingOptions;
	}

	public function renderModuleSettingField(array $arrSettingField)
	{
		if(! isset($arrSettingField[0]) )
			return;
		
		/* @var $settingField \MchWpSettingField */
		$settingField = $arrSettingField[0];
		
		
		$arrAttributes = array(
								'type' => $settingField->HTMLInputType,
								'name' => $this->moduleSetting->SettingKey . '[' . $settingField->Name . ']',
								'value' => $this->moduleSetting->getSettingOption($settingField->Name),
							);		
		
		switch ($settingField->HTMLInputType)
		{
			case MchWpUtilHtml::FORM_ELEMENT_INPUT_CHECKBOX :
				if(!empty($arrAttributes['value']))
				{
					$arrAttributes['checked'] = 'checked';
				}
				
				$arrAttributes['value'] = true;	

				echo MchWpUtilHtml::createInputElement($arrAttributes);
				
				break;

			default:
				echo MchWpUtilHtml::createInputElement($arrAttributes);
				break;
		}

	}
	
	public static function getInstance(array $arrPluginInfo)
	{
		static $arrInstances = array();
		$instanceKey         = implode('', $arrPluginInfo);
		return isset($arrInstances[$instanceKey]) ? $arrInstances[$instanceKey] : $arrInstances[$instanceKey] = new self($arrPluginInfo);
	}

	protected function getDefaultSettingOptions()
	{
		return $this->arrDefaultSettingOptions;
	}
}