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

class OhSnapDatabase {
    
    public    $SNAP;
    protected $_db_hostname   = 'localhost';
    protected $_db_port       = 3306;
    protected $_db_database   = '';
    protected $_db_username   = 'root';
    protected $_db_password   = '';
    private   $_db_persistant = false;
    private   $_db_log_errors = false;
    private   $_db_last_query = '';
    private   $_db_handle;
    public    $db_error_msg   = '';
    public    $db_error_num   = 0;
    public    $db_queries     = 0;
    
    public function set_hostname( $v ) { $this->_db_hostname = $v; }
    public function set_port( $v ) { $this->_db_port = $v; }
    public function set_database( $v ) { $this->_db_database = $v; }
    public function set_username( $v ) { $this->_db_username = $v; }
    public function set_password( $v ) { $this->_db_password = $v; }
    public function set_persistant( $v ) { $this->_db_persistant = $v; }
    public function set_log_errors( $v ) { $this->_db_log_errors = $v; }
    
    public function db_establish_connection()
    {
        switch ( $this->_db_persistant )
        {
            case true:
            $this->_db_handle = @mysql_pconnect( $this->_db_hostname . ':' . $this->_db_port, $this->_db_username, $this->_db_password );
            break;
            
            case false:
            $this->_db_handle = @mysql_connect( $this->_db_hostname . ':' . $this->_db_port, $this->_db_username, $this->_db_password );
            break;
        }
        
        if ( ! $this->_db_handle ) $this->db_error( 'Attempt to establish a valid connection to the configured database server failed.' );
        
        unset( $this->_db_username );
        unset( $this->_db_password );
        
        if ( ! @mysql_select_db( $this->_db_database, $this->_db_handle ) ) $this->db_error( 'Attempt to find the configuration database on the configured database server failed.' );
        
        return $this->_db_handle;
    }
    
    public function db_cleaner( $str ) { return @mysql_real_escape_string( $str, $this->_db_handle ); }
    
    public function db_query( $q )
    {
        if ( ! $query = @mysql_query( $q, $this->_db_handle ) )
        {
            $this->_db_last_query = $q;
            $this->db_error( 'Attempt to execute the desired SQL statement failed. For further details regarding this error, please see your database logs.' );
        }
        
        $this->db_queries++;
        
        return $query;
    }
    
    public function db_free_result( $r ) { return @mysql_free_result( $r ); }
    public function db_fetch_object( $r ) { return @mysql_fetch_object( $r ); }
    public function db_fetch_array( $r ) { return @mysql_fetch_array( $r ); }
    public function db_fetch_assoc( $r ) { return @mysql_fetch_assoc( $r ); }
    public function db_num_rows( $r ) { return @mysql_num_rows( $r ); }
    public function db_insert_id() { return @mysql_insert_id( $this->_db_handle ); }
    public function db_affected_rows() { return @mysql_affected_rows( $this->_db_handle ); }
    
    public function db_disconnect()
    {
        if ( $this->_db_handle ) { @mysql_close( $this->_db_handle ); unset( $this->_db_handle ); return; }
    }
    
    private function db_error( $err )
    {
        ( @mysql_error( $this->_db_handle ) ? $this->db_error_msg = @mysql_error( $this->_db_handle ) : ( @mysql_error() ) ? $this->db_error_msg = @mysql_error() : '' );
        ( @mysql_errno( $this->_db_handle ) ? $this->db_error_num = @mysql_errno( $this->_db_handle ) : ( @mysql_errno() ) ? $this->db_error_num = @mysql_errno() : '' );
        
        switch ( $this->_db_log_errors )
        {
            case true:
            if ( $this->_db_last_query != '' ) $this->_db_last_query = "\n" . $this->_db_last_query;
            
            $log_dir = _ROOT_PATH . $this->CFG['logs_dir'] . '/' . $this->CFG['db_logs_dir'];
            
            if ( ! file_exists( $log_dir ) ) die( '<h1>Oh Snap! Bulletin Error</h1>The configured database logs directory does not exist.' );
            if ( ! is_writable( $log_dir ) ) die( '<h1>Oh Snap! Bulletin Error</h1>The configured database logs directory does not have valid write permissions.' );
            
            $log_file = _ROOT_PATH . $this->CFG['logs_dir'] . '/' . $this->CFG['db_logs_dir'] . '/' . 'db-error-' . date( 'r' ) . '.log.' . $this->php_ext;
            $log_file = str_replace( ' ', '_', $log_file );
            
            touch ( $log_file ); chmod( $log_file, 0666 );
            
            $log .= "<?php\n\n";
            $log .= "if ( ! defined( '_SNAP_INIT' ) ){ header( 'HTTP/1.1 403 Forbidden' ); }\n\n";
            $log .= "---------------------------------------------------------------------------------------------------\n";
            $log .= "Date/Time: " . date( 'r' ) . "\n";
            $log .= "Error: " . $err . "\n";
            $log .= "MySQL Error Number: " . $this->db_error_num . "\n";
            $log .= "MySQL Error Message: " . $this->db_error_msg . "\n";
            $log .= "Visitor IP Address: " . $this->SNAP->AGENT['ip'] . "\n";
            $log .= "Visitor Hostname: " . $this->SNAP->AGENT['hostname'] . "\n";
            $log .= "Visitor Location: " . $this->SNAP->fetch_server_var( 'REQUEST_URI' );
            $log .= $this->_db_last_query;
            $log .= "\n\n";
            $log .= "---------------------------------------------------------------------------------------------------\n\n";
            $log .= "?>";
            
            if ( $fh = @fopen( $log_file, 'w' ) ) @fwrite( $fh, $log ); @close( $fh );
            break;
        }
        
        echo ( 'Database error: ' . $err . '<br><br>MySQL Message: ' . $this->db_error_msg );
        die ( '' );
    }
}

?>