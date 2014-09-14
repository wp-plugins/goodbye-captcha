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

final class GdbcTokenController
{
		
	private $TokenSecretKey    = null;
	private $HiddenInputName   = null;
	private $ModulesController = null;
	
	CONST TOKEN_SEPARATOR = '|';
	CONST TOKEN_LIVETIME  = 900;
	
	
	private function __construct()
	{
		$this->ModulesController = GdbcModulesController::getInstance(GoodByeCaptcha::getPluginInfo());
		if($this->ModulesController->isModuleRegistered(GdbcModulesController::MODULE_DEFAULT))
		{
			$this->TokenSecretKey  = $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_DEFAULT, GdbcDefaultAdminModule::OPTION_TOKEN_SECRET_KEY);
			$this->HiddenInputName = $this->ModulesController->getModuleSettingOption(GdbcModulesController::MODULE_DEFAULT, GdbcDefaultAdminModule::OPTION_HIDDEN_INPUT_NAME);
		}
		
	}
	
	public function isReceivedTokenValid()
	{

		$receivedToken = isset($_POST[$this->HiddenInputName]) ? $_POST[$this->HiddenInputName] : (null !== $this->getHiddenFieldCookie() ? $this->getHiddenFieldCookie() : null);
		if(null === $receivedToken)
			return false;
		
		$this->deleteHiddenFieldCookie();
		$arrDecryptedToken = json_decode(MchCrypt::decryptToken($this->TokenSecretKey, $receivedToken), true);
		
		if( !isset($arrDecryptedToken[0]) || false === ($tokenIndex = strpos($arrDecryptedToken[0], self::TOKEN_SEPARATOR)) )
		{
			return false;
		}

		$browserInfoInput = substr($arrDecryptedToken[0], 0, $tokenIndex);
		
		$receivedBrowserInfoInput = isset($_POST[$browserInfoInput]) ? $_POST[$browserInfoInput] : (isset($_COOKIE[GoodByeCaptcha::PLUGIN_SHORT_CODE . "-$browserInfoInput"]) ? $_COOKIE[GoodByeCaptcha::PLUGIN_SHORT_CODE . "-$browserInfoInput"] : null);

		if( null === $receivedBrowserInfoInput )
		{
			return false;
		}
//		if(isset($_COOKIE[GoodByeCaptcha::PLUGIN_SHORT_CODE . "-$browserInfoInput"]))
//		{
//			GdbcPluginUtils::deleteCookie(GoodByeCaptcha::PLUGIN_SHORT_CODE . "-$browserInfoInput");
//		}
		
		$receivedBrowserInfoInput = MchWpUtil::replaceNonAlphaNumericCharacters($receivedBrowserInfoInput, '');
		
		
		if($arrDecryptedToken[0] !== $browserInfoInput . self::TOKEN_SEPARATOR . $receivedBrowserInfoInput)
		{
			return false;
		}
		
		array_shift($arrDecryptedToken);

		$arrTokenData = $this->getTokenData();
				
		$timeSinceGenerated = ((int)array_pop($arrTokenData)) - ((int)array_pop($arrDecryptedToken));

		if($timeSinceGenerated > self::TOKEN_LIVETIME)
		{
			return false;
		}
		
		if(count(array_diff($arrDecryptedToken, $arrTokenData)) !== 0)
		{
			return false;
		}

		return true;
		
	}
	

	public function retrieveEncryptedToken()
	{
		if( ! $this->isAjaxRequestForTokenValid() )
			return json_encode (array());
		
		if(!isset($_POST['browserInfo']) || null === ($arrBrowserInfo = json_decode(stripcslashes($_POST['browserInfo']), true)))
			return json_encode (array());
		
		if( ($arrBrowserInfoLength = count($arrBrowserInfo)) < 3)
			return json_encode (array());
		
		$arrKeysToSave = array_flip((array)array_rand($arrBrowserInfo, MchCrypt::getRandomIntegerInRange(3, $arrBrowserInfoLength - 1)));

		foreach ($arrKeysToSave as $key => &$val)
		{
			$val = var_export($arrBrowserInfo[$key], true);
		}

		$arrTokenData = $this->getTokenData();
		$browserField = MchWpUtil::replaceNonAlphaCharacters(MchCrypt::getRandomString(25), '-');
		array_unshift($arrTokenData, $browserField . self::TOKEN_SEPARATOR . MchWpUtil::replaceNonAlphaNumericCharacters(implode('', array_values($arrKeysToSave)), ''));
		
		$arrResponse = array(
			'token'       => MchCrypt::encryptToken($this->TokenSecretKey, json_encode($arrTokenData)),
			$browserField => implode(self::TOKEN_SEPARATOR, array_keys($arrKeysToSave))
		);
		
		echo json_encode($arrResponse);

		exit;
	}
	
	
	
	private function getTokenData()
	{
	
		$arrData   = array();
		$arrData[] = get_current_blog_id();
		$arrData[] = MchWpBase::WP_VERSION_ID + PHP_VERSION_ID;
		$arrData[] = MchWpUtil::replaceNonAlphaNumericCharacters(get_bloginfo('name'), '');
		$arrData[] = MchWpUtil::replaceNonAlphaNumericCharacters(get_bloginfo('url'), '');
		$arrData[] = MchWpUtil::replaceNonAlphaNumericCharacters(get_bloginfo('charset'), '');
		$arrData[] = MchWpUtil::replaceNonAlphaNumericCharacters(get_bloginfo('language'), '');
		$arrData[] = MchWpUtil::replaceNonAlphaNumericCharacters(get_bloginfo('version'), '');
		$arrData[] = MchWpUtil::replaceNonAlphaNumericCharacters(php_uname());
		$arrData[] = isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time();
		
		foreach ($arrData as $key => $val)
		{
			if(empty($val))
				unset($arrData[$key]);
		}
		
		return $arrData;
	}
	
	
	private function isAjaxRequestForTokenValid()
	{
		
		if( !isset($_POST[$this->HiddenInputName]) || !$this->isAjaxNonceValid($this->HiddenInputName) )
			return false;
		
		
		if( !isset($_SERVER['HTTP_X_REQUESTED_WITH'] ) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest' )
			return false; 
	

		if( !isset($_SERVER['HTTP_ACCEPT']) || false === strpos($_SERVER['HTTP_ACCEPT'], 'json') )
			return false;

		return true;
	}
	
	
	public function setHiddenFieldNonceCookie()
	{
		return GdbcPluginUtils::setCookie(GoodByeCaptcha::PLUGIN_SLUG . '-' . $this->HiddenInputName, $this->getAjaxNonce(), 86400);
	}
	public function getHiddenFieldNonceCookie()
	{
		return GdbcPluginUtils::getCookie(GoodByeCaptcha::PLUGIN_SLUG . '-' . $this->HiddenInputName, $this->getAjaxNonce(), 86400);
	}
	
//	public function deleteHiddenFieldNonceCookie()
//	{
//		return GdbcPluginUtils::deleteCookie(GoodByeCaptcha::PLUGIN_SHORT_CODE . $this->HiddenInputName);
//	}
	
	private function getHiddenFieldCookie()
	{
		return GdbcPluginUtils::getCookie($this->HiddenInputName);
	}
	private function deleteHiddenFieldCookie()
	{
		return GdbcPluginUtils::getCookie($this->HiddenInputName);
	}
	


	/**
	 * Generate the complex nonce string
	 * @param  string $strNonceAction Action for which the nonce is needed
	 * @param  string $strItem (optional) Item for which the action will be performed
	 * @return string The complex nonce string
	 */
	private function getComplexNonceAction($isForAjax = true)
	{
		static $nonceAction = null;
		
		if(null !== $nonceAction)
			return $nonceAction . ($isForAjax ? 'ajax' : 'field');

		$arrParts   = array();
		
		$arrParts[] = GoodByeCaptcha::PLUGIN_SLUG;
				
		$arrParts[] = GoodByeCaptcha::PLUGIN_SHORT_CODE;
		$arrParts[] = GoodByeCaptcha::PLUGIN_VERSION;

		$arrParts[] = MchWpBase::WP_VERSION_ID + PHP_VERSION_ID;

		$arrParts[] = get_bloginfo('name');
		$arrParts[] = get_bloginfo('url');
		$arrParts[] = get_current_blog_id();
		
		$nonceAction = implode('', $arrParts);
		return  $nonceAction . ($isForAjax ? 'ajax' : 'field');
	}	

	public function isAjaxNonceValid($queryArgument)
	{
		return (bool)(check_ajax_referer($this->getComplexNonceAction(true), $queryArgument, false ));
	}	

	
	public function getTokenInputField()
	{
		return '<input type="hidden" autocomplete="off" autocorrect="off" name="' . esc_attr( $this->HiddenInputName ) . '" value="' . wp_create_nonce( $this->getComplexNonceAction(true) ) . '" />';
	}
	
	
	private function getAjaxNonce()
	{
		return wp_create_nonce($this->getComplexNonceAction(true));
	}
	
	private function getNonce()
	{
		return wp_create_nonce($this->getComplexNonceAction(false));
	}
	
	
	private function isNonceValid($strNonce)
	{
		return (bool)wp_verify_nonce($strNonce, $this->getComplexNonceAction(false));
	}

	/**
	 * 
	 * @staticvar null $instance
	 * @return \GdbcTokenController
	 */
	public static function getInstance()
	{
		static $instance = null;
		return null === $instance ? $instance = new self() : $instance;
	}

}