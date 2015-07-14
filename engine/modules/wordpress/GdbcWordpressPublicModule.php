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

final class GdbcWordpressPublicModule extends GdbcBasePublicModule
{

	protected function __construct(array $arrPluginInfo)
	{
		parent::__construct($arrPluginInfo);

	}

	public function activateCommentsActions()
	{
		add_action('comment_form_after_fields',    array($this, 'renderHiddenFieldIntoCommentsForm'), 1);
		add_action('comment_form_logged_in_after', array($this, 'renderHiddenFieldIntoCommentsForm'), 1);
		add_action('comment_form',                 array($this, 'renderHiddenFieldIntoForm'));

		add_filter('preprocess_comment', array($this, 'validateCommentsFormEncryptedToken'));
	}

	public function renderHiddenFieldIntoCommentsForm()
	{
		$this->renderHiddenFieldIntoForm();
		remove_action('comment_form', array($this,'renderHiddenFieldIntoForm') );
	}

	public function activateLoginActions()
	{
		add_action('login_form', array($this, 'renderHiddenFieldIntoLoginForm'));
		add_filter('login_form_bottom', array($this, 'getHiddenFieldForLoginForm'));

		add_filter('authenticate',  array($this, 'validateAuthenticationFormEncryptedToken'), 73, 3);
		add_filter('wp_authenticate_user',  array($this, 'validateAuthenticationFormEncryptedToken'), 20, 2);
	}

	public function renderHiddenFieldIntoLoginForm()
	{
		$this->renderHiddenFieldIntoForm();
		remove_filter('login_form_bottom', array($this,'renderHiddenFieldIntoForm'));
	}

	public function getHiddenFieldForLoginForm()
	{
		remove_action('login_form', array($this, 'renderHiddenFieldIntoLoginForm'));
		return GdbcTokenController::getInstance()->getTokenInputField();
	}

	public function activateRegisterActions()
	{
		add_action('register_form',             array($this, 'renderHiddenFieldIntoForm'));
		add_action('signup_extra_fields',       array($this, 'renderHiddenFieldIntoForm'));

		add_filter('registration_errors',       array($this, 'validateRegisterFormEncryptedToken'), 10, 3 );
		add_filter('wpmu_validate_user_signup', array($this, 'validateMURegisterFormEncryptedToken'));

	}

	public function validateMURegisterFormEncryptedToken($results)
	{
		if(GdbcRequest::isValid(array('module' => GdbcModulesController::MODULE_WORDPRESS, 'section' => GdbcWordpressAdminModule::REGISTRATION_FORM)))
			return $results;

		$results['errors']->add('gdbc-invalid-token', __('ERROR', $this->PLUGIN_SLUG));

		return $results;
	}

	public function validateRegisterFormEncryptedToken($errors, $userLogin, $userEmail)
	{
		if(GdbcRequest::isValid(array('module' => GdbcModulesController::MODULE_WORDPRESS, 'section' => GdbcWordpressAdminModule::REGISTRATION_FORM)))
			return $errors;

		!is_wp_error($errors) ? $errors = new WP_Error() : null;

		$errors->add('gdbc-invalid-token', __('ERROR', $this->PLUGIN_SLUG));

		return $errors;
	}



	public function activateLostPasswordActions()
	{
		add_action('lostpassword_form', array($this, 'renderHiddenFieldIntoForm'), 10);
		add_action('lostpassword_post', array($this, 'validateLostPasswordFormEncryptedToken'), 10);
	}

	public function activateFormDefaultFieldsActions()
    {
        add_filter('comment_form_default_fields', array($this, 'hideFormWebSiteField'));
    }

	public function activateCommentsFormNotesActions()
	{
		add_filter('comment_form_defaults', array($this, 'hideFormNotesFields'));
	}

	public function renderHiddenFieldIntoForm()
	{
		echo GdbcTokenController::getInstance()->getTokenInputField();
	}

	public function validateLostPasswordFormEncryptedToken()
	{
		if(GdbcRequest::isValid(array('module' => GdbcModulesController::MODULE_WORDPRESS, 'section' => GdbcWordpressAdminModule::LOST_PASSWORD_FORM)))
		{
			return;
		}

		wp_redirect(wp_login_url());

		exit;

	}


	public function validateAuthenticationFormEncryptedToken($user, $username = null, $password = null)
	{

		if (empty($username) || is_wp_error($user))
			return $user;

		return GdbcRequest::isValid(array('module' => GdbcModulesController::MODULE_WORDPRESS, 'section' => GdbcWordpressAdminModule::LOGIN_FORM)) ? $user : new WP_Error($this->PLUGIN_SLUG,  __('<strong>ERROR</strong>: Invalid username or incorrect password!', $this->PLUGIN_SLUG));

	}

	public function validateCommentsFormEncryptedToken($arrComment)
	{

		if( is_admin() || (!empty($arrComment['comment_type']) && $arrComment['comment_type'] !== 'comment') )
			return $arrComment;

		$arrComment['comment_post_ID'] = (!empty($arrComment['comment_post_ID']) && is_numeric($arrComment['comment_post_ID'])) ? (int)$arrComment['comment_post_ID'] : 0;

		if(0 === $arrComment['comment_post_ID'])
		{
			wp_safe_redirect(home_url('/'));exit;
		}

		if(!array_key_exists(get_post_type($arrComment['comment_post_ID']), get_post_types( array('public' => true, '_builtin' => true)) ))
		{
			return $arrComment; // not a regular wordpress comment
		}

		if( GdbcRequest::isValid(array('module' => GdbcModulesController::MODULE_WORDPRESS, 'section' => GdbcWordpressAdminModule::COMMENTS_FORM)) )
			return $arrComment;

		$postPermaLink = get_permalink($arrComment['comment_post_ID']);

		empty($postPermaLink) ? wp_safe_redirect(home_url('/')) : wp_safe_redirect($postPermaLink);

		exit;

//		if(null !== GoodByeCaptcha::getModulesControllerInstance()->getModuleSettingOption(GdbcModulesController::MODULE_WORDPRESS, GdbcWordpressAdminModule::STORE_SPAM_ATTEMPTS))
//		{
//			$arrComment['comment_approved'] = 'spam';
//			wp_insert_comment($arrComment);
//
//			return $arrComment;
//		}

	}

	public function hideFormWebSiteField($arrDefaultFields)
    {
		unset($arrDefaultFields['url']);

	    return $arrDefaultFields;
    }

	public function hideFormNotesFields($arrDefaultFields)
	{
		$arrDefaultFields['comment_notes_before'] = '';
		$arrDefaultFields['comment_notes_after'] = '';

		return $arrDefaultFields;
	}

	public static function getInstance(array $arrPluginInfo)
	{
		static $arrInstances = array();
		$instanceKey         = implode('', $arrPluginInfo);
		return isset($arrInstances[$instanceKey]) ? $arrInstances[$instanceKey] : $arrInstances[$instanceKey] = new self($arrPluginInfo);
	}

}