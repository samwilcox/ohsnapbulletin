<?php

/**
 * OH SNAP! BULLETIN
 * 
 * By Sam Wilcox <sam@ohsnapbulletin.com>
 * http://www.ohsnapbulletin.com
 * (C)Copyright Oh Snap! Bulletin. (R)All Rights Reserved.
 * 
 * USER-LICENSE AGREEMENT:
 * 
 * This file is part of Oh Snap! Bulletin.
 * 
 * Oh Snap! Bulletin is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * Oh Snap! Bulletin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Oh Snap! Bulletin.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * USER-DEFINABLE CONFIGURATION SECTION
 */

/**
 * APPLICATION BASE PATH
 * This is the absolute or relative path to where this application is
 * installed on your web server. Be sure this path includes a trailing
 * forward slash ("/") at the end.
 */
 
 $root_path = dirname(__FILE__) . '/';
 
/**
 * PHP EXTENSION
 * This is an optional setting, which is not recommended to change
 * unless it is neccessary. If you change the PHP extension here, be
 * sure to change all files ending with .php to the new extension.
 */
 
 $php_extension = 'php';
 
/**
 * ERROR REPORTING
 * If you are debugging, by uncommenting the error reporting below,
 * you will have access to useful debug information. Each error flag
 * is seperated by the pipe ( "|" ) character.
 */
 
 error_reporting( E_STRICT | E_ERROR | E_WARNING | E_PARSE | E_RECOVERABLE_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_USER_WARNING );
 
/**
 * END USER-DEFINEABLE CONFIGURATION SECTION
 */

define( 'ROOT_PATH', $root_path );
define( 'SNAP_VERSION', '1.0.0' );
define( 'SNAP_INIT', true );
define( 'PHP_EXT', $php_extension );

require_once ( ROOT_PATH . 'system/core_classes/snap.class.php' );

$SNAP = new OHSNAPClass;

$SNAP->system_init();

?>