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

final class GdbcWordpressAdminModule extends GdbcBaseAdminModule
{

	CONST LOGIN_FORM                       = 'IsLoginFormActivated';
	CONST COMMENTS_FORM                    = 'IsCommentsFormActivated';
	CONST LOST_PASSWORD_FORM               = 'IsLostPasswordFormActivated';
	CONST REGISTRATION_FORM                = 'IsUserRegistrationFormActivated';
    CONST COMMENTS_FORM_WEBSITE_FIELD      = 'IsCommentsFormWebsiteFieldHidden';
	CONST COMMENTS_FORM_NOTES_FIELDS       = 'CommentsFormNotesHidden'; // hides allowed tags and text like "Your email address will not be published"
	CONST STORE_SPAM_ATTEMPTS              = 'IsKeepSpamAttemptsActivated';

	private $arrDefaultSettingOptions = array(

			self::COMMENTS_FORM  => array(
				'Id'         => 1,
				'Value'      => NULL,
				'LabelText' => 'Comments',
				'InputType'  => MchWpUtilHtml::FORM_ELEMENT_INPUT_CHECKBOX
			),
		
			self::LOGIN_FORM=> array(
				'Id'         => 2,
				'Value'      => NULL,
				'LabelText' => 'Login',
				'InputType'  => MchWpUtilHtml::FORM_ELEMENT_INPUT_CHECKBOX
			),
		
			self::LOST_PASSWORD_FORM  => array(
				'Id'         => 3,
				'Value'      => NULL,
				'LabelText' => 'Lost Password',
				'InputType'  => MchWpUtilHtml::FORM_ELEMENT_INPUT_CHECKBOX
			),
		
			self::REGISTRATION_FORM=> array(
				'Id'         => 4,
				'Value'      => NULL,
				'LabelText' => 'Registration',
				'InputType'  => MchWpUtilHtml::FORM_ELEMENT_INPUT_CHECKBOX
			),

            self::COMMENTS_FORM_WEBSITE_FIELD=> array(
                'Id'          => 5,
                'Value'       => NULL,
                'LabelText'   => 'Hide Comments Website Field',
	            'Description' => 'Hides Comments Form Website Url',
                'InputType'   => MchWpUtilHtml::FORM_ELEMENT_INPUT_CHECKBOX
            ),


            self::COMMENTS_FORM_NOTES_FIELDS=> array(
	            'Id'         => 6,
	            'Value'      => NULL,
	            'LabelText'   => 'Hide Comments Form Notes Fields',
	            'Description' => 'Hides form allowed tags and text like "Your email address will not be published"',
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
		return  __('Wordpress', $this->PLUGIN_SLUG);
	}


	public function getModuleSettingSections()
	{
		$settingSection = new MchWpSettingSection($this->moduleSetting->SettingKey . '-section', __('WordPress standard forms', $this->PLUGIN_SLUG));
		
		foreach ($this->arrDefaultSettingOptions as $fieldName => $fieldInfo)
		{
			if(empty($fieldInfo['LabelText']) || empty($fieldInfo['InputType']))
				continue;
			
			$settingField = new MchWpSettingField($fieldName, $fieldInfo['Value']);
			
			$settingField->HTMLLabelText = $fieldInfo['LabelText'];
			$settingField->HTMLInputType = $fieldInfo['InputType'];
			$settingField->Description   = !empty($fieldInfo['Description']) ? $fieldInfo['Description'] : null;
			$settingSection->addSettingField($settingField);
		}

		return array($settingSection);
	}
	
	public function renderModuleSettingSection(array $arrSectionInfo)
	{
		echo '<h4 style = "position:relative;">' . __("Enable GoodBye Captcha with the following forms", $this->PLUGIN_SLUG) . '</h4>';
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

			case MchWpUtilHtml::FORM_ELEMENT_INPUT_TEXT :
				echo MchWpUtilHtml::createInputElement($arrAttributes);
				break;
		}

		if(!empty($settingField->Description))
		{
			echo '<p class = "description">' . $settingField->Description . '</p>';
		}

	}
	


	
//	private function activateLicense($licenseKey)
//	{
//		if(GoodByeCaptcha::isFreeVersion())
//			return false;
//
//
//		$api_params = array(
//			'edd_action'=> 'activate_license',
//			'license' 	=> trim($licenseKey),
//			'item_name' => urlencode('GoodBye Captcha Pro'),
//			'url'       => home_url()
//		);
//
//		$response = wp_remote_get(add_query_arg($api_params, GoodByeCaptcha::PLUGIN_SITE_URL ),
//								   array( 'timeout' => 15, 'sslverify' => false ));
//
//
//		if (is_wp_error($response) || (null === ($licenseData = json_decode(wp_remote_retrieve_body($response)))))
//			return false;
//
//		return isset($licenseData->license) && $licenseData->license === 'valid';
//	}



//Fiecare modul are setting - care este mchwpsetting. clasa asa are public function getDefaultOptions()
//deci cand ai nevoie de default options iei instanta de modul(public/admin) ii iei setting si are default options
//	public function getArrDefaultOptions()
//	{
//		return self::$arrDefaultOptions;
//	}
	
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