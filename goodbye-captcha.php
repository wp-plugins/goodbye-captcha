<?php
/**
 *
 * @package   GoodBye Captcha
 * @author    Mihai Chelaru
 * @license   GPL-2.0+
 * @link      http://www.goodbyecaptcha.com
 * @copyright 2015 GoodBye Captcha
 *
 * @wordpress-plugin
 * Plugin Name: GoodBye Captcha
 * Plugin URI: http://www.goodbyecaptcha.com
 * Description: An extremely powerful anti-spam plugin that blocks spambots without annoying captcha images.
 * Version: 1.1.19
 * Author: Mihai Chelaru
 * Author URI: http://www.goodbyecaptcha.com
 * Text Domain: goodbye-captcha
 * License: GPL-2.0+
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) || exit;

class GoodByeCaptcha
{

	CONST PLUGIN_VERSION    = '1.1.19';
	CONST PLUGIN_SHORT_CODE = 'gdbc';
	CONST PLUGIN_SLUG       = 'goodbye-captcha';
	CONST PLUGIN_SITE_URL   = 'http://www.goodbyecaptcha.com';

	private static $arrPluginInfo = array(

		'PLUGIN_DOMAIN_PATH' => 'languages',
		'PLUGIN_MAIN_FILE'   => __FILE__,
		'PLUGIN_SHORT_CODE'  => self::PLUGIN_SHORT_CODE,
		'PLUGIN_VERSION'     => self::PLUGIN_VERSION,
		'PLUGIN_SLUG'        => self::PLUGIN_SLUG,

	);

	private static $arrClassMap = array(

		'GdbcModulesController'   => '/engine/GdbcModulesController.php',
		'GdbcBasePublicPlugin'    => '/engine/GdbcBasePublicPlugin.php',
		'GdbcBaseAdminPlugin'     => '/engine/GdbcBaseAdminPlugin.php',
		'GdbcTokenController'     => '/engine/GdbcTokenController.php',
		'GdbcPluginUpdater'       => '/engine/GdbcPluginUpdater.php',
		'GdbcPluginUtils'         => '/engine/GdbcPluginUtils.php',
		'GdbcRequest'             => '/engine/GdbcRequest.php',
		'GdbcPublic'              => '/public/GdbcPublic.php',
		'GdbcAdmin'               => '/admin/GdbcAdmin.php',
		'MchCrypt'                => '/Libraries/MchCrypt/MchCrypt.php',
		'MchWp'                   => '/Libraries/MchWp/MchWp.php',
		'MchHttpUtil'             => '/Libraries/MchHttp/MchHttpUtil.php',
		'MchHttpRequest'          => '/Libraries/MchHttp/MchHttpRequest.php',
		'GdbcAttemptEntity'       => '/engine/dbaccess/entities/GdbcAttemptEntity.php',
		'GdbcAttemptsManager'     => '/engine/dbaccess/GdbcAttemptsManager.php',
		'GdbcCountryDataSource'   => '/engine/dbaccess/GdbcCountryDataSource.php',
		'GdbcReasonDataSource'    => '/engine/dbaccess/GdbcReasonDataSource.php',
		'GdbcBaseAdminModule'     => '/engine/modules/GdbcBaseAdminModule.php',
		'GdbcBasePublicModule'    => '/engine/modules/GdbcBasePublicModule.php',
		'GdbcCheckAttemptsTask'   => '/engine/tasks/GdbcCheckAttemptsTask.php',
		'GdbcLogsCleanerTask'     => '/engine/tasks/GdbcLogsCleanerTask.php',
		'GdbcTaskScheduler'       => '/engine/GdbcTaskScheduler.php',

		'GdbcEmailNotification'       => '/engine/notifications/email/GdbcEmailNotification.php',
		'GdbcNotificationsController' => '/engine/GdbcNotificationsController.php',
		'GdbcAjaxController'          => '/engine/GdbcAjaxController.php',
	);

	private static $isNetworkActivated = false;

	protected function __construct()
	{
		$pluginInstance = (MchWp::isUserInDashboad() || MchWp::isAjaxRequest()) ? GdbcAdmin::getInstance(self::$arrPluginInfo) : GdbcPublic::getInstance(self::$arrPluginInfo);
		self::$isNetworkActivated = $pluginInstance->isNetworkActivated();

		GdbcTaskScheduler::scheduleGdbcTasks();

	}

	/**
	 *
	 * @return \GdbcModulesController
	 */
	public static function getModulesControllerInstance()
	{
		return GdbcModulesController::getInstance(self::$arrPluginInfo);
	}

	public static function classAutoLoad($className)
	{
		if( !isset(self::$arrClassMap[$className]) )
			return null;

		$filePath = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . trim(self::$arrClassMap[$className], '/\\');

		return file_exists($filePath) ? include_once $filePath : null;
	}

	public static function getPluginInfo()
	{
		return self::$arrPluginInfo;
	}

	public static function getInstance()
	{
		static $gdbcInstance = null;
		return (null !== $gdbcInstance) ? $gdbcInstance : $gdbcInstance = new self();
	}

	public static function activate($isForNetwork)
	{
		if ( ! MchWp::isUserInDashboad() )
			return null;

		GdbcAdmin::activatePlugin(self::$arrPluginInfo, $isForNetwork);

	}

	public static function deactivate($isForNetwork)
	{


		if ( ! MchWp::isUserInDashboad() )
			return null;

		GdbcAdmin::deactivatePlugin(self::$arrPluginInfo, $isForNetwork);

		GdbcTaskScheduler::unscheduleGdbcTasks();
	}

}

if(ABSPATH !== '')
{
	spl_autoload_register(array('GoodByeCaptcha', 'classAutoLoad'));

	if(MchWp::isAjaxRequest())
	{
		GdbcAjaxController::processRequest();
	}

	if(!empty($_GET['gdbc-client']))
	{
		if(file_exists(dirname(__FILE__) . '/public/scripts/gdbc-client.js.php')) {
			require_once(ABSPATH .'wp-includes/pluggable.php');
			function_exists('wp_cookie_constants') ? wp_cookie_constants() : null;
			require dirname( __FILE__ ) . '/public/scripts/gdbc-client.js.php';
			exit;
		}
	}


	register_activation_hook(__FILE__, array('GoodByeCaptcha', 'activate'));

	register_deactivation_hook(__FILE__, array('GoodByeCaptcha', 'deactivate'));

	add_action('plugins_loaded', array('GoodByeCaptcha', 'getInstance'));

}
