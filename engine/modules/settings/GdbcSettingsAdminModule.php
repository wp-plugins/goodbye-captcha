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

final class GdbcSettingsAdminModule extends GdbcBaseAdminModule
{

	CONST OPTION_PLUGIN_VERSION_ID         = 'PluginVersionId';

	CONST OPTION_TOKEN_SECRET_KEY          = 'TokenSecretKey';
	CONST OPTION_TOKEN_CREATED_TIMESTAMP   = 'TokenCreatedTimestamp';
	CONST OPTION_HIDDEN_INPUT_NAME         = 'HiddenInputName';
	
	CONST OPTION_LICENSE_KEY          = 'LicenseKey';
	CONST OPTION_LICENSE_ACTIVATED    = 'IsLicenseActivated';
	
	private $arrDefaultSettingOptions = array(

		self::OPTION_PLUGIN_VERSION_ID  => array(
			'Value'      => NULL,
			'LabelText'  => null,
			'InputType'  => MchWpUtilHtml::FORM_ELEMENT_INPUT_TEXT
		),

		self::OPTION_TOKEN_CREATED_TIMESTAMP  => array(
			'Value'      => NULL,
			'LabelText'  => null,
			'InputType'  => MchWpUtilHtml::FORM_ELEMENT_INPUT_TEXT
		),

		self::OPTION_LICENSE_KEY  => array(
			'Value'      => NULL,
			'LabelText' => 'License Key',
			'InputType'  => MchWpUtilHtml::FORM_ELEMENT_INPUT_TEXT
		),

		self::OPTION_LICENSE_ACTIVATED  => array(
			'Value'      => NULL,
			'LabelText' => 'License not activated',
			'InputType'  => MchWpUtilHtml::FORM_ELEMENT_INPUT_CHECKBOX
		),

		self::OPTION_TOKEN_SECRET_KEY  => array(
			'Value'      => NULL,
			'LabelText' => NULL,
		),

		self::OPTION_HIDDEN_INPUT_NAME  => array(
			'Value'      => NULL,
			'LabelText'  => NULL,
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
		return __('Settings', $this->PLUGIN_SLUG);
	}


	protected function getModuleSettingSections()
	{
		$settingSection = new MchWpSettingSection($this->moduleSetting->SettingKey . '-section', __('GoodBye Captcha General Settings', $this->PLUGIN_SLUG));
		
		foreach ($this->arrDefaultSettingOptions as $fieldName => $fieldInfo)
		{
			if(empty($fieldInfo['LabelText']) || empty($fieldInfo['InputType']))
				continue;
			
			$settingField = new MchWpSettingField($fieldName, $fieldInfo['Value']);
			
			$settingField->HTMLLabelText = $fieldInfo['LabelText'];
			$settingField->HTMLInputType = $fieldInfo['InputType'];
			
			if($fieldName === self::OPTION_LICENSE_ACTIVATED)
			{
				if(GoodByeCaptcha::isFreeVersion())
					continue;
				 
				$this->getModuleSetting()->getSettingOption($fieldName) ? $settingField->HTMLLabelText =  __('Your license is activated', $this->PLUGIN_SLUG): null;
			}

			if($fieldName === self::OPTION_LICENSE_KEY && GoodByeCaptcha::isFreeVersion())
				continue;
			
			$settingSection->addSettingField($settingField);
		}
		
		
		return array($settingSection);
	}
	
	public function renderModuleSettingSection(array $arrSectionInfo)
	{
		if(!GoodByeCaptcha::isFreeVersion())
		{
			echo '<h4 style = "position:relative;"></h4>';
			return;
		}
	
		$imageSrc = plugins_url( 'admin/images/donate.png', $this->PLUGIN_MAIN_FILE);
		$settingSectionHtml  = '<h4 style = "position:relative;">' . __("General Settings", $this->PLUGIN_SLUG);
		$settingSectionHtml .= '<a target = "_blank" style = "top:-10px;right:0;position:absolute;display:inline-block; width:80px; height:32px;background:url('.$imageSrc.')" href = "https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XVC3TSGEJQP2U"></a>';
		$settingSectionHtml .= '</h4>';

		echo $settingSectionHtml;
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
		
		if($settingField->Name === self::OPTION_LICENSE_ACTIVATED)
		{
			unset($arrAttributes['type']);
		}
		
		if(!isset($arrAttributes['type']))
			return;
		
		switch ($settingField->HTMLInputType)
		{
			case MchWpUtilHtml::FORM_ELEMENT_INPUT_CHECKBOX :

				!empty($arrAttributes['value']) ? $arrAttributes['checked'] = 'checked' : null;

				$arrAttributes['value'] = true;

				echo MchWpUtilHtml::createInputElement($arrAttributes);
				
				break;

			case MchWpUtilHtml::FORM_ELEMENT_INPUT_TEXT :
				echo MchWpUtilHtml::createInputElement($arrAttributes);
				break;
		}
		
		
	}

	public function filterOptionsBeforeSave($arrNewSettings, $arrOldSettings)
	{

		$arrNewSettings = !empty($arrNewSettings) ? (array)$arrNewSettings : array();
		$arrOldSettings = !empty($arrOldSettings) ? (array)$arrOldSettings : array();

		
		if(!empty($arrOldSettings[self::OPTION_TOKEN_SECRET_KEY]))
			$arrNewSettings[self::OPTION_TOKEN_SECRET_KEY] = $arrOldSettings[self::OPTION_TOKEN_SECRET_KEY];
		
		if(!empty($arrOldSettings[self::OPTION_HIDDEN_INPUT_NAME]))
			$arrNewSettings[self::OPTION_HIDDEN_INPUT_NAME] = $arrOldSettings[self::OPTION_HIDDEN_INPUT_NAME];
		
		if(empty($arrOldSettings[self::OPTION_LICENSE_ACTIVATED]) && !empty($arrNewSettings[self::OPTION_LICENSE_KEY]))
			$arrNewSettings[self::OPTION_LICENSE_ACTIVATED] = $this->activateLicense($arrNewSettings[self::OPTION_LICENSE_KEY]);

		$arrSettings = parent::filterOptionsBeforeSave($arrNewSettings, $arrOldSettings);
		
		if(empty($arrSettings[self::OPTION_TOKEN_SECRET_KEY]))
			$arrSettings[self::OPTION_TOKEN_SECRET_KEY] = MchCrypt::getRandomString(MchCrypt::getCipherKeySize());

		if(empty($arrSettings[self::OPTION_TOKEN_CREATED_TIMESTAMP]))
			$arrSettings[self::OPTION_TOKEN_CREATED_TIMESTAMP] = time() + ( get_option( 'gmt_offset' ) * 3600 );

		while(empty($arrSettings[self::OPTION_HIDDEN_INPUT_NAME]))
			$arrSettings[self::OPTION_HIDDEN_INPUT_NAME] = MchWpUtil::replaceNonAlphaCharacters(MchCrypt::getRandomString(25), '-');

		$arrSettings[self::OPTION_PLUGIN_VERSION_ID] = MchWp::getVersionIdFromString($this->PLUGIN_VERSION);

		return $arrSettings;
	}

	
	private function activateLicense($licenseKey)
	{
		if(GoodByeCaptcha::isFreeVersion())
			return false;
		
		
		$api_params = array( 
			'edd_action'=> 'activate_license', 
			'license' 	=> trim($licenseKey), 
			'item_name' => urlencode('GoodByeCaptchaPro'),
			'url'       => home_url()
		);

		$response = wp_remote_get(add_query_arg($api_params, GoodByeCaptcha::PLUGIN_SITE_URL ), 
								   array( 'timeout' => 15, 'sslverify' => false ));

		
		if (is_wp_error($response) || (null === ($licenseData = json_decode(wp_remote_retrieve_body($response)))))
			return false;

		return isset($licenseData->license) && $licenseData->license === 'valid';
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