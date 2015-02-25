<?php

/**
 *
 * @package   GoodBye Captcha
 * @author    Mihai Chelaru
 * @license   GPL-2.0+
 * @link      http://www.goodbyecaptcha.com
 * @copyright 2014 GoodBye Captcha
 *
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

class_exists('GoodByeCaptcha', false) || require_once 'goodbye-captcha.php';

class GoodByeCaptchaUninstaller
{
	public function __construct()
	{
		GoodByeCaptcha::getInstance();

		if(!current_user_can( 'activate_plugins'))
		{
			exit;
		}

		delete_site_option('gdbc-blocked-attempts');
		GdbcTaskScheduler::unscheduleGdbcTasks();

		if(MchWpBase::isMultiSite())
		{
			foreach(self::getAllBlogIds() as $blogId)
			{
				switch_to_blog( $blogId );

				self::singleUninstall($blogId);

				restore_current_blog();
			}
		}

	}

	private static function singleUninstall($blogId)
	{
		$attemptEntity = new GdbcAttemptEntity();
		foreach(array_keys(GoodByeCaptcha::getModulesControllerInstance()->getRegisteredModules()) as $module)
		{
			$moduleInstance = GoodByeCaptcha::getModulesControllerInstance()->getAdminModuleInstance($module);
			if(null === $moduleInstance)
				continue;

			$moduleInstance->getModuleSetting()->deleteAllSettingOptions();
		}

		$GLOBALS['wpdb']->query("DROP TABLE IF EXISTS " . $attemptEntity->getTableName());

		$GLOBALS['wpdb']->query("OPTIMIZE TABLE `" . $GLOBALS['wpdb']->prefix . "options`");

	}

	private static function getAllBlogIds()
	{
		global $wpdb;

		if( empty($wpdb->blogs) )
			return array();

		return false === ( $arrBlogs = $wpdb->get_col(  "SELECT blog_id FROM $wpdb->blogs WHERE archived = '0' AND spam = '0' AND deleted = '0'" ) ) ? array() : $arrBlogs;

	}

}

new GoodByeCaptchaUninstaller();
