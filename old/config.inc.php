<?php

// OH SNAP! BULLETIN
// Application Configurations File
// Generated By Oh Snap! Bulletin Setup Wizard.
// Edit this file at your own risk, as it is not recommended.

if ( ! defined ( 'SNAP_INIT' ) )
{
    header( 'HTTP/1.0 403 Forbidden' );
    die();
}

$CFG['db_driver'] = 'mysql';
$CFG['db_hostname'] = 'localhost';
$CFG['db_port'] = 3306;
$CFG['db_database'] = 'yourihos_ohsnapbulletin';
$CFG['db_username'] = 'yourihos_tester';
$CFG['db_password'] = '1Meridian7!';
$CFG['db_persistant'] = false;
$CFG['db_log_errors'] = false;
$CFG['db_prefix'] = 'ohsnap_';
$CFG['logs_dir'] = 'logs';
$CFG['db_logs_dir'] = 'db';
$CFG['gzip_compression'] = true;
$CFG['db_cache'] = true;
$CFG['db_cache_method'] = 'filecache';
$CFG['db_cache_dir'] = 'db';
$CFG['cache_dir'] = 'cache';

?>