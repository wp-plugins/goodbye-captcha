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

final class MchWpSettingsList implements Countable, Iterator
{
	/**
	 *
	 * @var type array 
	 */
	private $arrSettings = array(); 
	
	/**
	 *
	 * @var type inteher
	 */
	private $position   = 0;
	
	/**
	 *
	 * @var type integer
	 */
	private $counter    = 0;
	
	
	public function __construct()
	{}
	
	
	public function addModuleSetting(MchWpSetting $setting)
	{
		$this->arrSettings[] = $setting;
		++$this->counter;      
	}
	
	/**
	 * 
	 * @return integer
	 */
	public function count()
	{
		return $this->counter;
	}

	public function current()
	{
		$this->arrSettings[$this->position];
	}

	public function key()
	{
		return $this->position;
	}

	public function next()
	{
		++$this->position;
	}

	public function rewind()
	{
		$this->position = 0;
	}
	
	/**
	 * Checks if the iterator is valid
	 * 
	 * @return void
	 */
	public function valid()
	{
		return isset($this->arrSettings[$this->position]);
	}

}