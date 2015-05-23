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

final class GdbcJetPackContactFormPublicModule extends GdbcBasePublicModule
{

	protected function __construct(array $arrPluginInfo)
	{
		parent::__construct($arrPluginInfo);
	}

	public function activateJetPackContactFormActions()
	{
		if(!GdbcPluginUtils::isJetPackContactFormModuleActivated())
			return;

		add_filter('grunion_contact_form_field_html', array($this, 'insertGoodByeCaptchaToken'), 10, 3);

		defined('JETPACK__VERSION') && version_compare(JETPACK__VERSION, '3.4-beta', '>')
			? add_filter('jetpack_contact_form_is_spam', array($this, 'validateContactFormEncryptedToken'), 1)
			: add_filter('contact_form_is_spam', array($this, 'validateContactFormEncryptedToken'));

	}

	public function validateContactFormEncryptedToken()
	{
		return GdbcRequest::isValid(array('module' => GdbcModulesController::MODULE_JETPACK_CONTACT_FORM)) ? false : new WP_Error($this->PLUGIN_SLUG, __('Your entry appears to be spam!', $this->PLUGIN_SLUG));
	}

	public function insertGoodByeCaptchaToken($fieldBlock, $fieldLabel, $postId)
	{

		static $pageContactFormDetected   = false;
		static $widgetContactFormDetected = false;

		if($pageContactFormDetected && $widgetContactFormDetected)
			return $fieldBlock;

		$arrAttributes = shortcode_parse_atts($fieldBlock);

		$fieldId   = isset($arrAttributes['id'])   ? $arrAttributes['id']   : null;
		$fieldName = isset($arrAttributes['name']) ? $arrAttributes['name'] : null;


		if(null === $fieldName || $fieldId !== $fieldName)
			return $fieldBlock;

		$arrNameParts = explode('-', $fieldName);
		if( !isset($arrNameParts[0]) || empty($arrNameParts))
			return $fieldBlock;

		if(!$widgetContactFormDetected)
		{
			$widgetContactFormDetected = ( false !== strpos( $arrNameParts[0], 'widget' ) );

			if ( $widgetContactFormDetected && isset( $arrNameParts[2] ) && is_numeric( $arrNameParts[2] ) ) {
				return $fieldBlock . GdbcTokenController::getInstance()->getTokenInputField();
			}
		}

		if(!$pageContactFormDetected)
		{
			$postId = (string) $postId;

			if ( ! empty( $postId ) && ( substr( $arrNameParts[0], - strlen( $postId ) ) === $postId ) ) {
				$pageContactFormDetected = true;
				return $fieldBlock . GdbcTokenController::getInstance()->getTokenInputField();
			}

			if ( empty( $postId ) && preg_match( "/g([0-9]+)/", $arrNameParts[0], $matches ) && $arrNameParts[0] === $matches[0] ) {
				$pageContactFormDetected = true;
				return $fieldBlock . GdbcTokenController::getInstance()->getTokenInputField();
			}
		}

		return $fieldBlock;

	}

	public static function getInstance(array $arrPluginInfo)
	{
		static $arrInstances = array();
		$instanceKey         = implode('', $arrPluginInfo);
		return isset($arrInstances[$instanceKey]) ? $arrInstances[$instanceKey] : $arrInstances[$instanceKey] = new self($arrPluginInfo);
	}


}