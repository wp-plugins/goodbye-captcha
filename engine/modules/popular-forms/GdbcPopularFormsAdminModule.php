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

final class GdbcPopularFormsAdminModule extends GdbcBaseAdminModule
{

	CONST CONTACT_FORM_7       = 'IsCFActivated';
	CONST GRAVITY_FORMS        = 'IsGFActivated';
	CONST NINJA_FORMS          = 'IsNFActivated';
	CONST FORMIDABLE_FORMS     = 'IsFFActivated';
	CONST FAST_SECURE_FORM     = 'IsFSActivated';
	CONST JETPACK_CONTACT_FORM = 'IsJCFctivated'; // - misspelled !!!!
	CONST PLANSO_FORMS         = 'IsPFActivated';

	private $arrDefaultSettingOptions = array(

			self::CONTACT_FORM_7    => array(
				'Id'         => 1,
				'Value'      => NULL,
				'LabelText' => 'Contact Form 7',
				'InputType'  => MchWpUtilHtml::FORM_ELEMENT_INPUT_CHECKBOX
			),
		
			self::GRAVITY_FORMS     => array(
				'Id'         => 2,
				'Value'      => NULL,
				'LabelText' => 'Gravity Forms',
				'InputType'  => MchWpUtilHtml::FORM_ELEMENT_INPUT_CHECKBOX
			),
		
			self::NINJA_FORMS       => array(
				'Id'         => 3,
				'Value'      => NULL,
				'LabelText' => 'Ninja Forms',
				'InputType'  => MchWpUtilHtml::FORM_ELEMENT_INPUT_CHECKBOX
			),
		
			self::FORMIDABLE_FORMS  => array(
				'Id'         => 4,
				'Value'      => NULL,
				'LabelText' => 'Formidable Forms',
				'InputType'  => MchWpUtilHtml::FORM_ELEMENT_INPUT_CHECKBOX
			),

			self::FAST_SECURE_FORM  => array(
				'Id'         => 5,
				'Value'      => NULL,
				'LabelText' => 'Fast Secure Forms',
				'InputType'  => MchWpUtilHtml::FORM_ELEMENT_INPUT_CHECKBOX
			),

			self::JETPACK_CONTACT_FORM  => array(
				'Id'         => 6,
				'Value'      => NULL,
				'LabelText' => 'JetPack Contact Form',
				'InputType'  => MchWpUtilHtml::FORM_ELEMENT_INPUT_CHECKBOX
			),

			self::PLANSO_FORMS  => array(
				'Id'         => 7,
				'Value'      => NULL,
				'LabelText' => 'PlanSo Forms',
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
		return  __('Popular Forms', $this->PLUGIN_SLUG);
	}


	protected function getModuleSettingSections()
	{
		$settingSection = new MchWpSettingSection($this->moduleSetting->SettingKey . '-section', "Popular Forms");
		
		foreach ($this->arrDefaultSettingOptions as $fieldName => $fieldInfo)
		{
			if(empty($fieldInfo['LabelText']) || empty($fieldInfo['InputType']))
				continue;

			$shouldRenderSetting = false;
			switch($fieldName)
			{
				case self::CONTACT_FORM_7 :
					$shouldRenderSetting = GdbcModulesController::getInstance($this->ArrPluginInfo)->isModuleRegistered(GdbcModulesController::MODULE_CONTACT_FORM_7);
					break;
				case self::FAST_SECURE_FORM :
					$shouldRenderSetting = GdbcModulesController::getInstance($this->ArrPluginInfo)->isModuleRegistered(GdbcModulesController::MODULE_FAST_SECURE_FORM);
					break;

				case self::GRAVITY_FORMS :
					$shouldRenderSetting = GdbcModulesController::getInstance($this->ArrPluginInfo)->isModuleRegistered(GdbcModulesController::MODULE_GRAVITY_FORMS);
					break;

				case self::NINJA_FORMS :
					$shouldRenderSetting = GdbcModulesController::getInstance($this->ArrPluginInfo)->isModuleRegistered(GdbcModulesController::MODULE_NINJA_FORMS);
					break;

				case self::FORMIDABLE_FORMS :
					$shouldRenderSetting = GdbcModulesController::getInstance($this->ArrPluginInfo)->isModuleRegistered(GdbcModulesController::MODULE_FORMIDABLE_FORMS);
					break;

				case self::JETPACK_CONTACT_FORM :
					$shouldRenderSetting = GdbcModulesController::getInstance($this->ArrPluginInfo)->isModuleRegistered(GdbcModulesController::MODULE_JETPACK_CONTACT_FORM);
					break;

				case self::PLANSO_FORMS :
					$shouldRenderSetting = GdbcModulesController::getInstance($this->ArrPluginInfo)->isModuleRegistered(GdbcModulesController::MODULE_PLAN_SO_FORMS);
					break;

			}

			if(!$shouldRenderSetting)
				continue;

			$settingField = new MchWpSettingField($fieldName, $fieldInfo['Value']);
			
			$settingField->HTMLLabelText = $fieldInfo['LabelText'];
			$settingField->HTMLInputType = $fieldInfo['InputType'];
			
			$settingSection->addSettingField($settingField);
		}
		
		return array($settingSection);
	}
	
	public function renderModuleSettingSection(array $arrSectionInfo)
	{
		echo '<h4 id = "' . $arrSectionInfo['id'] . '">' . __('Enable GoodBye Captcha with the following popular forms', $this->PLUGIN_SLUG) . '</h4>';
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