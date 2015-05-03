<?php
/** 
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

final class GdbcNotificationsController
{
	public static function sendTestModeEmailNotification($isTokenValid, $arrParameters)
	{
		$gdbcEmailNotification = new GdbcEmailNotification();
		$gdbcEmailNotification->EmailSubject = __('GoodBye Captcha - Test Mode Notification', GoodByeCaptcha::PLUGIN_SLUG);

		$adminFullName = MchWpBase::getAdminFullName();
		empty($adminFullName) ? $adminFullName = MchWpBase::getAdminDisplayName() : null;

		$sectionId = isset($arrParameters['section']) && !empty($arrParameters['module']) ? GoodByeCaptcha::getModulesControllerInstance()->getAdminModuleInstance($arrParameters['module'])->getSettingOptionIdByOptionName($arrParameters['section']) : null;

		$submittedForm  = !empty($arrParameters['module'])  ? $arrParameters['module'] : '';
		$submittedForm .=  (null === $sectionId) ? '' : '/' . GoodByeCaptcha::getModulesControllerInstance()->getAdminModuleInstance($arrParameters['module'])->getSettingOptionDisplayTextByOptionId($sectionId);
		$rejectReason = (true !== $isTokenValid) ? GdbcReasonDataSource::getReasonDescription($isTokenValid) : null;

		ob_start();
			require_once (dirname(__FILE__) . '/notifications/email/templates/notification-test-mode.php');

		$gdbcEmailNotification->EmailBodyContent = ob_get_clean();

//		$gdbcEmailNotification->EmailBodyContent .= "\n";
//		$gdbcEmailNotification->EmailBodyContent .= print_r(GdbcTokenController::getInstance()->getTokenDebugData(), true);

		$gdbcEmailNotification->send();
	}
}