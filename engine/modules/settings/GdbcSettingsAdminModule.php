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

	CONST OPTION_MIN_SUBMISSION_TIME       = 'MinSubmissionTime';
	CONST OPTION_MAX_SUBMISSION_TIME       = 'MaxSubmissionTime';
	CONST OPTION_MAX_ALLOWED_ATTEMPTS      = 'MaxAllowedAttempts';
	CONST OPTION_MAX_LOGS_DAYS             = 'MaxLogsDays';
	CONST OPTION_AUTO_BLOCK_IP             = 'AutoBlockIp';

	CONST OPTION_TRUSTED_IPS               = 'TrustedIps';

	CONST OPTION_LICENSE_KEY               = 'LicenseKey';
	CONST OPTION_LICENSE_ACTIVATED         = 'IsLicenseActivated';

	private $arrDefaultSettingOptions = array(

		self::OPTION_TRUSTED_IPS  => array(
			'Value'       => array(),
			'LabelText'   => 'Most Trusted IP Address',
			'Description' => 'All requests from this IP will be trusted!',
			'InputType'   => MchWpUtilHtml::FORM_ELEMENT_INPUT_TEXT
		),

		self::OPTION_MIN_SUBMISSION_TIME  => array(
			'Value'       => 5,
			'LabelText'   => 'Minimum Form Submission Time',
			'Description' => 'Number of seconds before the submission is considered valid',
			'InputType'   => MchWpUtilHtml::FORM_ELEMENT_INPUT_TEXT
		),

		self::OPTION_MAX_SUBMISSION_TIME  => array(
			'Value'       => 600,
			'LabelText'   => 'Maximum Form Submission Time',
			'Description' => 'Number of seconds after the submission is not considered valid',
			'InputType'   => MchWpUtilHtml::FORM_ELEMENT_INPUT_TEXT
		),

		self::OPTION_MAX_ALLOWED_ATTEMPTS  => array(
			'Value'       => 10,
			'LabelText'   => 'Maximum Attempts per Minute',
			'Description' => 'Maximum number of allowed attempts per minute',
			'InputType'   => MchWpUtilHtml::FORM_ELEMENT_INPUT_TEXT
		),


		self::OPTION_AUTO_BLOCK_IP => array(
			'Value'       => NULL,
			'LabelText'   => 'Automatically Block IP Address',
			'Description' => 'Automatically block IP Address if the Maximum Attempts per Minute is reached',
			'InputType'   => MchWpUtilHtml::FORM_ELEMENT_INPUT_CHECKBOX
		),

		self::OPTION_MAX_LOGS_DAYS  => array(
			'Value'       => 60,
			'LabelText'   => 'Automatically purge logs older than',
			'Description' => 'Logs older than selected number of days will be automatically purged',
			'InputType'   => MchWpUtilHtml::FORM_ELEMENT_SELECT
		),


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

//		self::OPTION_LICENSE_KEY  => array(
//			'Value'      => NULL,
//			'LabelText' => 'License Key',
//			'InputType'  => MchWpUtilHtml::FORM_ELEMENT_INPUT_TEXT
//		),
//
//		self::OPTION_LICENSE_ACTIVATED  => array(
//			'Value'      => NULL,
//			'LabelText' => 'License not activated',
//			'InputType'  => MchWpUtilHtml::FORM_ELEMENT_INPUT_CHECKBOX
//		),

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
			$settingField->Description   = !empty($fieldInfo['Description']) ? $fieldInfo['Description'] : null;

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
		$settingSectionHtml  = '<h4 style = "position:relative;">' . __("General Settings", $this->PLUGIN_SLUG) . "</h4>";
		echo $settingSectionHtml;
	}

//	public function getSettingOption($settingOptionName)
//	{
//		$optionValue = parent::getSettingOption($settingOptionName);
//
//		if($settingOptionName === self::OPTION_TRUSTED_IPS)
//		{
//			return isset($optionValue[0]) && is_array($optionValue) ? $optionValue[0] : null;
//		}
//
//		return $optionValue;
//	}

	public function validateModuleSetting($arrSettingOptions)
	{

		$hasErrors = false;

		if(!$hasErrors)
		{
			if (empty($arrSettingOptions[self::OPTION_MIN_SUBMISSION_TIME])
				|| false === ($arrSettingOptions[self::OPTION_MIN_SUBMISSION_TIME] = filter_var(sanitize_text_field($arrSettingOptions[self::OPTION_MIN_SUBMISSION_TIME]), FILTER_VALIDATE_INT))
				|| $arrSettingOptions[self::OPTION_MIN_SUBMISSION_TIME] < 1
			){
				$this->moduleSetting->addErrorMessage(__('Minimum Submission Time should be a numeric value greater than 1 !', $this->PLUGIN_SLUG));
				unset($arrSettingOptions[self::OPTION_MIN_SUBMISSION_TIME]);
				$hasErrors = true;
			}
		}

		if(!$hasErrors)
		{
			if (empty($arrSettingOptions[self::OPTION_MAX_SUBMISSION_TIME])
				|| false === ($arrSettingOptions[self::OPTION_MAX_SUBMISSION_TIME] = filter_var(sanitize_text_field($arrSettingOptions[self::OPTION_MAX_SUBMISSION_TIME]), FILTER_VALIDATE_INT))
				|| $arrSettingOptions[self::OPTION_MAX_SUBMISSION_TIME] <= $arrSettingOptions[self::OPTION_MIN_SUBMISSION_TIME]
			){
				$this->moduleSetting->addErrorMessage(__('Minimum Submission Time should be a numeric value greater than Minimum Submission Time !', $this->PLUGIN_SLUG));
				unset($arrSettingOptions[self::OPTION_MAX_SUBMISSION_TIME]);
				$hasErrors = true;
			}
		}
		if(!$hasErrors)
		{
			if (empty($arrSettingOptions[self::OPTION_MAX_ALLOWED_ATTEMPTS])
				|| false === ($arrSettingOptions[self::OPTION_MAX_ALLOWED_ATTEMPTS] = filter_var(sanitize_text_field($arrSettingOptions[self::OPTION_MAX_ALLOWED_ATTEMPTS]), FILTER_VALIDATE_INT))
				|| $arrSettingOptions[self::OPTION_MAX_ALLOWED_ATTEMPTS] < 1
			){
				$this->moduleSetting->addErrorMessage(__('Minimum Form Submissions per Minute should be a numeric value greater 0 !', $this->PLUGIN_SLUG));
				unset($arrSettingOptions[self::OPTION_MAX_ALLOWED_ATTEMPTS]);
				$hasErrors = true;
			}
		}

		if(!$hasErrors && !empty($arrSettingOptions[self::OPTION_TRUSTED_IPS]))
		{
			$arrSettingOptions[self::OPTION_TRUSTED_IPS] = sanitize_text_field($arrSettingOptions[self::OPTION_TRUSTED_IPS]);

			if(!MchHttpUtil::isValidIpAddress($arrSettingOptions[self::OPTION_TRUSTED_IPS]))
			{
				$this->moduleSetting->addErrorMessage(__('Please enter a valid IP address!', $this->PLUGIN_SLUG));
				unset($arrSettingOptions[self::OPTION_TRUSTED_IPS]);
				$hasErrors = true;
			}

			!$hasErrors ? $arrSettingOptions[self::OPTION_TRUSTED_IPS] = array($arrSettingOptions[self::OPTION_TRUSTED_IPS]) : null;

		}

//		print_r($arrSettingOptions);exit;


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

		if($settingField->Name === self::OPTION_TRUSTED_IPS)
		{
			if(!empty($arrAttributes['value']) && is_array($arrAttributes['value']))
			{
				$arrAttributes['value'] = $arrAttributes['value'][0];
			}
			else
			{
				$arrAttributes['value'] = '';
				$settingField->Description =  __('<strong style = "color:#d54e21">Whitelist your current IP Address: <b style = "color:#1618d5">' . MchHttpRequest::getClientIp() . '</b></strong>', $this->PLUGIN_SLUG);
			}
		}


		if(!isset($arrAttributes['value']) && isset($this->arrDefaultSettingOptions[$settingField->Name]))
		{
			if(!is_array($this->arrDefaultSettingOptions[$settingField->Name]['Value']))
				$arrAttributes['value'] = $this->arrDefaultSettingOptions[$settingField->Name]['Value'];
		}


//		if($settingField->Name === self::OPTION_LICENSE_ACTIVATED)
//		{
//			unset($arrAttributes['type']);
//		}
//
//		if(!isset($arrAttributes['type']))
//			return;

		switch ($settingField->HTMLInputType)
		{
			case MchWpUtilHtml::FORM_ELEMENT_SELECT :

				if($settingField->Name === self::OPTION_MAX_LOGS_DAYS)
				{
					$arrAttributes['options'] = array();
					for($i = 0; $i <= 6; ++$i)
					$arrAttributes['options'][(60 * $i) . ' days'] = 60 * $i;

				}

				echo MchWpUtilHtml::createSelectElement($arrAttributes);
				break;


			case MchWpUtilHtml::FORM_ELEMENT_INPUT_CHECKBOX :

				!empty($arrAttributes['value']) ? $arrAttributes['checked'] = 'checked' : null;

				$arrAttributes['value'] = true;

				echo MchWpUtilHtml::createInputElement($arrAttributes);
				
				break;

			case MchWpUtilHtml::FORM_ELEMENT_INPUT_TEXT :
				echo MchWpUtilHtml::createInputElement($arrAttributes);
				break;
		}

		if(!empty($settingField->Description))
		{
			echo '<p class = "description">' . $settingField->Description . '</p>';

			if($settingField->Name === self::OPTION_MAX_LOGS_DAYS)
			{
				echo '<p class = "description hidden" style = "color:#d54e21">' .  __('By selecting ZERO you TURN OFF logging and you wont be protected against brute-force attacks !', $this->PLUGIN_SLUG)  . '</p>';
			}
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