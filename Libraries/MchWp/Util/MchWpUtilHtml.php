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

/**
 *
 * @author Mihai Chelaru
 */
final class MchWpUtilHtml
{
	const FORM_ELEMENT_INPUT_HIDDEN   = 'hidden';
	const FORM_ELEMENT_INPUT_TEXT     = 'text';	
	const FORM_ELEMENT_INPUT_CHECKBOX = 'checkbox'; 

	
	public static function createInputElement(array $arrAttributes)
	{
		$code  = '<input';
		
		$code .= isset($arrAttributes['type'])   ? " type=\"{$arrAttributes['type']}\"" : " type=\"text\"";
		
		unset($arrAttributes['type']);
		
		foreach ($arrAttributes as $key => $value)
		{
			$code .= " {$key}=\"{$value}\"";
		}
		
		$code .= ' />';

		return $code;
	}		

	public static function createLabelElement($innerText, $forInputId)
	{
		return '<label>' . esc_html($innerText) . '<label>';
	}
	
	public static function createAnchorElement($innerText, array $arrAttributes)
	{
		$code  = '<a';
		
		foreach ($arrAttributes as $key => $value)
		{
			$code .= " {$key}=\"{$value}\"";
		}
		
		$code .= '>' . esc_html($innerText) . '</a>';
		
		return $code;
	}		
	
	
	private function __construct()
	{}
}
