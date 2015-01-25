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

if ( ! defined ( 'SNAP_INIT' ) )
{
    header( 'HTTP/1.0 403 Forbidden' );
    die();
}

class OHSNAPClass {
    
    public $snap_version = SNAP_VERSION;
    public $php_ext      = PHP_EXT;
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
    public $TWITTER      = array();
    public $MOBILE       = array();
    public $base_url     = array();
    public $script_url   = array();
    public $imageset_url = array();
    public $theme_url    = '';
    public $theme_path   = '';
    public $lang_path    = '';
    public $db_prefix    = 'ohsnap_';
    
    public function __construct()
    {
        $this->start_execution_timer();
        $this->fix_iis_uri();
        
        $this->AGENT['ip'] = $this->fetch_server_var( 'REMOTE_ADDR' );
        $this->validate_ip_address( $this->AGENT['ip'] );
        $this->AGENT['hostname'] = gethostbyaddr( $this->AGENT['ip'] );
        $this->AGENT['agent']    = $this->fetch_server_var( 'HTTP_USER_AGENT' );
        
        $this->browser_detection( $this->AGENT['agent'] );
        $this->search_bot_detection( $this->AGENT['agent'] );
        $this->mobile_browser_detection();
    }
    
    public function system_init()
    {
        $CFG = array();
        require_once ( ROOT_PATH . 'config.inc.' . $this->php_ext );
        $this->CFG = $CFG;
        
        $this->filter_incoming_data();
        $this->initialize_database();
        $this->initialize_core_classes();
        $this->initialize_database_cache();
        $this->populate_system_settings();
        $this->start_gzip_compression();
        $this->setup_urls();
        
        $this->SESSIONS->session_gc();
        $this->SESSIONS->manage_sessions();
        
        if ( isset( $_SESSION['ohsnap_username'] ) )
        {
            $this->MEMBER['status']   = true;
            $this->MEMBER['username'] = $_SESSION['ohsnap_username'];
        }
        else
        {
            $this->MEMBER['status']   = false;
            $this->MEMBER['username'] = 'Guest';
        }
        
        $this->configure_urls_paths();
        $this->load_language();
        $this->load_errors();
        $this->load_theme();
        
        if ( isset( $this->INC['action'] ) )
        {
            $action = strtolower( $this->INC['action'] );
        }
        else
        {
            $action = 'index';
        }
        
        if ( file_exists( ROOT_PATH . 'system/classes/' . $action . '.class.' . $this->php_ext ) )
        {
            require_once( ROOT_PATH . 'system/classes/' . $action . '.class.' . $this->php_ext );
            
            $init = new $action;
            $init->SNAP =& $this;
            $init->class_init();
        }
        else
        {
            require_once( ROOT_PATH . 'system/classes/index.class.' . $this->php_ext );
            
            $init = new index;
            $init->SNAP =& $this;
            $init->class_init();
        }
        
        session_write_close();
        
        $this->DB->db_disconnect();
    }
     
    public function fetch_server_var( $v )
    {
        return trim( $_SERVER[$v] );
    }
    
    public function fetch_env_var( $v )
    {
        return trim( $_ENV[$v] );
    }
    
    public function fix_iis_uri()
    {
        if ( ! isset( $_SERVER['REQUEST_URI'] ) )
        {
            $_SERVER['REQUEST_URI'] = substr( $_SERVER['PHP_SELF'], 1 );
            if ( isset( $_SERVER['QUERY_STRING'] ) ) { $_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING']; }
        }
    }
    
    public function start_execution_timer()
    {
        $micro_time           = microtime();
        $micro_time           = explode( ' ', $micro_time );
        $this->TIMER['start'] = $micro_time[1] + $micro_time[0];
    }
    
    public function stop_execution_timer()
    {
        $micro_time  = microtime();
        $micro_time  = explode( ' ', $micro_time );
        $micro_time  = $micro_time[1] + $micro_time[0];
        $micro_time  = ( $micro_time - $this->TIMER['start'] );
        $this->TIMER = round( $micro_time, 2 );
    }
    
    public function validate_ip_address( $ip )
    {
        if ( preg_match( "/^((1?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(1?\d{1,2}|2[0-4]\d|25[0-5])$/", $ip ) )
        {
            $octs = explode( '.', $ip );
            
            foreach ( $octs as $octet )
            {
                if ( ( intval( $octet ) > 255 ) OR ( intval( $octet ) < 0 ) )
                {
                    $valid = false;
                }
            }
            
            $valid = true;
        }
        else
        {
            $valid = false;
        }
        
        switch ( $valid )
        {
            case false:
            echo '<h1>Oh Snap! Bulletin Error</h1>We are unable to determine your IP address. In order to access our site, you must have a valid IP address.';
            exit();
            break;
        }
    }
    
    public function browser_detection( $agent )
    {
        $browser_title = '';
        $agent         = strtolower( $agent );
        
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
                           
        $found = false;
        
        foreach ( $browsers as $k => $v )
        {
            if ( preg_match( "/$v/i", $agent ) )
            {
                $browser_title = $k;
                break;
            }
        }
        
        $this->AGENT['browser_title'] = $browser_title;
    }
    
    public function search_bot_detection( $agent )
    {
        $search_bots = array( 'bingbot', 'msn', 'abacho', 'abcdatos', 'abcsearch', 'acoon', 'adsarobot', 'aesop', 'ah-ha',
                              'alkalinebot', 'almaden', 'altavista', 'antibot', 'anzwerscrawl', 'aol', 'search', 'appie', 'arachnoidea',
                              'araneo', 'architext', 'ariadne', 'arianna', 'ask', 'jeeves', 'aspseek', 'asterias', 'astraspider', 'atomz',
                              'augurfind', 'backrub', 'baiduspider', 'bannana_bot', 'bbot', 'bdcindexer', 'blindekuh', 'boitho', 'boito',
                              'borg-bot', 'bsdseek', 'christcrawler', 'computer_and_automation_research_institute_crawler', 'coolbot',
                              'cosmos', 'crawler', 'crawler@fast', 'crawlerboy', 'cruiser', 'cusco', 'cyveillance', 'deepindex', 'denmex',
                              'dittospyder', 'docomo', 'dogpile', 'dtsearch', 'elfinbot', 'entire', 'esismartspider', 'exalead',
                              'excite', 'ezresult', 'fast', 'fast-webcrawler', 'fdse', 'felix', 'fido', 'findwhat', 'finnish', 'firefly',
                              'firstgov', 'fluffy', 'freecrawl', 'frooglebot', 'galaxy', 'gaisbot', 'geckobot', 'gencrawler', 'geobot',
                              'gigabot', 'girafa', 'goclick', 'goliat', 'googlebot', 'griffon', 'gromit', 'grub-client', 'gulliver',
                              'gulper', 'henrythemiragorobot', 'hometown', 'hotbot', 'htdig', 'hubater', 'ia_archiver', 'ibm_planetwide',
                              'iitrovatore-setaccio', 'incywincy', 'incrawler', 'indy', 'infonavirobot', 'infoseek', 'ingrid', 'inspectorwww',
                              'intelliseek', 'internetseer', 'ip3000.com-crawler', 'iron33', 'jcrawler', 'jeeves', 'jubii', 'kanoodle',
                              'kapito', 'kit_fireball', 'kit-fireball', 'ko_yappo_robot', 'kototoi', 'lachesis', 'larbin', 'legs',
                              'linkwalker', 'lnspiderguy', 'look.com', 'lycos', 'mantraagent', 'markwatch', 'maxbot', 'mercator', 'merzscope',
                              'meshexplorer', 'metacrawler', 'mirago', 'mnogosearch', 'moget', 'motor', 'muscatferret', 'nameprotect',
                              'nationaldirectory', 'naverrobot', 'nazilla', 'ncsa', 'beta', 'netnose', 'netresearchserver', 'ng/1.0',
                              'northerlights', 'npbot', 'nttdirectory_robot', 'nutchorg', 'nzexplorer', 'odp', 'openbot', 'openfind',
                              'osis-project', 'overture', 'perlcrawler', 'phpdig', 'pjspide', 'polybot', 'pompos', 'poppi', 'portalb',
                              'psbot', 'quepasacreep', 'rabot', 'raven', 'rhcs', 'robi', 'robocrawl', 'robozilla', 'roverbot', 'scooter',
                              'scrubby', 'search.ch', 'search.com.ua', 'searchfeed', 'searchspider', 'searchuk', 'seventwentyfour',
                              'sidewinder', 'sightquestbot', 'skymob', 'sleek', 'slider_search', 'slurp', 'solbot', 'speedfind', 'speedy',
                              'spida', 'spider_monkey', 'spiderku', 'stackrambler', 'steeler', 'suchbot', 'suchknecht.at-robot', 'suntek',
                              'szukacz', 'surferf3', 'surfnomore', 'surveybot', 'suzuran', 'synobot', 'tarantula', 'teomaagent', 'teradex',
                              't-h-u-n-d-e-r-s-t-o-n-e', 'tigersuche', 'topiclink', 'toutatis', 'tracerlock', 'turnitinbot', 'tutorgig',
                              'uaportal', 'uasearch.kiev.ua', 'uksearcher', 'ultraseek', 'unitek', 'vagabondo', 'verygoodsearch', 'vivisimo',
                              'voilabot', 'voyager', 'vscooter', 'w3index', 'w3c_validator', 'wapspider', 'wdg_validator', 'webcrawler',
                              'webmasterresourcesdirectory', 'webmoose', 'websearchbench', 'webspinne', 'whatuseek', 'whizbanglab', 'winona',
                              'wire', 'wotbox', 'wscbot', 'www.webwombat.com.au', 'xenu', 'link', 'sleuth', 'xyro', 'yahoobot', 'yahoo!',
                              'slurp', 'yandex', 'yellopet-spider', 'zao/0', 'zealbot', 'zippy', 'zyborg', 'mediapartners-google' );
                              
        for ( $i = 0; $i < count( $search_bots ); $i++ )
        {
            if ( strpos( ' ' . strtolower( $agent ), strtolower( $search_bots[$i] ) ) != false )
            {
                $bot_name = $search_bots[$i];
            }
        }
        
        if ( isset( $bot_name ) )
        {
            $this->SESSION['search_bot']      = true;
            $this->SESSION['search_bot_name'] = $bot_name;
        }
        else
        {
            $this->SESSION['search_bot']      = false;
            $this->SESSION['search_bot_name'] = '';
        }
    }
    
    public function mobile_browser_detection()
    {
        $is_mobile = false;
        
        $mobile_browsers = explode( ',', $this->CFG['mobile_browsers'] );
        
        foreach ( $mobile_browsers as $v )
        {
            if ( $this->AGENT['browser_title'] == $v )
            {
                $is_mobile = true;
            }
        }
        
        $this->MOBILE['is_mobile'] = $is_mobile;
    }
    
    public function filter_incoming_data()
    {
        foreach ( $_GET as $k => $v )
        {
            $this->INC[$k] = filter_var( $v, FILTER_SANITIZE_STRING );
        }
        
        foreach ( $_POST as $k => $v )
        {
            $this->INC[$k] = filter_var( $v, FILTER_SANITIZE_STRING );
        }
    }
    
    public function start_gzip_compression()
    {
        switch ( $this->CFG['gzip_compression'] )
        {
            case true:
            ob_start( 'ob_gzhandler' );
            break;
        }
    }
    
    public function initialize_core_classes()
    {
        require_once( ROOT_PATH . 'system/core_classes/sqlqueries.class.' . $this->php_ext );
        require_once( ROOT_PATH . 'system/core_classes/sessions.class.' . $this->php_ext );
        
        $this->SQL            = new OHSNAPSQL;
        $this->SQL->SNAP      =& $this;
        $this->SESSIONS       = new OHSNAPSessions;
        $this->SESSIONS->SNAP =& $this;
    }
    
    public function initialize_database()
    {
        if ( ! file_exists( ROOT_PATH . 'system/db_drivers/' . $this->CFG['db_driver'] . '.driver.class.' . $this->php_ext ) )
        {
            die( '<h1>Oh Snap! Bulletin Error</h1>The selected database driver does not exist in the database drivers.' );
        }
        else
        {
            require_once( ROOT_PATH . 'system/db_drivers/' . $this->CFG['db_driver'] . '.driver.class.' . $this->php_ext );
        }
        
        $this->DB = new OHSNAPDatabase;
        $this->DB->SNAP =& $this;
        $this->DB->set_hostname( $this->CFG['db_hostname'] );
        $this->DB->set_port( $this->CFG['db_port'] );
        $this->DB->set_database( $this->CFG['db_database'] );
        $this->DB->set_username( $this->CFG['db_username'] );
        $this->DB->set_password( $this->CFG['db_password'] );
        $this->DB->set_persistant( $this->CFG['db_persistant'] );
        $this->DB->set_log_errors( $this->CFG['db_log_errors'] );
        $this->DB->db_establish_connection();
        
        if ( $this->CFG['db_prefix'] != '' )
        {
            $this->db_prefix = $this->CFG['db_prefix'];
        }
    }
    
    public function initialize_database_cache()
    {
        switch ( $this->CFG['db_cache'] )
        {
            case true:
            $cache_list = array( 'members'             => 'member_id',
                                 'installed_themes'    => 'theme_id',
                                 'installed_languages' => 'language_id',
                                 'forums'              => 'forum_id',
                                 'system_settings'     => 'setting_id' );
                                 
            switch ( $this->CFG['db_cache_method'] )
            {
                case 'dbcache':
                $sql = $this->DB->db_query( $this->SQL->sql_fetch_stored_cache() );
                
                while ( $r = $this->DB->db_fetch_array( $sql ) )
                {
                    unset( $records );
                    
                    foreach ( $cache_list as $k => $v )
                    {
                        if ( $k == 'groups' )
                        {
                            $sorting = " ORDER BY sorting ASC";
                        }
                        else
                        {
                            $sorting = " ORDER BY " . $v . " ASC";
                        }
                        
                        if ( $r['title'] == $k )
                        {
                            if ( $r['data'] != '' )
                            {
                                $this->CACHE[$k] = unserialize( $r['data'] );
                            }
                            else
                            {
                                if ( $k == 'forums' )
                                {
                                    $sqll = $this->DB->db_query( $this->SQL->sql_fetch_forums_with_depth() );
                                }
                                else
                                {
                                    $this->TOSQL = array( 'table'   => $k,
                                                          'sorting' => $sorting );
                                                          
                                    $sqll = $this->DB->db_query( $this->SQL->sql_fetch_for_cache() );
                                }
                                
                                while ( $record = $this->DB->db_fetch_array( $sqll ) )
                                {
                                    $records[] = $record;
                                }
                                
                                $this->DB->db_free_result( $sqll );
                                
                                $to_cache = serialize( $records );
                                
                                $this->TOSQL = array( 'to_cache' => $to_cache,
                                                      'title'    => $k );
                                                      
                                $this->DB->db_query( $this->SQL->sql_update_cache_data() );
                                
                                $this->CACHE[$k] = unserialize( $to_cache );
                            }
                        }
                    }
                }
                
                $this->DB->db_free_result( $sql );
                break;
                
                case 'filecache':
                if ( substr( $this->CFG['cache_dir'], ( strlen( $this->CFG['cache_dir'] ) - 1), strlen( $this->CFG['cache_dir'] ) ) == '/' )
                {
                    $cache_dir = substr( $this->CFG['cache_dir'], 0, ( strlen( $this->CFG['cache_dir'] ) - 1 ) );
                }
                else
                {
                    $cache_dir = $this->CFG['cache_dir'];
                }
                
                if ( substr( $cache_dir, 0, 1 ) == '/' )
                {
                    $cache_dir = substr( $cache_dir, 1, strlen( $cache_dir ) );
                }
                
                if ( substr( $this->CFG['db_cache_dir'], ( strlen( $this->CFG['db_cache_dir'] ) - 1), strlen( $this->CFG['db_cache_dir'] ) ) == '/' )
                {
                    $db_cache_dir = substr( $this->CFG['db_cache_dir'], 0, ( strlen( $this->CFG['db_cache_dir'] ) - 1 ) );
                }
                else
                {
                    $db_cache_dir = $this->CFG['db_cache_dir'];
                }
                
                if ( substr( $db_cache_dir, 0, 1 ) == '/' )
                {
                    $db_cache_dir = substr( $db_cache_dir, 1, strlen( $db_cache_dir ) );
                }
                
                $cache_dir = ROOT_PATH . $cache_dir . '/' . $db_cache_dir . '/';
                
                if ( ! file_exists( $cache_dir ) )
                {
                    die( '<h1>Oh Snap! Bulletin Error</h1>The database cache directory does not exist.' );
                }
                
                if ( ! is_readable( $cache_dir ) )
                {
                    die( '<h1>Oh Snap! Bulletin Error</h1>The database cache directory is not readable.' );
                }
                
                if ( ! is_writable( $cache_dir ) )
                {
                    die( '<h1>Oh Snap Bulletin Error</h1>The database cache directory is not writable.' );
                }
                
                foreach ( $cache_list as $k => $v )
                {
                    if ( ! file_exists( $cache_dir . $k . '.cache.' . $this->php_ext ) )
                    {
                        touch( $cache_dir . $k . '.cache.' . $this->php_ext );
                        chmod( $cache_dir . $k . '.cache.' . $this->php_ext, 0666 );
                    }
                }
                
                foreach ( $cache_list as $k => $v )
                {
                    unset( $records );
                    
                    if ( filesize( $cache_dir . $k . '.cache.' . $this->php_ext ) == 0 )
                    {
                        if ( $k == 'groups' )
                        {
                            $sorting = " ORDER BY sorting ASC";
                        }
                        else
                        {
                            $sorting = " ORDER BY " . $v . " ASC";
                        }
                        
                        if ( $k == 'forums' )
                        {
                            $sql = $this->DB->db_query( $this->SQL->sql_fetch_forums_with_depth() );
                        }
                        else
                        {
                            $this->TOSQL = array( 'table'   => $k,
                                                  'sorting' => $sorting );
                                                  
                            $sql = $this->DB->db_query( $this->SQL->sql_fetch_for_cache() );
                        }
                        
                        while ( $record = $this->DB->db_fetch_array( $sql ) )
                        {
                            $records[] = $record;
                        }
                        
                        $this->DB->db_free_result( $sql );
                        
                        $to_cache = serialize( $records );
                        
                        if ( $fh = @fopen( $cache_dir . $k . '.cache.' . $this->php_ext, 'w' ) )
                        {
                            @fwrite( $fh, $to_cache );
                            @fclose( $fh );
                        }
                        else
                        {
                            die( '<h1>Oh Snap! Bulletin</h1>The attempt to write the selected cache file has failed.' );
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
            if ( $table == 'groups' )
            {
                $sorting = " ORDER BY sorting ASC";
            }
            else
            {
                $sorting = " ORDER BY " . $id . " ASC";
            }
            
            switch ( $this->CFG['db_cache_method'] )
            {
                case 'dbcache':
                if ( $table == 'forums' )
                {
                    $sql = $this->DB->db_query( $this->SQL->sql_fetch_forums_with_depth() );
                }
                else
                {
                    $this->TOSQL = array( 'table'   => $table,
                                          'sorting' => $sorting );
                                          
                    $sql = $this->DB->db_query( $this->SQL->sql_fetch_for_cache() );
                }
                
                while ( $record = $this->DB->db_fetch_array( $sql ) )
                {
                    $records[] = $record;
                }
                
                $this->DB->db_free_result( $sql );
                
                $to_cache = serialize( $records );
                
                $this->TOSQL = array( 'to_cache' => $to_cache,
                                      'title'    => $table );
                                      
                $this->DB->db_query( $this->SQL->sql_update_cache_data() );
                
                $this->CACHE[$table] = unserialize( $to_cache );
                break;
                
                case 'filecache':
                if ( substr( $this->CFG['cache_dir'], ( strlen( $this->CFG['cache_dir'] ) - 1), strlen( $this->CFG['cache_dir'] ) ) == '/' )
                {
                    $cache_dir = substr( $this->CFG['cache_dir'], 0, ( strlen( $this->CFG['cache_dir'] ) - 1 ) );
                }
                else
                {
                    $cache_dir = $this->CFG['cache_dir'];
                }
                
                if ( substr( $cache_dir, 0, 1 ) == '/' )
                {
                    $cache_dir = substr( $cache_dir, 1, strlen( $cache_dir ) );
                }
                
                if ( substr( $this->CFG['db_cache_dir'], ( strlen( $this->CFG['db_cache_dir'] ) - 1), strlen( $this->CFG['db_cache_dir'] ) ) == '/' )
                {
                    $db_cache_dir = substr( $this->CFG['db_cache_dir'], 0, ( strlen( $this->CFG['db_cache_dir'] ) - 1 ) );
                }
                else
                {
                    $db_cache_dir = $this->CFG['db_cache_dir'];
                }
                
                if ( substr( $db_cache_dir, 0, 1 ) == '/' )
                {
                    $db_cache_dir = substr( $db_cache_dir, 1, strlen( $db_cache_dir ) );
                }
                
                $cache_dir = ROOT_PATH . $cache_dir . '/' . $db_cache_dir . '/';
                
                if ( ! file_exists( $cache_dir ) )
                {
                    die( '<h1>Oh Snap! Bulletin Error</h1>The database cache directory does not exist.' );
                }
                
                if ( ! is_readable( $cache_dir ) )
                {
                    die( '<h1>Oh Snap! Bulletin Error</h1>The database cache directory is not readable.' );
                }
                
                if ( ! is_writable( $cache_dir ) )
                {
                    die( '<h1>Oh Snap Bulletin Error</h1>The database cache directory is not writable.' );
                }
                
                if ( $table == 'groups' )
                {
                    $sorting = " ORDER BY sorting ASC";
                }
                else
                {
                    $sorting = " ORDER BY " . $id . " ASC";
                }
                
                if ( $table == 'forums' )
                {
                    $sql = $this->DB->db_query( $this->SQL->sql_fetch_forums_with_depth() );
                }
                else
                {
                    $this->TOSQL = array( 'table'   => $table,
                                          'sorting' => $sorting );
                                          
                    $sql = $this->DB->db_query( $this->SQL->sql_fetch_for_cache() );
                }
                
                while ( $record = $this->DB->db_fetch_array( $sql ) )
                {
                    $records[] = $record;
                }
                
                $this->DB->db_free_result( $sql );
                
                $to_cache = serialize( $records );
                
                if ( $fh = @fopen( $cache_dir . $table . '.cache.' . $this->php_ext, 'w' ) )
                {
                    @fwrite( $fh, $to_cache );
                    @fclose( $fh );
                }
                else
                {
                    die( '<h1>Oh Snap! Bulletin Error</h1>The attempt to write the selected cache file has failed.' );
                }
                
                $this->CACHE[$table] = unserialize( $to_cache );
                break;
            }
            break;
        }
    }
    
    public function populate_system_settings()
    {
        $settings = array();
        $settings = $this->get_data( 'system_settings', 'setting_id' );
        
        if ( $settings != false )
        {
            foreach ( $settings as $k => $v )
            {
                $this->CFG[$v['setting_key']] = $v['setting_value'];
            }
        }
    }
    
    public function get_data( $table, $id = '' )
    {
        switch ( $this->CFG['db_cache'] )
        {
            case true:
            if ( count( $this->CACHE[$table] ) > 0 )
            {
                return $this->CACHE[$table];
            }
            else
            {
                return false;
            }
            break;
            
            case false:
            if ( $table == 'groups' )
            {
                $sorting = " ORDER BY sorting ASC";
            }
            else
            {
                $sorting = " ORDER BY " . $id . " ASC";
            }
            
            if ( $table == 'forums' )
            {
                $sql = $this->DB->db_query( $this->SQL->sql_fetch_forums_with_depth() );
            }
            else
            {
                $this->TOSQL = array( 'table'   => $table,
                                      'sorting' => $sorting );
                                      
                $sql = $this->DB->db_query( $this->SQL->sql_fetch_for_cache() );
            }
            
            while ( $record = $this->DB->db_fetch_array( $sql ) )
            {
                $records[] = $record;
            }
            
            $this->DB->db_free_result( $sql );
            
            $result = serialize( $records );
            
            $this->CACHE[$table] = unserialize( $result );
            
            if ( count( $this->CACHE[$table] ) > 0 )
            {
                return $this->CACHE[$table];
            }
            else
            {
                return false;
            }
            break;
        }
    }
    
    public function setup_urls()
    {
        if ( substr( $this->CFG['application_url'], strlen( $this->CFG['application_url'] ) - 1, strlen( $this->CFG['application_url'] ) ) == '/' )
        {
            $this->base_url = substr( $this->CFG['application_url'], 0, strlen( $this->CFG['application_url'] ) - 1 );
        }
        else
        {
            $this->base_url = $this->CFG['application_url'];
        }
        
        $this->script_url = $this->base_url . '/' . $this->CFG['application_script_wrapper'] . '.' . $this->php_ext;
    }
    
    public function configure_urls_paths()
    {
        switch ( $this->MEMBER['status'] )
        {
            case false:
            $r = $this->get_data( 'installed_themes', 'theme_id' );
            
            if ( $r != false )
            {
                foreach ( $r as $k => $v )
                {
                    if ( $v['theme_id'] == $this->CFG['theme_id'] )
                    {
                        $theme_folder   = $v['installed_folder'];
                        $theme_imageset = $v['imageset_installed_folder'];
                    }
                }
            }
            
            $r = $this->get_data( 'installed_languages', 'language_id' );
            
            if ( $r != false )
            {
                foreach ( $r as $k => $v )
                {
                    if ( $v['language_id'] == $this->CFG['language_id'] )
                    {
                        $language_folder = $v['installed_folder'];
                    }
                }
            }
            
            date_default_timezone_set( $this->CFG['datetime_timezone'] );
            
            $this->theme_path   = ROOT_PATH . 'themes/' . $theme_folder . '/';
            $this->theme_url    = $this->base_url . '/themes/' . $theme_folder;
            $this->imageset_url = $this->base_url . '/public/imagesets/' . $theme_imageset;
            $this->lang_path    = ROOT_PATH . 'languages/' . $language_folder . '/';
            break;
            
            case true:
            // To do...
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
    
    public function load_theme()
    {
        require_once( $this->theme_path . 'global.theme.' . $this->php_ext );
        $this->THEME = new OHSNAPThemeGlobal;
        $this->THEME->SNAP =& $this;
        $this->THEME->LANG = $this->LANG;
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
            ( $b != '' ) ? $url .= '&amp;sact=' . $b : '';
            ( $c != '' ) ? $url .= '&amp;page=' . $c : '';
            
            return $this->script_url . $url;
            break;
        }
    }
}

?>