<?php
/*
 * HDHRTV is a streaming web application for the HDHomeRun cable tuner
 * Copyright (C) 2013 Brian Kirkman (kirkman [dot] brian [at] gmail [dot] com)
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
 
class Favorites
{

        public function get_favorites()
        {
                if (isset($_COOKIE[COOKIE_FAVORITES]))
                {
                        //if the user favorites are retrieved, unserialize the favorites
                        $favorites = unserialize($_COOKIE[COOKIE_FAVORITES]);
                }
                else
                {
                        //if there is no user favorites cookie, create default user favorites and cookie, which returns default user favorites array
                        $favorites = $this->_create_default_favorites();
                }

                return $favorites;
        }


        public function add_favorite($channel)
        {
		$favorites = $this->get_favorites();
		if(($key = array_search($channel, $favorites)) !== false)
		{
			// If channel already exists, return false
			return false;
		}
		else
		{
			$favorites[] = $channel;
			$this->_create_favorites($favorites);

			return $favorites;
		}
        }


        public function delete_favorite($channel)
        {
		$favorites = $this->get_favorites();
		if(($key = array_search($channel, $favorites)) !== false)
		{
			// If channel exists, delete.
    			unset($favorites[$key]);
                	$this->_create_favorites($favorites);
			return $favorites;
		}
		else
		{
			// If channel does not exist, return false.
			return false;
		}
        }


        private function _create_default_favorites()
        {
		$favorites = array();
                $this->_create_favorites($favorites);

                return $favorites;
        }


        private function _create_favorites($favorites)
        {
                setcookie(COOKIE_FAVORITES, serialize($favorites), time() + 60 * 60 * 24 * 365 * 2);
        }
}
