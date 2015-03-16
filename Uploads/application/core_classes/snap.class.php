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

if ( ! defined( '_SNAP_INIT' ) ) { die( '<h1>Oh Snap! Bulletin Error</h1>You do not valid permissions to access this file directly.' ); }

class OhSnapClass {
    
    public $snap_version = _SNAP_VERSION;
    public $php_ext      = _PHP_EXT;
    public $CFG          = array();
    public $INC          = array();
    public $CACHE        = array();
    public $MEMBER       = array();
    public $AGENT        = array();
    public $SESSION      = array();
    public $T            = array();
    public $TOSQL        = array();
    public $LANG         = array();
    public $ERRORS       = array();
    public $TIMER        = array();
    public $FACEBOOK     = array();
    public $MOBILE       = array();
    public $base_url     = '';
    public $wrapper      = '';
    public $skin_url     = '';
    public $skin_path    = '';
    public $imageset_url = '';
    public $lang_path    = '';
    public $db_prefix    = 'ohsnap_';
    
    public function __construct()
    {
        $this->start_execution_timer(); $this->fix_iis_uri();
        $this->AGENT['ip']       = $this->fetch_server_var( 'REMOTE_ADDR' );
        $this->validate_ip( $this->AGENT['ip'] );
        $this->AGENT['hostname'] = gethostbyaddr( $this->AGENT['ip'] );
        $this->AGENT['agent']    = $this->fetch_server_var( 'HTTP_USER_AGENT' );
        
        $this->browser_detection( $this->AGENT['agent'] );
    }
    
    public function system_init()
    {
        $CFG = array();
        require_once( _ROOT_PATH . 'config.inc.' . $this->php_ext );
        $this->CFG = $CFG;
        
        $this->filter_incoming_data();     $this->initialize_database();
        $this->initialize_core_classes();  $this->initialize_database_cache();
        $this->populate_system_settings(); $this->search_bot_detection( $this->AGENT['agent'] );
        $this->start_gzip_compression();   $this->setup_urls();
        
        $this->SESSIONS->session_gc();          $this->SESSIONS->manage_sessions(); 
        $this->SESSIONS->check_online_record(); $this->record_visits();
        
        if ( isset( $_SESSION['ohsnap_username'] ) )
        {
            $this->MEMBER['status'] = true; $this->MEMBER['username'] = $_SESSION['ohsnap_username'];
        }
        else
        {
            $this->MEMBER['status'] = false; $this->MEMBER['username'] = 'Guest';
        }
        
        $this->configure_urls_paths(); $this->load_language();
        $this->load_errors();          $this->load_skin();
        
        ( isset( $this->INC['action'] ) ) ? $cls = strtolower( $this->INC['cls'] ) : $cls = 'forums';
        
        if ( file_exists( _ROOT_PATH . 'application/classes/' . $cls . '.class.' . $this->php_ext ) )
        {
            require_once( _ROOT_PATH . 'application/classes/' . $cls . '.class.' . $this->php_ext );            
            $init = new $cls; $init->SNAP =& $this; $init->class_init();
        }
        else
        {
            require_once( _ROOT_PATH . 'application/classes/forums.class.' . $this->php_ext );
            $init = new forums; $init->SNAP =& $this; $init->class_init();
        }
        
        session_write_close(); $this->DB->db_disconnect();
    }
    
    public function fetch_server_var( $v ) { return trim( $_SERVER[$v] ); }
    public function fetch_env_var( $v ) { return trim( $_ENV[$v] ); }
    
    public function fix_iis_uri()
    {
        if ( ! isset( $_SERVER['REQUEST_URI'] ) )
        {
            $_SERVER['REQUEST_URI'] = substr( $_SERVER['PHP_SELF'], 1 );
            if ( isset( $_SERVER['QUERY_STRING'] ) ) $_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
        }
    }
    
    public function start_execution_timer()
    {
        $micro_time = microtime(); $micro_time = explode( ' ', $micro_time ); $this->TIMER['start'] = $micro_time[1] + $micro_time[0];
    }
    
    public function stop_execution_timer()
    {
        $micro_time = microtime(); $micro_time = explode( ' ', $micro_time ); $micro_time = $micro_time[1] + $micro_time[0]; $micro_time = ( $micro_time - $this->TIMER['start'] );
        $this->TIMER['end'] = round( $micro_time, 2 );
    }
    
    public function validate_ip( $ip )
    {
        if ( preg_match( "/^((1?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(1?\d{1,2}|2[0-4]\d|25[0-5])$/", $ip ) )
        {
            $octs = explode( '.', $ip );
            
            foreach ( $octs as $octet )
            {
                if ( ( intval( $octet ) > 255 ) OR ( intval( $octet ) < 0 ) ) $valid = false;
            }
            
            $valid = true;
        }
        else
        {
            $valid = false;
        }
        
        if ( ! $valid ) die( '<h1>Oh Snap! Bulletin Error</h1>We are unable to determine your IP address. In order to access our site, you must have a valid IP address.' );
    }
    
    public function new_cookie( $name, $val, $expire ) { setcookie( $name, $val, $expire, $this->CFG['cookie_path'], $this->CFG['cookie_domain'] ); }
    
    public function delete_cookie( $name, $php_cookie = false )
    {
        setcookie( $name, '', time() - 3600, ( ! $php_cookie ) ? $this->CFG['cookie_path'] : '', ( ! $php_cookie ) ? $this->CFG['cookie_domain'] : '' );
    }
    
    public function browser_detection( $agent )
    {
        $title = ''; $agent = strtolower( $agent ); $found = false;
        
        $browsers = array( 'Windows Mobile'       => 'IEMobile',
                           'Android Mobile'       => 'Android',
                           'iPhone Mobile'        => 'iPhone',
                           'Blackberry'           => 'Blackberry',
                           'Blazer'               => 'Blazer',
                           'Bolt'                 => 'BOLT',
                           'Opera Mini'           => 'Opera Mini',
                           'Opera Mobile'         => 'Opera Mobi',
                           'Skyfire'              => 'Skyfire',
                           'Firefox'              => 'Firefox',
                           'Google Chrome'        => 'Chrome',
                           'Internet Explorer'    => 'MSIE',
                           'Internet Explorer v1' => 'microsoft internet explorer',
                           'Opera'                => 'Opera',
                           'Apple Safari'         => 'Safari',
                           'Konqueror'            => 'Konqueror',
                           'America Online'       => 'America Online Browser',
                           'AOL'                  => 'AOL',
                           'Netscape'             => 'Navigator' );
                           
        foreach ( $browsers as $k => $v ) { if ( preg_match( "/$v/i", $agent ) ) $title = $k; }        
        $this->AGENT['browser_title'] = $title;
    }
    
    public function search_bot_detection( $agent )
    {
        $bots = explode( ',', $this->CFG['bot_names'] );        
        for ( $i = 0; $i < count( $bots ); $i++ ) if ( strpos( ' ' . strtolower( $agent ), strtolower( $bots[$i] ) ) != false ) $name = $bots[$i];
        ( isset( $name ) ) ? ( $this->SESSION['bot'] = 1 ) && ( $this->SESSION['bot_name'] = $name ) : ( $this->SESSION['bot'] = 0 ) && ( $this->SESSION['bot_name'] = '' );
    }
    
    public function filter_incoming_data()
    {
        foreach ( $_GET  as $k => $v ) $this->INC[$k] = filter_var( $v, FILTER_SANITIZE_STRING );
        foreach ( $_POST as $k => $v ) $this->INC[$k] = filter_var( $v, FILTER_SANITIZE_STRING );
    }
    
    public function start_gzip_compression() { if ( $this->CFG['gzip_compression'] ) ob_start( 'ob_gzhandler' ); }
    
    public function initialize_core_classes()
    {
        require_once( _ROOT_PATH . 'application/core_classes/sessions.class.' . $this->php_ext );
        
        $this->SESSIONS = new OhSnapSessions;
        $this->SESSIONS->SNAP =& $this;
    }
    
    public function initialize_database()
    {
        if ( ! file_exists( _ROOT_PATH . 'application/db_drivers/' . $this->CFG['db_driver'] . '.driver.class.' . $this->php_ext ) )
        {
            die( '<h1>Oh Snap! Bulletin Error</h1>The configured database driver does not exist.' );
        }
        else
        {
            require_once( _ROOT_PATH . 'application/db_drivers/' . $this->CFG['db_driver'] . '.driver.class.' . $this->php_ext );
        }
        
        if ( ! file_exists( _ROOT_PATH . 'application/db_drivers/' . $this->CFG['db_driver'] . '.sql_queries.class.' . $this->php_ext ) )
        {
            die( '<h1>Oh Snap! Bulletin Error</h1>The SQL query statements does not exist for the configured database driver.' );
        }
        else
        {
            require_once( _ROOT_PATH . 'application/db_drivers/' . $this->CFG['db_driver'] . '.sql_queries.class.' . $this->php_ext );
        }
        
        $this->DB = new OhSnapDatabase;
        $this->DB->SNAP =& $this;
        $this->DB->set_hostname( $this->CFG['db_hostname'] );
        $this->DB->set_port( $this->CFG['db_port'] );
        $this->DB->set_database( $this->CFG['db_database'] );
        $this->DB->set_username( $this->CFG['db_username'] );
        $this->DB->set_password( $this->CFG['db_password'] );
        $this->DB->set_persistant( $this->CFG['db_persistant'] );
        $this->DB->set_log_errors( $this->CFG['db_log_errors'] );
        $this->DB->db_establish_connection();
        
        if ( $this->CFG['db_prefix'] != '' ) $this->db_prefix = $this->CFG['db_prefix'];
        
        $this->SQL = new OhSnapSQL;
        $this->SQL->SNAP =& $this;
    }
    
    public function initialize_database_cache()
    {
        switch ( $this->CFG['db_cache'] )
        {
            case true:
            $caching = array(
                'members'             => 'member_id',
                'installed_languages' => 'language_id',
                'installed_skins'     => 'skin_id',
                'forums'              => 'forum_id',
                'system_settings'     => 'setting_id',
                'feature_permissions' => 'feature_id',
                'avatars'             => 'avatar_id',
                'notifications'       => 'notification_id',
                'pm_inbox'            => 'inbox_id',
                'pm_messages'         => 'message_id',
                'topics'              => 'topic_id',
                'forums_read'         => 'read_id',
                'posts'               => 'post_id',
                'statistics'          => 'statistic_id',
                'visits'              => 'visit_id'
            );
            
            switch ( $this->CFG['db_cache_method'] )
            {
                case 'dbcache':
                $sql = $this->DB->db_query( $this->SQL->sql_fetch_stored_cache() );
                
                while ( $r = $this->DB->db_fetch_array( $sql ) )
                {
                    unset( $records );
                    
                    foreach ( $caching as $k => $v )
                    {
                        ( $k == 'groups' ) ? $sorting = ' ORDER BY sorting ASC' : $sorting = ' ORDER BY ' . $v . ' ASC';
                        
                        if ( $v['title'] == $k )
                        {
                            if ( $r['data'] != '' )
                            {
                                $this->CACHE[$k] = unserialize( $r['data'] );
                            }
                            else
                            {
                                if ( $k == 'forums' )
                                {
                                    $sql_l = $this->DB->db_query( $this->SQL->sql_fetch_forums_with_depth() );
                                }
                                else
                                {
                                    $this->TOSQL = array( 'table' => $k, 'sorting' => $sorting );                                    
                                    $sql_l       = $this->DB->db_query( $this->SQL->sql_fetch_for_cache() );
                                }
                                
                                while ( $record = $this->DB->db_fetch_array( $sql_l ) ) $records[] = $record; $this->DB->db_free_result( $sql_l );
                                
                                $to_cache = serialize( $records );
                                
                                $this->TOSQL = array( 'to_cache' => $to_cache, 'title' => $k );
                                $this->DB->db_query( $this->SQL->sql_update_cache_data() );
                                $this->CACHE[$k] = unserialize( $to_cache );
                            }
                        }
                    }
                }
                
                $this->DB->db_free_result( $sql );
                break;
                
                case 'filecache':
                $cache_dir    = $this->CFG['cache_dir']; $db_cache_dir = $this->CFG['db_cache_dir'];
                
                if ( substr( $cache_dir, ( strlen( $cache_dir ) - 1 ), strlen( $cache_dir ) ) == '/' ) $cache_dir = substr( $cache_dir, 0, ( strlen( $cache_dir ) - 1 ) );
                if ( substr( $cache_dir, 0, 1 ) == '/' ) $cache_dir = substr( $cache_dir, 1, strlen( $cache_dir ) );
                if ( substr( $db_cache_dir, ( strlen( $db_cache_dir ) - 1 ), strlen( $db_cache_dir ) ) == '/' ) $db_cache_dir = substr( $db_cache_dir, 0, ( strlen( $db_cache_dir ) - 1 ) );
                if ( substr( $db_cache_dir, 0, 1 ) == '/' ) $db_cache_dir = substr( $db_cache_dir, 1, strlen( $db_cache_dir ) );
                
                $cache_dir = _ROOT_PATH . $cache_dir . '/' . $db_cache_dir . '/';
                
                if ( ! file_exists( $cache_dir ) )  die( '<h1>Oh Snap! Bulletin Error</h1>The configured database cache directory does not exist.' );
                if ( ! is_readable( $cache_dir ) )  die( '<h1>Oh Snap! Bulletin Error</h1>The configured database cache directory does not have read permissions.' );
                if ( ! is_writeable( $cache_dir ) ) die( '<h1>Oh Snap! Bulletin Error</h1>The configured database cache directory does not have write permissions.' ); 
                
                foreach ( $caching as $k => $v )
                {
                    if ( ! file_exists( $cache_dir . $k . '.cache.' . $this->php_ext ) )
                    {
                        touch ( $cache_dir . $k . '.cache.' . $this->php_ext ); chmod( $cache_dir . $k . '.cache.' . $this->php_ext, 0666 );
                    }
                }    
                
                foreach ( $caching as $k => $v )
                {
                    unset( $records );
                    
                    if ( filesize( $cache_dir . $k . '.cache.' . $this->php_ext ) == 0 )
                    {
                        ( $k == 'groups' ) ? $sorting = ' ORDER BY sorting ASC' : $sorting = ' ORDER BY ' . $v . ' ASC';
                        
                        if ( $k == 'forums' )
                        {
                            $sql = $this->DB->db_query( $this->SQL->sql_fetch_forums_with_depth() );
                        }
                        else
                        {
                            $this->TOSQL = array( 'table' => $k, 'sorting' => $sorting );
                            $sql         = $this->DB->db_query( $this->SQL->sql_fetch_for_cache() );
                        }
                        
                        while ( $record = $this->DB->db_fetch_array( $sql ) ) $records[] = $record; $this->DB->db_free_result( $sql );
                        
                        $to_cache = serialize( $records );
                        
                        if ( $fh = @fopen( $cache_dir . $k . '.cache.' . $this->php_ext, 'w' ) )
                        {
                            @fwrite( $fh, $to_cache );
                            @fclose( $fh );
                        }
                        else
                        {
                            die( '<h1>Oh Snap! Bulletin Error</h1>The attempt to write the selected database cache file failed.' );
                        }
                        
                        $this->CACHE[$k] = unserialize( $to_cache );
                    }
                    else
                    {
                        $this->CACHE[$k] = unserialize( implode( '', file( $cache_dir . $k . '.cache.' . $this->php_ext ) ) );
                    }
                }          
                break;
            }
            break;
        }
    }
    
    public function update_database_cache( $table, $id )
    {
        switch ( $this->CFG['db_cache'] )
        {
            case true:
            ( $table == 'groups' ) ? $sorting = ' ORDER BY sorting ASC' : $sorting = ' ORDER BY ' . $id . ' ASC';
            
            switch ( $this->CFG['db_cache_method'] )
            {
                case 'dbcache':
                if ( $table == 'forums' )
                {
                    $sql = $this->DB->db_query( $this->SQL->sql_fetch_forums_with_depth() );
                }
                else
                {
                    $this->TOSQL = array( 'table' => $table, 'sorting' => $sorting );
                    $sql         = $this->DB->db_query( $this->SQL->sql_fetch_for_cache() );
                }
                
                while ( $record = $this->DB->db_fetch_array( $sql ) ) $records[] = $record; $this->DB->db_free_result( $sql );
                
                $to_cache = serialize( $records );
                
                $this->TOSQL = array( 'to_cache' => $to_cache, 'title' => $table );
                $this->DB->db_query( $this->SQL->sql_update_cache_data() );
                $this->CACHE[$table] = unserialize( $to_cache );
                break;
                
                case 'filecache':
                $cache_dir = $this->CFG['cache_dir']; $db_cache_dir = $this->CFG['db_cache_dir'];
                
                if ( substr( $cache_dir, ( strlen( $cache_dir ) - 1 ), strlen( $cache_dir ) ) == '/' ) $cache_dir = substr( $cache_dir, 0, ( strlen( $cache_dir ) - 1 ) );
                if ( substr( $cache_dir, 0, 1 ) == '/' ) $cache_dir = substr( $cache_dir, 1, strlen( $cache_dir ) );
                if ( substr( $db_cache_dir, ( strlen( $db_cache_dir ) - 1 ), strlen( $db_cache_dir ) ) == '/' ) $db_cache_dir = substr( $db_cache_dir, 0, ( strlen( $db_cache_dir ) - 1 ) );
                if ( substr( $db_cache_dir, 0, 1 ) == '/' ) $db_cache_dir = substr( $db_cache_dir, 1, strlen( $db_cache_dir ) );
                
                $cache_dir = _ROOT_PATH . $cache_dir . '/' . $db_cache_dir . '/';
                
                if ( ! file_exists( $cache_dir ) )  die( '<h1>Oh Snap! Bulletin Error</h1>The configured database cache directory does not exist.' );
                if ( ! is_readable( $cache_dir ) )  die( '<h1>Oh Snap! Bulletin Error</h1>The configured database cache directory does not have read permissions.' );
                if ( ! is_writeable( $cache_dir ) ) die( '<h1>Oh Snap! Bulletin Error</h1>The configured database cache directory does not have write permissions.' );
                
                ( $table == 'groups' ) ? $sorting = ' ORDER BY sorting ASC' : $sorting = ' ORDER BY ' . $id . ' ASC';
                
                if ( $table == 'forums' )
                {
                    $sql = $this->DB->db_query( $this->SQL->sql_fetch_forums_with_depth() );
                }
                else
                {
                    $this->TOSQL = array( 'table' => $table, 'sorting' => $sorting );
                    $sql         = $this->DB->db_query( $this->SQL->sql_fetch_for_cache() );
                }
                
                while ( $record = $this->DB->db_fetch_array( $sql ) ) $records[] = $record; $this->DB->db_free_result( $sql );
                
                $to_cache = serialize( $records );
                
                if ( $fh = @fopen( $cache_dir . $table . '.cache.' . $this->php_ext, 'w' ) )
                {
                    @fwrite( $fh, $to_cache );
                    @fclose( $fh );
                }
                else
                {
                    die( '<h1>Oh Snap! Bulletin Error<h1>The attempt to write the selected cache file failed.' );
                }
                
                $this->CACHE[$table] = unserialize( $to_cache );
                break;
            }
            break;
        }
    }
    
    public function populate_system_settings()
    {
        $settings = array(); $settings = $this->get_data( 'system_settings', 'setting_id' );
        if ( $settings != false ) foreach ( $settings as $k => $v ) $this->CFG[$v['key']] = $v['value'];
    }
    
    public function get_data( $table, $id = '' )
    {
        switch ( $this->CFG['db_cache'] )
        {
            case true:
            return ( count( $this->CACHE[$table] ) > 0 ) ? $this->CACHE[$table] : false;
            
            break;
            
            case false:
            ( $table == 'groups' ) ? $sorting = ' ORDER BY sorting ASC' : $sorting = ' ORDER BY ' . $id . ' ASC';
            
            if ( $table == 'forums' )
            {
                $sql = $this->DB->db_query( $this->SQL->sql_fetch_forums_with_depth() );
            }
            else
            {
                $this->TOSQL = array( 'table' => $table, 'sorting' => $sorting );
                $sql         = $this->DB->db_query( $this->SQL->sql_fetch_for_cache() );
            }
            
            while ( $record = $this->DB->db_fetch_array( $sql ) ) $records[] = $record; $this->DB->db_free_result( $sql );
            
            $result = serialize( $records );
            $this->CACHE[$table] = unserialize( $result );
            
            return ( count( $this->CACHE[$table ] ) > 0 ) ? $this->CACHE[$table] : false;
            break;
        }
    }
    
    public function setup_urls()
    {
        $application_url = $this->CFG['application_url'];
        
        if ( substr( $application_url, strlen( $application_url ) - 1, strlen( $application_url ) ) == '/' )
        {
            $this->base_url = substr( $application_url, 0, strlen( $application_url ) - 1 );
        }
        else
        {
            $this->base_url = $application_url;
        }
        
        $this->wrapper = $this->base_url . '/' . $this->CFG['application_script_wrapper'] . '.' . $this->php_ext;
    }
    
    public function configure_urls_paths()
    {
        switch ( $this->MEMBER['status'] )
        {
            case false:
            $r = $this->get_data( 'installed_skins', 'skin_id' );
            
            if ( $r != false )
            {
                foreach ( $r as $k => $v ) if ( $v['skin_id'] == $this->CFG['skin_id'] ) $skin_folder = $v['installed_folder']; $skin_imageset = $v['imageset_installed_folder'];
            }
            
            $r = $this->get_data( 'installed_languages', 'language_id' );
            
            if ( $r != false )
            {
                foreach ( $r as $k => $v ) if ( $v['language_id'] == $this->CFG['language_id'] ) $language_folder = $v['installed_folder'];
            }
            
            date_default_timezone_set( $this->CFG['datetime_timezone'] );
            
            $this->skin_path    = _ROOT_PATH . 'skins/' . $skin_folder . '/';
            $this->skin_url     = $this->base_url . '/skins/' . $skin_folder;
            $this->imageset_url = $this->base_url . '/public/imagesets/' . $skin_imageset;
            $this->lang_path    = _ROOT_PATH . 'languages/' . $language_folder . '/';
            break;
        }
    }
    
    public function load_language()
    {
        $LANG = array();
        require( $this->lang_path . 'global.lang.' . $this->php_ext );
        $this->LANG = $LANG;
    }
    
    public function load_errors()
    {
        $ERRORS = array();
        require( $this->lang_path . 'errors.lang.' . $this->php_ext );
        $this->ERRORS = $ERRORS;
    }
    
    public function load_skin()
    {
        require_once( $this->skin_path . 'global.skin.' . $this->php_ext );
        $this->SKIN = new OhSnapSkinGlobal;
        $this->SKIN->SNAP =& $this;
        $this->SKIN->LANG = $this->LANG;
    }
    
    public function seo_url( $a = '', $b = '', $c = '' )
    {
        $url = '';
        
        switch ( $this->CFG['seo_enabled'] )
        {
            case 1:
            ( $a != '' ) ? $url .= $a : '';
            ( $b != '' ) ? $url .= '/' . $b : '';
            ( $c != '' ) ? $url .= '/' . $c : '';
            
            return $this->base_url . '/' . $url;
            break;
            
            case 0:
            ( $a != '' ) ? $url .= '?action=' . $a : '';
            ( $b != '' ) ? $url .= '&amp;do=' . $b : '';
            ( $c != '' ) ? $url .= '&amp;page=' . $c : '';
            
            return $this->wrapper . $url;
            break;
        }
    }
    
    public function lang_replace( $lang, $replace, $replacement ) { return str_replace( '%%' . $replace . '%%', $replacement, $lang); }
    
    public function time_ago( $timestamp )
    {
        $old  = new DateTime( date( 'm/d/Y m:i:s', time() - 3600 ) );
        $diff = $old->diff( new DateTime( date( 'm/d/Y m:i:s', $timestamp - 3600 ) ) );
        $ago  = '';
        
        if ( $diff->d == 0 )
        {
            if ( $diff->s > 0 ) $ago = ( $diff->s > 1 ) ? $diff->s . ' ' . $this->LANG['seconds'] : $diff->s . ' ' . $this->LANG['second'];
            if ( $diff->i > 0 ) $ago = ( $diff->i > 1 ) ? $diff->i . ' ' . $this->LANG['minutes'] : $diff->i . ' ' . $this->LANG['minute'];
            if ( $diff->h > 0 ) $ago = ( $diff->h > 1 ) ? $diff->h . ' ' . $this->LANG['hours'] : $diff->h . ' ' . $this->LANG['hour'];
            return $ago . ' ' . $this->LANG['ago'];
        }
        else
        {
            return false;
        }
    }
    
    public function parse_timestamp( $timestamp, $ago = false, $return_date = false, $return_time = false )
    {
        $time_format = $this->CFG['datetime_time_format']; $date_format = $this->CFG['datetime_date_format'];
        $time_ago    = $this->CFG['datetime_ago'];         $full_format = $date_format . ' ' . $time_format;
        
        if ( $return_date ) return date( $date_format, $timestamp - 3600 );
        if ( $return_time ) return date( $time_format, $timestamp - 3600 );
        
        if ( $ago )
        {
            if ( $time_ago == 1 )
            {
                return ( ! $this->time_ago( $timestamp ) ) ? date( $full_format, $timestamp - 3600 ) : $this->time_ago( $timestamp );
            }
            else
            {
                return date( $full_format, $timestamp - 3600 );
            }
        }
        else
        {
            return date( $full_format, $timestamp - 3600 );
        }
    }
    
    public function url_exist( $url )
    {
        $opts      = array( CURLOPT_URL => $url, CURLOPT_BINARYTRANSFER => true, CURLOPT_HEADERFUNCTION => 'curlHeaderCallback', CURLOPT_FAILONERROR => true );
        $curl_init = curl_init();       
        curl_setopt_array( $curl_init, $opts );
        $curl      = curl_exec( $curl_init );
        $code      = curl_getinfo( $curl_init, CURLINFO_HTTP_CODE );
        curl_close( $curl_init );
        return ( $code != 200 && $code != 302 && $code != 304 ) ? false : true;
    }
    
    public function validate_insertion( $table )
    {
        if ( $this->DB->db_affected_rows() < 1 ) $this->general_error( 'The attempt to insert a new record into the table ' . $table . ' within the database failed.' );
    }
    
    public function general_error( $err )
    {
        echo 'ERROR: ' . $err;
    }
    
    public function check_feature_permissions( $feature )
    {
        switch ( $this->MEMBER['status'] )
        {
            case true:
            $found = false; $r = $this->get_data( 'members', 'member_id' );
            
            if ( $r != false )
            {
                foreach ( $r as $k => $v )
                {
                    if ( $v['member_id'] == $this->MEMBER['id'] )
                    {
                        $found = true; $prm_grp = $v['primary_group_id'];
                        $sc_grp_ids = explode( ',', $v['secondary_group_ids'] );
                    }
                }
            }
            
            if ( ! $found ) return false;
            break;
            
            case false:
            $prm_grp = 6;
            break;
        }
        
        $found = false; $r = $this->get_data( 'feature_permissions', 'feature_id' );
        
        if ( $r != false )
        {
            foreach ( $r as $k => $v )
            {
                if ( strtolower( $v['title'] ) == strtolower( $feature ) )
                {
                    $found = true; $enabled = $v['enabled'];
                    $allowed_users  = explode( ',', $v['allowed_users'] );
                    $allowed_groups = explode( ',', $v['allowed_groups'] );
                }
            }
        }
        
        if ( ! $found ) return false;
        if ( $enabled == 0 ) return false;
        if ( $prm_grp == 1 ) return true;
        
        if ( count( $sc_grp_ids ) > 0 ) foreach ( $sc_grp_ids as $group ) if ( $group == 1 ) return true;
        
        if ( count( $allowed_groups ) > 0 )
        {
            foreach ( $allowed_groups as $group )
            {
                if ( $group != '' )
                {
                    if ( $group == $prm_grp ) return true;
                    
                    if ( count( $sc_grp_ids ) > 0 ) foreach ( $sc_grp_ids as $sgroup ) if ( $sgroup != '' ) if ( $group == $sgroup ) return true;
                }
            }
        }
        
        if ( count( $allowed_users ) > 0 ) foreach ( $allowed_users as $user ) if ( $user != '' ) if ( $user == $this->MEMBER['id'] ) return true;
        
        return false;
    }
    
    public function header( $page_title, $title = '', $nav = '', $redirect = false, $redirect_url = '' )
    {        
        switch ( $this->check_feature_permissions( 'calendar' ) )
        {
            case true:
            $this->T       = array( 'calendar_link' => $this->seo_url( 'calendar' ) );            
            $calendar_link = $this->SKIN->skin_calendar_link();
            break;
            
            case false:
            $calendar_link = '';
            break;
        }
        
        switch ( $this->check_feature_permissions( 'gallery' ) )
        {
            case true:
            $this->T      = array( 'gallery_link' => $this->seo_url( 'gallery' ) );
            $gallery_link = $this->SKIN->skin_gallery_link();
            break;
            
            case false:
            $gallery_link = '';
            break;
        } 
        
        if ( strlen( $title ) > 0 ) $title .= ' - ';
        if ( $redirect ) { $this->T = array( 'url' => $redirect_url ); $meta_refresh = $this->SKIN->skin_redirect_meta(); } else { $meta_refresh = ''; }
        
        $r = $this->get_data( 'installed_skins', 'skin_id' ); $x = 0;
        
        if ( $r != false )
        {
            foreach ( $r as $k => $v )
            {
                if ( $x == 0 ) { $option_class = $this->SKIN->skin_first_option_class(); $x++; } else { $option_class = $this->SKIN->skin_option_class(); }                
                ( $v['skin_id'] == $this->CFG['skin_id'] ) ? $checkmark = $this->SKIN->skin_option_checkmark() : $checkmark = '';
                
                $this->T = array( 'item_class' => $option_class, 'checkmark' => $checkmark, 'id' => $v['skin_id'], 'title' => $v['title'] );
                $skin_options .= $this->SKIN->skin_skin_option();                
            }
        }
        
        $r = $this->get_data( 'installed_languages', 'language_id' ); $x = 0;
        
        if ( $r != false )
        {
            foreach ( $r as $k => $v )
            {
                if ( $x == 0 ) { $option_class = $this->SKIN->skin_first_option_class(); $x++; } else { $option_class = $this->SKIN->skin_option_class(); }
                ( $v['language_id'] == $this->CFG['language_id'] ) ? $checkmark = $this->SKIN->skin_option_checkmark() : $checkmark = '';
                
                $this->T = array( 'item_class' => $option_class, 'checkmark' => $checkmark, 'id' => $v['language_id'], 'title' => $v['title'], 'flag_icon' => $v['flag_icon'] );
                $language_options .= $this->SKIN->skin_language_option();
            }
        }
        
        if ( $nav != '' )
        {
            $this->T  = array( 'forums_link' => $this->seo_url( 'forums' ), 'nav' => $nav );
            $nav_tree = $this->SKIN->skin_navigation_tree();
        }
        else
        {
            $nav_tree = '';
        }
        
        switch ( $this->MEMBER['status'] )
        {
            case false:
            $this->T = array(
                'title'               => $title,
                'skin_options'        => $skin_options,
                'language_options'    => $language_options,
                'create_account_link' => $this->seo_url( 'createaccount' ),
                'lostpw_link'         => $this->seo_url( 'authenticate', 'lostpw' ),
                'forums_link'         => $this->seo_url( 'forums' ),
                'signin_link'         => $this->seo_url( 'authenticate' ),
                'members_link'        => $this->seo_url( 'members', 'list' ),
                'search_link'         => $this->seo_url( 'search' ),
                'help_link'           => $this->seo_url( 'help' ),
                'calendar_link'       => $calendar_link,
                'gallery_link'        => $gallery_link,
                'nav'                 => $nav_tree,
                'page_title'          => $page_title
            );
            
            echo $this->SKIN->skin_guest_header();
            break;
            
            case true:
            $r = $this->get_data( 'notifications', 'notification_id' ); $total = 0;
            
            if ( $r != false )
            {
                foreach ( $r as $k => $v )
                {
                    if ( ( $v['member_id'] == $this->MEMBER['id'] ) AND ( $v['read'] == 0 ) ) $total++;
                }
            }
            
            if ( $total > 0 )
            {
                array_reverse( $r ); $count = 0;
                
                foreach  ( $r as $k => $v )
                {
                    if ( ( $v['member_id'] == $this->MEMBER['id'] ) AND ( $v['read'] == 0 ) )
                    {
                        if ( $count <= $this->CFG['notification_limit'] )
                        {
                            switch ( $type )
                            {
                                case 'pm':
                                $row = $this->get_data( 'pm_messages', 'message_id' );
                                
                                if ( $row != false )
                                {
                                    foreach ( $row as $key => $val )
                                    {
                                        if ( $val['message_id'] == $v['linked_id'] ) $senders_id = $val['sender_member_id']; $linked_id = $v['linked_id'];
                                    }
                                }
                                
                                $row = $this->get_data( 'members', 'member_id' );
                                
                                if ( $row != false )
                                {
                                    foreach ( $row as $key => $val )
                                    {
                                        if ( $val['member_id'] == $senders_id ) $senders_name = $val['display_name'];
                                    }
                                }
                                
                                $this->T = array(
                                    'notification_link' => $this->seo_url( 'messenger', 'view', $linked_id ),
                                    'title'             => $this->lang_replace( $this->LANG['n_new_pm'], 'sender', $senders_name )
                                );
                                
                                $notifications .= $this->SKIN->skin_notification_item();
                                break;
                            }
                            
                            $count++;
                        }
                    }
                }
                
                $notification_icon = $this->SKIN->skin_notification_icon_new();
            }
            else
            {
                $this->T       = array( 'notification_link' => $this->seo_url( 'notifications' ) ); $notifications = $this->SKIN->skin_notification_item_none();                
                $notification_icon = $this->SKIN->skin_notification_icon();
            }
            
            $r = $this->get_data( 'pm_inbox', 'inbox_id' ); $total_pms = 0;
            
            if ( $r != false )
            {
                foreach ( $r as $k => $v )
                {
                    if ( ( $v['member_id'] == $this->MEMBER['id'] ) AND ( $v['read'] == 0 ) ) $total_pms++;
                }
            }
            
            $avatar = $this->get_member_avatar( $this->MEMBER['id'], true );
            
            $this->T = array(
                'title'               => $title,
                'skin_options'        => $skin_options,
                'language_options'    => $language_options,
                'forums_link'         => $this->seo_url( 'forums' ),
                'members_link'        => $this->seo_url( 'members', 'list' ),
                'search_link'         => $this->seo_url( 'search' ),
                'help_link'           => $this->seo_url( 'help' ),
                'calendar_link'       => $calendar_link,
                'gallery_link'        => $gallery_link,
                'nav'                 => $nav_tree,
                'page_title'          => $page_title,
                'notifications'       => $notifications,
                'notification_icon'   => $notification_icon,
                'notifications_link'  => $this->seo_url( 'notifications' ),
                'avatar'              => $avatar,
                'lang_pm'             => $this->lang_replace( $this->LANG['private_messages'], 'total', number_format( $total_pms ) ),
                'lang_pm_info'        => $this->lang_replace( $this->LANG['private_message_info'], 'total', number_format( $total_pms ) ),
                'view_profile_link'   => $this->seo_url( 'members', str_replace( ' ', '_', $this->MEMBER['display_name'] ) . '_' . $this->MEMBER['id'] ),
                'settings_link'       => $this->seo_url( 'settings' ),
                'messenger_link'      => $this->seo_url( 'messenger' ),
                'following_link'      => $this->seo_url( 'following' ),
                'signout_link'        => $this->seo_url( 'authenticate', 'signout' )
            );
            
            echo $this->SKIN->skin_member_header();
            break;
        }
        
        if ( $redirect ) $this->redirection( $redirect_url );
    }
    
    public function redirection()
    {
        
    }
    
    public function get_member_link( $member_id, $title = '', $seperator = '' )
    {
        $r = $this->get_data( 'members', 'member_id' ); $found = false;
        
        if ( $r != false )
        {
            foreach ( $r as $k => $v )
            {
                if ( $v['member_id'] == $member_id ) $found = true; $display_name = $v['display_name'];
            }
        }
        
        switch ( $found )
        {
            case false:
            return $this->LANG['unknown'];
            break;
            
            case true:
            if ( $title == '' ) $title = $this->lang_replace( $this->LANG['view_profile'], 'member_name', $display_name );
            
            $this->T = array(
                'seperator'    => $seperator,
                'title'        => $title,
                'display_name' => $display_name,
                'member_link'  => $this->seo_url( 'members', str_replace( ' ', '_', $display_name ) . '_' . $member_id )
            );
            
            return $this->SKIN->skin_member_link();
            break;
        }
    }
    
    public function get_member_avatar( $member_id, $thumb = false )
    {
        $no_photo   = $this->imageset_url . '/images/' . $this->CFG['no_photo_img'];
        $upload_dir = $this->CFG['upload_dir']; $avatar_dir = $this->CFG['avatar_dir'];
        
        if ( substr( $upload_dir, ( strlen( $upload_dir ) - 1 ), strlen( $upload_dir ) ) == '/' ) $upload_dir = substr( $upload_dir, 0, ( strlen( $upload_dir ) - 1 ) );
        if ( substr( $upload_dir, 0, 1 ) == '/' ) $upload_dir = substr( $upload_dir, 1, strlen( $upload_dir ) );
        if ( substr( $avatar_dir, ( strlen( $avatar_dir ) - 1 ), strlen( $avatar_dir ) ) == '/' ) $avatar_dir = substr( $avatar_dir, 0, ( strlen( $avatar_dir ) - 1 ) );
        if ( substr( $avatar_dir, 0, 1 ) == '/' ) $avatar_dir = substr( $avatar_dir, 1, strlen( $avatar_dir ) );
        
        $avatar_path = _ROOT_PATH . $upload_dir . '/' . $avatar_dir . '/'; $avatar_url  = $this->base_url . '/' . $upload_dir . '/' . $avatar_dir . '/';
        
        if ( $member_id < 1 )
        {
            switch ( $thumb )
            {
                case true:
                if ( $this->CFG['avatar_thumb_max_width'] == 0 )
                {
                    return $this->get_avatar_thumbnail( $no_photo );
                }
                else
                {
                    return $this->get_avatar_thumbnail( $no_photo, $this->calculate_width( $no_photo, true ) );
                }
                break;
                
                case false:
                if ( $this->CFG['avatar_max_width'] == 0 )
                {
                    return $this->get_avatar_normal( $no_photo );
                }
                else
                {
                    return $this->get_avatar_normal( $no_photo, $this->calculate_width( $no_photo ) );
                }
                break;
            }
        }
        
        $r = $this->get_data( 'members', 'member_id' ); $found = false;
        
        if ( $r != false )
        {
            foreach ( $r as $k => $v )
            {
                if ( $v['member_id'] == $member_id )
                {
                    $found = true;              $enabled = $v['avatar_enabled'];
                    $type  = $v['avatar_type']; $location = $v['avatar_location'];
                    $id    = $v['avatar_id'];   $fb_id = $v['facebook_oauth_id'];
                }
            }
        }
        
        switch ( $found )
        {
            case false:
            switch ( $thumb )
            {
                case true:
                if ( $this->CFG['avatar_thumb_max_width'] == 0 )
                {
                    return $this->get_avatar_thumbnail( $no_photo );
                }
                else
                {
                    return $this->get_avatar_thumbnail( $no_photo, $this->calculate_width( $no_photo, true ) );
                }
                break;
                
                case false:
                if ( $this->CFG['avatar_max_width'] == 0 )
                {
                    return $this->get_avatar_normal( $no_photo );
                }
                else
                {
                    return $this->get_avatar_normal( $no_photo, $this->calculate_width( $no_photo ) );
                }
                break;
            }
            break;
        }
        
        switch ( $enabled )
        {
            case 0:
            switch ( $thumb )
            {
                case true:
                if ( $this->CFG['avatar_thumb_max_width'] == 0 )
                {
                    return $this->get_avatar_thumbnail( $no_photo );
                }
                else
                {
                    return $this->get_avatar_thumbnail( $no_photo, $this->calculate_width( $no_photo, true ) );
                }
                break;
                
                case false:
                if ( $this->CFG['avatar_max_width'] == 0 )
                {
                    return $this->get_avatar_normal( $no_photo );
                }
                else
                {
                    return $this->get_avatar_normal( $no_photo, $this->calculate_width( $no_photo ) );
                }
                break;
            }
            break;
        }
        
        switch ( $type )
        {
            case 'uploaded':
            $r = $this->get_data( 'avatars', 'avatar_id' ); $found = false;
            
            if ( $r != false )
            {
                foreach ( $r as $k => $v )
                {
                    if ( ( $v['avatar_id'] == $id ) AND ( $v['member_id'] == $member_id ) )
                    {
                        $found = true; $filename = $v['filename'];
                    }
                }
            }
            
            switch ( $found )
            {
                case false:
                switch ( $thumb )
                {
                    case true:
                    if ( $this->CFG['avatar_thumb_max_width'] == 0 )
                    {
                        return $this->get_avatar_thumbnail( $no_photo );
                    }
                    else
                    {
                        return $this->get_avatar_thumbnail( $no_photo, $this->calculate_width( $no_photo, true ) );
                    }
                    break;
                    
                    case false:
                    if ( $this->CFG['avatar_max_width'] == 0 )
                    {
                        return $this->get_avatar_normal( $no_photo );
                    }
                    else
                    {
                        return $this->get_avatar_normal( $no_photo, $this->calculate_width( $no_photo ) );
                    }
                    break;
                }
                break;
            }
            
            if ( file_exists( $avatar_path . $filename ) )
            {
                switch ( $thumb )
                {
                    case true:
                    if ( $this->CFG['avatar_thumb_max_width'] == 0 )
                    {
                        return $this->get_avatar_thumbnail( $avatar_url . $filename );
                    }
                    else
                    {
                        return $this->get_avatar_thumbnail( $avatar_url . $filename, $this->calculate_width( $avatar_url . $filename, true ) );
                    }
                    break;
                    
                    case false:
                    if ( $this->CFG['avatar_max_width'] == 0 )
                    {
                        return $this->get_avatar_normal( $avatar_url . $filename );
                    }
                    else
                    {
                        return $this->get_avatar_normal( $avatar_url . $filename, $this->calculate_width( $avatar_url . $filename ) );
                    }
                    break;
                }
            }
            else
            {
                switch ( $thumb )
                {
                    case true:
                    if ( $this->CFG['avatar_thumb_max_width'] == 0 )
                    {
                        return $this->get_avatar_thumbnail( $no_photo );
                    }
                    else
                    {
                        return $this->get_avatar_thumbnail( $no_photo, $this->calculate_width( $no_photo, true ) );
                    }
                    break;
                    
                    case false:
                    if ( $this->CFG['avatar_max_width'] == 0 )
                    {
                        return $this->get_avatar_normal( $no_photo );
                    }
                    else
                    {
                        return $this->get_avatar_normal( $no_photo, $this->calculate_width( $no_photo ) );
                    }
                    break;
                }
            }
            break;
            
            case 'facebook':
            if ( $fb_id != 0 )
            {
                switch ( $thumb )
                {
                    case true:
                    if ( $this->CFG['avatar_thumb_max_width'] == 0 )
                    {
                        $this->T = array( 'fb_id' => $fb_id );
                        return $this->SKIN->skin_avatar_facebook_no_limits();
                    }
                    else
                    {
                        $this->T = array( 'fb_id' => $fb_id, 'width' => $this->CFG['avatar_thumb_max_width'] );
                        return $this->SKIN->skin_avatar_facebook_limits();
                    }
                    break;
                    
                    case false:
                    if ( $this->CFG['avatar_max_width'] == 0 )
                    {
                        $this->T = array( 'fb_id' => $fb_id );
                        return $this->SKIN->skin_avatar_facebook_no_limits();
                    }
                    else
                    {
                        $this->T = array( 'fb_id' => $fb_id, 'width' => $this->CFG['avatar_max_width'] );
                        return $this->SKIN->skin_avatar_facebook_limits();
                    }
                    break;
                }
            }
            else
            {
                switch ( $thumb )
                {
                    case true:
                    if ( $this->CFG['avatar_thumb_max_width'] == 0 )
                    {
                        return $this->get_avatar_thumbnail( $no_photo );
                    }
                    else
                    {
                        return $this->get_avatar_thumbnail( $no_photo, $this->calculate_width( $no_photo, true ) );
                    }
                    break;
                    
                    case false:
                    if ( $this->CFG['avatar_max_width'] == 0 )
                    {
                        return $this->get_avatar_normal( $no_photo );
                    }
                    else
                    {
                        return $this->get_avatar_normal( $no_photo, $this->calculate_width( $no_photo ) );
                    }
                    break;
                }
            }
            break;
            
            case 'linked':
            $fp = @fopen( $location, 'r' );
            
            if ( $fp )
            {
                switch ( $thumb )
                {
                    case true:
                    if ( $this->CFG['avatar_thumb_max_width'] == 0 )
                    {
                        return $this->get_avatar_thumbnail( $location );
                    }
                    else
                    {
                        return $this->get_avatar_thumbnail( $location, $this->calculate_width( $location, true ) );
                    }
                    break;
                    
                    case false:
                    if ( $this->CFG['avatar_max_width'] == 0 )
                    {
                        return $this->get_avatar_normal( $location );
                    }
                    else
                    {
                        return $this->get_avatar_normal( $location, $this->calculate_width( $location ) );
                    }
                    break;
                }
                
                @fclose( $fp );
            }
            else
            {
                switch ( $thumb )
                {
                    case true:
                    if ( $this->CFG['avatar_thumb_max_width'] == 0 )
                    {
                        return $this->get_avatar_thumbnail( $no_photo );
                    }
                    else
                    {
                        return $this->get_avatar_thumbnail( $no_photo, $this->calculate_width( $no_photo, true ) );
                    }
                    break;
                    
                    case false:
                    if ( $this->CFG['avatar_max_width'] == 0 )
                    {
                        return $this->get_avatar_normal( $no_photo );
                    }
                    else
                    {
                        return $this->get_avatar_normal( $no_photo, $this->calculate_width( $no_photo ) );
                    }
                    break;
                }
            }
            break;
            
            case 'gallery':
            if ( $this->url_exist( $location ) )
            {
                switch ( $thumb )
                {
                    case true:
                    if ( $this->CFG['avatar_thumb_max_width'] == 0 )
                    {
                        return $this->get_avatar_thumbnail( $location );
                    }
                    else
                    {
                        return $this->get_avatar_thumbnail( $location, $this->calculate_width( $location, true ) );
                    }
                    break;
                    
                    case false:
                    if ( $this->CFG['avatar_max_width'] == 0 )
                    {
                        return $this->get_avatar_normal( $location );
                    }
                    else
                    {
                        return $this->get_avatar_normal( $location, $this->calculate_width( $location ) );
                    }
                    break;
                }
            }
            else
            {
                switch ( $thumb )
                {
                    case true:
                    if ( $this->CFG['avatar_thumb_max_width'] == 0 )
                    {
                        return $this->get_avatar_thumbnail( $no_photo );
                    }
                    else
                    {
                        return $this->get_avatar_thumbnail( $no_photo, $this->calculate_width( $no_photo, true ) );
                    }
                    break;
                    
                    case false:
                    if ( $this->CFG['avatar_max_width'] == 0 )
                    {
                        return $this->get_avatar_normal( $no_photo );
                    }
                    else
                    {
                        return $this->get_avatar_normal( $no_photo, $this->calculate_width( $no_photo ) );
                    }
                    break;
                }
            }
            break;
        }
    }
    
    public function get_avatar_thumbnail( $filename, $dimensions = array() )
    {
        if ( empty( $dimensions ) )
        {
            $this->T = array( 'filename' => $filename );
            return $this->SKIN->skin_avatar_thumb_no_limits();
        }
        else
        {
            $this->T = array( 'filename' => $filename, 'width' => $dimensions['width'], 'height' => $dimensions['height'] );
            return $this->SKIN->skin_avatar_thumb_limits();
        }
    }
    
    public function get_avatar_normal( $filename, $dimensions = array() )
    {
        if ( empty( $dimensions ) )
        {
            $this->T = array( 'filename' => $filename );
            return $this->SKIN->skin_avatar_no_limits();
        }
        else
        {
            $this->T = array( 'filename' => $filename, 'width' => $dimensions['width'], 'height' => $dimensions['height'] );
            return $this->SKIN->skin_avatar_limits();
        }
    }
    
    public function calculate_width( $filename, $thumb = false )
    {
        list( $width, $height ) = getimagesize( $filename );
        ( $thumb ) ? $max = $this->CFG['avatar_thumb_max_width'] : $max = $this->CFG['avatar_max_width'];
        
        if ( $width > $max ) $height = ( $height / $width ) * $max; $width = $max;
        return array( 'width' => $width, 'height' => round( $height ) );
    }
    
    public function check_for_unread( $method, $forum = '', $topic = '' )
    {
        switch ( $method )
        {
            case 'forum':
            $r = $this->get_data( 'forums_read', 'read_id' ); $unread = true;
            
            if ( $r != false )
            {
                foreach ( $r as $k => $v )
                {
                    if ( ( $v['forum_id'] == $forum ) AND ( $v['member_id'] == $this->MEMBER['id'] ) )
                    {
                        $row = $this->get_data( 'topics', 'topic_id' );
                        
                        if ( $row != false )
                        {
                            foreach ( $row as $key => $val ) if ( $val['topic_id'] == $v['topic_id'] ) $last_post = $val['topic_lastpost_timestamp'];
                        }
                        
                        if ( $last_post <= $v['last_read_timestamp'] ) $unread = false;
                    }
                }
            }
            
            return $unread;
            break;
            
            case 'topic':
            $r = $this->get_data( 'forums_read', 'read_id' ); $unread = true;
            
            if ( $r != false )
            {
                foreach ( $r as $k => $v )
                {
                    if ( ( $v['topic_id'] == $topic ) AND ( $v['member_id'] == $this->MEMBER['id'] ) )
                    {
                        $row = $this->get_data( 'topics', 'topic_id' );
                        
                        if ( $row != false )
                        {
                            foreach ( $row as $key => $val ) if ( $val['topic_id'] == $topic ) $last_post = $val['topic_lastpost_timestamp'];
                        }
                        
                        if ( $last_post <= $v['last_read_timestamp'] ) $unread = false;
                    }
                }
            }
            
            return $unread;
            break;
        }
    }
    
    public function calculate_age( $m, $d, $y )
    {
        $age_time = mktime( 0, 0, 0, $m, $d, $y );
        $age      = ( $age_time < 0 ) ? ( time() + ( $age_time * -1 ) ) : time() - $age_time;
        $yr       = 60 * 60 * 24 * 365;
        
        return floor( $age / $yr );
    }
    
    public function record_visits()
    {
        $exist = false;
        
        $r = $this->get_data( 'visits', 'visit_id' );
        
        if ( $r != false )
        {
            foreach ( $r as $k => $v )
            {
                if ( ( date( 'm/d/Y', time() - 3600 ) == date( 'm/d/Y', $v['timestamp'] - 3600 ) ) AND ( $v['member_id'] == $this->MEMBER['id'] ) )
                {
                    $exist = true;
                }
            }
        }
        
        if ( ! $exist )
        {
            $this->TOSQL = array( 'member_id' => $this->MEMBER['id'], 'timestamp' => time() );
            
            $this->DB->db_query( $this->SQL->sql_insert_new_visit() );
            
            $this->validate_db_insert( 'ERRORHERE' );
            
            $this->update_database_cache( 'visits', 'visit_id' );
        }
    }
    
    public function validate_db_insert( $msg )
    {
        if ( $this->DB->db_affected_rows() < 1 )
        {
            $this->general_error( $msg );
        }
    }
    
    public function footer()
    {
        switch ( $this->CFG['show_debug_information'] )
        {
            case 1:
            ( $this->CFG['gzip_compression'] == 1 ) ? $gzip = $this->LANG['enabled'] : $gzip = $this->LANG['disabled'];
            
            $this->stop_execution_timer();
            
            $this->SKIN->LANG['page_processed'] = $this->lang_replace( $this->SKIN->LANG['page_processed'], 'seconds', $this->TIMER['end'] );
            $this->SKIN->LANG['sql_queries']    = $this->lang_replace( $this->SKIN->LANG['sql_queries'], 'queries', number_format( $this->DB->db_queries ) );
            $this->SKIN->LANG['gzip']           = $this->lang_replace( $this->SKIN->LANG['gzip'], 'status', $gzip );
            
            $debug_information = $this->SKIN->skin_debug_information();
            break;
            
            case 0:
            $debug_information = '';
            break;
        }
        
        $this->SKIN->LANG['all_times'] = $this->lang_replace( $this->SKIN->LANG['all_times'], 'timezone', $this->CFG['datetime_timezone'] );
        
        $this->T = array(
            'mark_all_link'     => $this->seo_url( 'forums', 'markall' ),
            'new_content_link'  => $this->seo_url( 'search', 'newcontent' ),
            'leaders_link'      => $this->seo_url( 'groups', 'leaders' ),
            'rules_link'        => $this->seo_url( 'forums', 'rules' ),
            'privacy_link'      => $this->seo_url( 'forums', 'privacy' ),
            'debug_information' => $debug_information
        ); 
        
        echo $this->SKIN->skin_footer();
    }
}

?>