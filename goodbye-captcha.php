<?php

/**
 *
 * @package   GoodBye Captcha
 * @author    Mihai Chelaru
 * @license   GPL-2.0+
 * @link      http://www.goodbyecaptcha.com
 * @copyright 2014 GoodBye Captcha
 *
 * @wordpress-plugin
 * Plugin Name: GoodBye Captcha
 * Plugin URI: http://www.goodbyecaptcha.com
 * Description: GoodBye Captcha is the best solution for protecting your site without annoying captcha images.
 * Version: 1.0.7
 * Author: Mihai Chelaru
 * Author URI: http://www.goodbyecaptcha.com
 * Text Domain: goodbye-captcha
 * License: GPL-2.0+
 * Domain Path: /languages 
 */


defined( 'WPINC' ) || exit;

final class GoodByeCaptcha
{
	
	CONST PLUGIN_VERSION    = '1.0.7';
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
		
									'GdbcModulesController'   =>  '/engine/GdbcModulesController.php',
									'GdbcBasePublicPlugin'    =>  '/engine/GdbcBasePublicPlugin.php',
									'GdbcBaseAdminPlugin'     =>  '/engine/GdbcBaseAdminPlugin.php',
									'GdbcTokenController'     =>  '/engine/GdbcTokenController.php',
									'GdbcPluginUpdater'       =>  '/engine/GdbcPluginUpdater.php',
									'GdbcPluginUtils'         =>  '/engine/GdbcPluginUtils.php',
									'GdbcRequest'			  =>  '/engine/GdbcRequest.php',
									'GdbcPublic'              =>  '/public/GdbcPublic.php',
									'GdbcAdmin'               =>  '/admin/GdbcAdmin.php',
									'MchCrypt'				  =>  '/Libraries/MchCrypt/MchCrypt.php',
									'MchWp'				      =>  '/Libraries/MchWp/MchWp.php',
									'MchHttpUtil'			  =>  '/Libraries/MchHttp/MchHttpUtil.php',	
								);

	
	private static $DIR_PATH      = null;
	private static $isFreeVersion = true;
	
	private function __construct()
	{
		spl_autoload_register('self::classAutoLoad');

		self::$DIR_PATH = dirname( __FILE__ );
		
		$pluginInstance = MchWp::isUserInDashboad() ? GdbcAdmin::getInstance(self::$arrPluginInfo) : GdbcPublic::getInstance(self::$arrPluginInfo);
		
		self::$isFreeVersion = (1 === count($pluginInstance->getRegisteredModules()));

	}
	
	public static function isFreeVersion()
	{
		return self::$isFreeVersion;
	}
	
	public static function classAutoLoad($className)
	{
		if( !isset(self::$arrClassMap[$className]) )
			return null;
		
		(null === self::$DIR_PATH) ? self::$DIR_PATH = dirname( __FILE__ ) : null;
		
		$filePath = self::$DIR_PATH . DIRECTORY_SEPARATOR . trim(self::$arrClassMap[$className], '/\\');
		
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
		spl_autoload_register('self::classAutoLoad');
		
		if ( ! MchWp::isUserInDashboad() )
			return null;
		
		return GdbcAdmin::activatePlugin(self::$arrPluginInfo, $isForNetwork);

	}
	
	public static function deactivate($isForNetwork)
	{
		spl_autoload_register('self::classAutoLoad');

		if ( ! MchWp::isUserInDashboad() )
			return null;
		
		return GdbcAdmin::deactivatePlugin(self::$arrPluginInfo, $isForNetwork);

	}

}


/*
 * Registered hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */

register_activation_hook  ( __FILE__, array( 'GoodByeCaptcha', 'activate' ));
register_deactivation_hook( __FILE__, array( 'GoodByeCaptcha', 'deactivate'));

add_action('plugins_loaded', array( 'GoodByeCaptcha', 'getInstance' ));

