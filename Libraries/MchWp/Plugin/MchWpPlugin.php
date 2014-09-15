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
abstract class MchWpPlugin extends MchWpBase implements MchWpIPlugin
{
	/**
	 *
	 * @var \MchWpModulesController  
	 */
	protected $ModulesController     = null;
	
	protected function __construct(array $arrPluginInfo) 
	{
		parent::__construct($arrPluginInfo);
		
		if(null === ($this->ModulesController = $this->getModulesControllerInstance($arrPluginInfo)))
			throw new Exception ('Please implement getModulesControllerInstance method! ');
		
		if( ! ($this->ModulesController instanceof MchWpModulesController) )
			throw new Exception ('The getModulesControllerInstance method should return an instance of MchWpModulesController class! ');


		add_action('init', array($this, 'initPlugin' ) );
		
	}
	
	public function getRegisteredModules()
	{
		return $this->ModulesController->getRegisteredModules();
	}
	
	public function initPlugin()
	{
		if(null !== $this->PLUGIN_DOMAIN_PATH)
		{	
			$locale = apply_filters('plugin_locale', get_locale(), $this->PLUGIN_SLUG);

			load_textdomain($this->PLUGIN_SLUG, trailingslashit( WP_LANG_DIR ) . $this->PLUGIN_SLUG . '/' . $this->PLUGIN_SLUG . '-' . $locale . '.mo' );

			load_plugin_textdomain($this->PLUGIN_SLUG, false, $this->PLUGIN_DIRECTORY_NAME . '/' . $this->PLUGIN_DOMAIN_PATH . '/' ); 
		}
	}	
	
	
}