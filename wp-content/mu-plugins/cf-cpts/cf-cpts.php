<?php

/**
 * Plugin Name: CF Custom Post Types
 * Description: This plugin registers the custom post types with their post meta and maybe custom taxonomies.
 * Plugin URI: http://www.crowdfavorite.com
 * Author: Crowd Favorite
 * Author URI: http://www.crowdfavorite.com
 * Version: 3
 * License: GPL2
 *
 * @package cf-cpts
 */

/*
Copyright (C) 2020 Crowd Favorite crowdfavorite@gmail.com

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once 'config.php';
require_once 'classes/class-core.php';
require_once 'classes/class-modelbase.php';

// Instantiate the plugin.
CrowdFavorite\CPTs\Core::getInstance();
