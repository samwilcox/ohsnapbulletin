<?php

/*************************************************************************************
 ** =============================================================================== **
 ** OH SNAP! BULLETIN                                                               **
 ** =============================================================================== **
 ** By Sam Wilcox <sam@ohsnapbulletin.com                                           **
 ** http://www.ohsnapbulletin.com                                                   **
 ** (C)Copyright Oh Snap! Bulletin. (R)All Rights Reserved.                         **
 ** =============================================================================== **
 ** USER-LICENSE AGREEMENT:                                                         **
 **                                                                                 **
 ** Oh Snap! Bulletin is free software: you can redistribute it and/or modify it    **
 ** under the terms of the GNU General Public License as published by the Free      **
 ** Software Foundation, either version 3 of the License, or (at your option) any   **
 ** later version.                                                                  **
 **                                                                                 **
 ** Oh Snap! Bulletin is distributed in the hope that it will be useful, but        **
 ** WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or   **
 ** FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more  **
 ** details.                                                                        **
 **                                                                                 **
 ** You should have received a copy of the GNU General Public License               **
 ** along with Oh Snap! Bulletin.  If not, see <http://www.gnu.org/licenses/>.      **
 ** =============================================================================== **
 *************************************************************************************/
 
/**
 * USER-DEFINEABLE SETTINGS
 */

/**
 * APPLICATION BASE PATH
 * This is the absolute or relative path to where this application is installed on
 * your web server. Be sure this path includes a trailing forward slash ("/") at the
 * end.
 */
 
 $root_path = dirname(__FILE__) . '/';
 
/**
 * PHP EXTENSION
 * This is an optional setting, which is not recommended to change unless it is
 * neccessary. If you change this setting, be sure to change all files ending with
 * .php within this installation to the new PHP extension configured here.
 */
 
 $php_extension = 'php';
 
/**
 * ERROR REPORTING
 * If you debugging, by uncommenting the error reporting below, you will have access
 * to useful debug information. Each error flag is seperated by the pipe ("|")
 * character.
 */
 
 error_reporting( E_STRICT | E_ERROR | E_WARNING | E_PARSE | E_RECOVERABLE_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_USER_WARNING );

/**
 * END USER-DEFINEABLE SETTINGS
 */

if ( substr( $root_path, strlen( $root_path ) - 1, 1 ) != '/' ) $root_path = $root_path . '/';
if ( substr( $php_extension, 0, 1 ) == '.') $php_extension = substr( $php_extension, 1, strlen( $php_extension ) );

define( '_ROOT_PATH', $root_path );
define( '_SNAP_VERSION', '1.0.0' );
define( '_SNAP_INIT', true );
define( '_PHP_EXT', $php_extension );

require( _ROOT_PATH . 'application/core_classes/snap.class.' . _PHP_EXT );

$SNAP = new OhSnapClass;
$SNAP->system_init();

?>