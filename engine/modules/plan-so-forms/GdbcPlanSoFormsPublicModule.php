<?php

/* 
 * Copyright (C) 2015 Mihai Chelaru
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

final class GdbcPlanSoFormsPublicModule extends GdbcBasePublicModule
{

	protected function __construct(array $arrPluginInfo)
	{
		parent::__construct($arrPluginInfo);
	}

	public function activatePlanSoFormsActions()
	{
		if(!GdbcPluginUtils::isPlanSoFormsActivated())
			return;

		add_filter('psfb_form_after_hidden_fields', array($this, 'renderHiddenFieldIntoPlanSoForms'), 10, 1);
		add_filter('psfb_validate_form_request', array($this, 'validatePlanSoFormsEncryptedToken'), 10, 2);

	}

	public function renderHiddenFieldIntoPlanSoForms(array $arrFormInfo)
	{
		$arrFormInfo['out']  = !isset($arrFormInfo['out']) ? '' : (string)$arrFormInfo['out'];
		$arrFormInfo['out'] .= GdbcTokenController::getInstance()->getTokenInputField();

		return $arrFormInfo;
	}

	public function validatePlanSoFormsEncryptedToken($validationAlreadyPassed, $arrFormFields)
	{
		$planSoFormType = 'regular';
		if(isset($arrFormFields['j']->registration->active) && (string)$arrFormFields['j']->registration->active == 'registration'){
			$planSoFormType = 'registration';
		}
		elseif(isset($arrFormFields['j']->registration->active) && (string)$arrFormFields['j']->registration->active == 'login'){
			$planSoFormType = 'login';
		}
		elseif(isset($arrFormFields['j']->paypal->paypal_payment_activate) && (bool)$arrFormFields['j']->paypal->paypal_payment_activate === true) {
			$planSoFormType = 'paypal';
		}

		return GdbcRequest::isValid(array('module' => GdbcModulesController::MODULE_PLAN_SO_FORMS)) ? true : __('There was an error while processing your request!', $this->PLUGIN_SLUG);
	}

	public static function getInstance(array $arrPluginInfo)
	{
		static $arrInstances = array();
		$instanceKey         = implode('', $arrPluginInfo);
		return isset($arrInstances[$instanceKey]) ? $arrInstances[$instanceKey] : $arrInstances[$instanceKey] = new self($arrPluginInfo);
	}
}