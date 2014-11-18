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
		add_action('comment_form_after_fields',    array($this, 'renderHiddenFieldIntoForm'), 1);
		add_action('comment_form_logged_in_after', array($this,'renderHiddenFieldIntoForm'), 1);
		add_filter('preprocess_comment', array($this, 'validateCommentsFormEncryptedToken'));
	}
	
	public function activateLoginActions()
	{
		add_action('login_form', array($this, 'renderHiddenFieldIntoForm'));
		add_filter('authenticate',  array($this, 'validateAuthenticationFormEncryptedToken'), 23, 3);				
	}
	
	public function activateRegisterActions()
	{
		add_action('register_form',             array($this, 'renderHiddenFieldIntoForm'), 1);
		add_action('signup_extra_fields',       array($this, 'renderHiddenFieldIntoForm'), 1);
		
		add_action('registration_errors',       array($this, 'validateRegisterFormEncryptedToken'), 10, 3 );
		add_filter('wpmu_validate_user_signup', array($this, 'validateMURegisterFormEncryptedToken'));
		
	}

	public function activateLostPasswordActions()
	{
		add_action('lostpassword_form', array($this, 'renderHiddenFieldIntoForm'), 1);
		add_action('lostpassword_post', array($this, 'validateLostPasswordFormEncryptedToken'), 10);				
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

		wp_redirect(wp_get_referer());
		
		exit;
		
	}
	
	public function validateMURegisterFormEncryptedToken($results)
	{
		if(GdbcRequest::isValid(array('module' => GdbcModulesController::MODULE_WORDPRESS, 'section' => GdbcWordpressAdminModule::REGISTRATION_FORM)))
			return $results;
		
		$results['errors']->add('gdbc-invalid-token', __( '<strong>ERROR</strong>: Invalid token received !', $this->PLUGIN_SLUG ));
		
		return $results;
	}
	
	public function validateRegisterFormEncryptedToken($errors, $userLogin, $userEmail)
	{
		if(GdbcRequest::isValid(array('module' => GdbcModulesController::MODULE_WORDPRESS, 'section' => GdbcWordpressAdminModule::REGISTRATION_FORM)))
			return $errors;
		
		$errors->add('gdbc-invalid-token', __( '<strong>ERROR</strong>: Invalid token received', $this->PLUGIN_SLUG ));
		
		return $errors;
	}
	
	
	
	public function validateAuthenticationFormEncryptedToken($user, $username, $password)
	{
		if(empty($username) || empty($password))
			return $user;
		
		return GdbcRequest::isValid(array('module' => GdbcModulesController::MODULE_WORDPRESS, 'section' => GdbcWordpressAdminModule::LOGIN_FORM)) ? $user : null;
		
	}
	
	public function validateCommentsFormEncryptedToken($arrComment)
	{	
		if(is_admin())
			return $arrComment;

		if(!empty($arrComment['comment_type']) && $arrComment['comment_type'] !== 'comment')
			return $arrComment;
		
		if( GdbcRequest::isValid(array('module' => GdbcModulesController::MODULE_WORDPRESS, 'section' => GdbcWordpressAdminModule::COMMENTS_FORM)) )
			return $arrComment;
		
		$arrComment['comment_approved'] = 'spam';
		wp_insert_comment($arrComment);
			
		return $arrComment;
	}
	
	
	public static function getInstance(array $arrPluginInfo)
	{
		static $arrInstances = array();
		$instanceKey         = implode('', $arrPluginInfo);
		return isset($arrInstances[$instanceKey]) ? $arrInstances[$instanceKey] : $arrInstances[$instanceKey] = new self($arrPluginInfo);
	}
	
}