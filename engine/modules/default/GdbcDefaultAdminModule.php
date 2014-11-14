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

final class GdbcDefaultAdminModule extends MchWpAdminModule
{
	
	CONST OPTION_TOKEN_SECRET_KEY  = 'TokenSecretKey';
	CONST OPTION_HIDDEN_INPUT_NAME = 'HiddenInputName';
	
	CONST OPTION_LICENSE_KEY          = 'LicenseKey';
	CONST OPTION_LICENSE_ACTIVATED    = 'IsLicenseActivated';
	
	CONST OPTION_LOGIN_FORM_ACTIVATED             = 'IsLoginFormActivated';
	CONST OPTION_COMMENTS_FORM_ACTIVATED          = 'IsCommentsFormActivated';
	CONST OPTION_LOST_PASSWORD_FORM_ACTIVATED     = 'IsLostPasswordFormActivated';
	CONST OPTION_REGISTRATION_FORM_ACTIVATED      = 'IsUserRegistrationFormActivated';


	private static $arrDefaultOptions = array(

		
			self::OPTION_COMMENTS_FORM_ACTIVATED  => array(
				'Value'      => NULL,
				'LabelText' => 'Comments',
				'InputType'  => MchWpUtilHtml::FORM_ELEMENT_INPUT_CHECKBOX
			),
		
			self::OPTION_LOGIN_FORM_ACTIVATED=> array(
				'Value'      => NULL,
				'LabelText' => 'Login',
				'InputType'  => MchWpUtilHtml::FORM_ELEMENT_INPUT_CHECKBOX
			),
		
			self::OPTION_LOST_PASSWORD_FORM_ACTIVATED  => array(
				'Value'      => NULL,
				'LabelText' => 'Lost Password',
				'InputType'  => MchWpUtilHtml::FORM_ELEMENT_INPUT_CHECKBOX
			),
		
			self::OPTION_REGISTRATION_FORM_ACTIVATED=> array(
				'Value'      => NULL,
				'LabelText' => 'Registration',
				'InputType'  => MchWpUtilHtml::FORM_ELEMENT_INPUT_CHECKBOX
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
		$this->moduleSetting = new MchWpSetting(__CLASS__, self::$arrDefaultOptions);

		parent::__construct($arrPluginInfo);
		
	}
	
	public function getModuleSetting()
	{
		return $this->moduleSetting;
	}
	
	public function getModuleSettingTabCaption()
	{
		return 'Default';
	}


	protected function getModuleSettingSections()
	{
		$settingSection = new MchWpSettingSection($this->moduleSetting->SettingKey . '-section', __('WordPress standard forms', $this->PLUGIN_SLUG));
		
		foreach (self::$arrDefaultOptions as $fieldName => $fieldInfo)
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
			echo '<h4 style = "position:relative;">' . __("Enable GoodBye Captcha with the following forms", $this->PLUGIN_SLUG) . '</h4>';
			return;
		}
	
		$imageSrc = plugins_url( 'admin/images/donate.png', $this->PLUGIN_MAIN_FILE);
		$settingSectionHtml  = '<h4 style = "position:relative;">' . __("Enable GoodBye Captcha with the following forms", $this->PLUGIN_SLUG);
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
				if(!empty($arrAttributes['value']))
				{
					$arrAttributes['checked'] = 'checked';
				}
				
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
		{
			$arrNewSettings[self::OPTION_LICENSE_ACTIVATED] = $this->activateLicense($arrNewSettings[self::OPTION_LICENSE_KEY]);
		}
		
		$arrSettings = parent::filterOptionsBeforeSave($arrNewSettings, $arrOldSettings);
		
		if( empty($arrSettings[self::OPTION_TOKEN_SECRET_KEY]) )
			$arrSettings[self::OPTION_TOKEN_SECRET_KEY] = MchCrypt::getRandomString(MchCrypt::getCipherKeySize());
		
		if( empty($arrSettings[self::OPTION_HIDDEN_INPUT_NAME]) )
			$arrSettings[self::OPTION_HIDDEN_INPUT_NAME] = MchWpUtil::replaceNonAlphaCharacters(MchCrypt::getRandomString(25), '-');
		
		
		return $arrSettings;
	}

	
	private function activateLicense($licenseKey)
	{
		if(GoodByeCaptcha::isFreeVersion())
			return false;
		
		
		$api_params = array( 
			'edd_action'=> 'activate_license', 
			'license' 	=> trim($licenseKey), 
			'item_name' => urlencode('GoodBye Captcha Pro'), 
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

}