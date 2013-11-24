<?php
/*
 * HDHRTV is a streaming web application for the HDHomeRun cable tuner
 * Copyright (C) 2013 Brian Kirkman (kirkman [dot] brian [at] gmail [dot] org)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
 
class View
{
	protected $template;
	protected $fields = array();
    
	public function display($template, array $fields = array())
	{
		foreach ($fields as $name => $value) {
            		$this->fields[$name] = $value;
        	}

		$template = $template . ".php";
		$this->template = $template;

		extract($this->fields);
		ob_start();
		include $this->template;
		return ob_get_clean();
	}
}
