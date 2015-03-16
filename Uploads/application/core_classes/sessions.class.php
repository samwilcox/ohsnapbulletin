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

class OhSnapSessions {
    
    public    $SNAP;
    protected $_session_dur      = 15;
    protected $_session_ip_match = 0;
    protected $_session_lifetime = 0;
    
    public function manage_sessions()
    {
        $this->_session_dur      = $this->SNAP->CFG['session_timeout'];
        $this->_session_dur      = ( $this->_session_dur * 60 );
        $this->_session_ip_match = $this->SNAP->CFG['session_ip_matching'];
        
        switch ( $this->SNAP->CFG['session_storing_method'] )
        {
            case 'dbstore':
            $this->_session_lifetime = get_cfg_var( 'session.gc_maxlifetime' );
            
            session_set_save_handler(
                array( &$this, 'session_open' ),
                array( &$this, 'session_close' ),
                array( &$this, 'session_read' ),
                array( &$this, 'session_write' ),
                array( &$this, 'session_destroy' ),
                array( &$this, 'session_garbage_collection' )
            ); 
            
            session_start();
            break;
            
            case 'filestore':
            session_start();
            break;
        }
        
        $this->SNAP->SESSION['id'] = session_id();
        
        if ( isset( $_COOKIE['ohsnap_token'] ) )
        {
            $token = $_COOKIE['ohsnap_token']; $found = false;
            
            $r = $this->get_data( 'members', 'member_id' );
            
            if ( $r != false )
            {
                foreach ( $r as $k => $v )
                {
                    if ( $v['token'] == $token )
                    {
                        $found = true; $member_id = $v['member_id']; $member_display_name = $v['display_name'];
                        $member_username = $v['username']; $member_password = $v['password'];
                    }
                }
            }
            
            switch ( $found )
            {
                case true:
                $this->SNAP->TOSQL = array( 'member_id' => $member_id );
                $sql               = $this->SNAP->DB->db_query( $this->SNAP->SQL->sql_fetch_sessions_member_id() );
                $total             = $this->SNAP->DB->db_num_rows( $sql );
                $r                 = $this->SNAP->DB->db_fetch_object( $sql ); $this->SNAP->DB->db_free_result( $sql );
                
                switch ( $total )
                {
                    case 1:
                    switch ( $this->_session_ip_match )
                    {
                        case 1:
                        if ( ( $r->ip_address != $this->SNAP->AGENT['ip'] ) OR ( $r->user_agent != $this->SNAP->AGENT['agent'] ) )
                        {
                            session_unset(); session_destroy();
                            
                            if ( isset( $_COOKIE[session_name()] ) ) $this->SNAP->delete_cookie( session_name(), true );
                            $this->SNAP->delete_cookie( 'ohsnap_token' ); $this->SNAP->delete_cookie( 'ohsnap_anonymous' );
                            
                            $this->SNAP->TOSQL = array( 'member_id' => $member_id );
                            $this->SNAP->DB->db_query( $this->SNAP->SQL->sql_update_member_clear_token() );
                            $this->SNAP->update_database_cache( 'members', 'member_id' );
                            $this->delete_user_session();
                            unset( $_SESSION['ohsnap_username'] );
                            session_regenerate_id( true );
                            header( "Location: " . $this->SNAP->seo_url( 'forums' ) ); exit();
                        }
                        else
                        {
                            $this->SNAP->SESSION['expires']  = ( time() + $this->_session_dur ); $this->SNAP->SESSION['last_click'] = time();
                            $this->SNAP->SESSION['location'] = $this->SNAP->fetch_server_var( 'REQUEST_URI' ); $this->SNAP->MEMBER['id'] = $member_id;
                            $this->SNAP->MEMBER['username']  = $member_username; $this->SNAP->MEMBER['display_name'] = $member_display_name;
                            $this->update_user_session();
                        }
                        break;
                        
                        case 0:
                        $this->SNAP->SESSION['expires']  = ( time() + $this->_session_dur ); $this->SNAP->SESSION['last_click'] = time();
                        $this->SNAP->SESSION['location'] = $this->SNAP->fetch_server_var( 'REQUEST_URI' ); $this->SNAP->MEMBER['id'] = $member_id;
                        $this->SNAP->MEMBER['username']  = $member_username; $this->SNAP->MEMBER['display_name'] = $member_display_name;
                        $this->update_user_session();
                        break;
                    }
                    break;
                    
                    case 0:
                    $this->SNAP->MEMBER['id']             = $member_id; $this->SNAP->MEMBER['username'] = $member_username;
                    $this->SNAP->MEMBER['display_name']   = $member_display_name; $this->SNAP->SESSION['expires'] = ( time() + $this->_session_dur );
                    $this->SNAP->SESSION['last_click']    = time(); $this->SNAP->SESSION['location'] = $this->SNAP->fetch_server_var( 'REQUEST_URI' );
                    $this->SNAP->SESSION['admin_session'] = 0;
                    
                    ( $_COOKIE['ohsnap_anonymous'] == 1 ) ? $this->MEMBER['anonymous'] = 1 : $this->MEMBER['anonymous'] = 0;
                    $this->new_user_session();
                    $_SESSION['ohsnap_username'] = $member_username;
                    break;
                }
                break;
                
                case false:
                session_unset(); session_destroy();
                
                if ( isset( $_COOKIE[session_name()] ) ) $this->SNAP->delete_cookie( session_name(), true );
                $this->SNAP->delete_cookie( 'ohsnap_token' ); $this->SNAP->delete_cookie( 'ohsnap_anonymous' );
                $this->delete_user_session();
                unset( $_SESSION['ohsnap_username'] );
                session_regenerate_id( true );
                header( "Location: " . $this->SNAP->seo_url( 'forums' ) ); exit();
                break;
            }
        }
        else
        {
            $sql   = $this->SNAP->DB->db_query( $this->SNAP->SQL->sql_fetch_sessions_from_id() );
            $total = $this->SNAP->DB->db_num_rows( $sql );
            $r     = $this->SNAP->DB->db_fetch_object( $sql ); $this->SNAP->DB->db_free_result( $sql );
            
            switch ( $total )
            {
                case 1:
                switch ( $this->_session_ip_match )
                {
                    case 1:
                    if ( ( $r->ip_address != $this->SNAP->AGENT['ip'] ) OR ( $r->user_agent != $this->SNAP->AGENT['agent'] ) )
                    {
                        session_unset(); session_destroy();
                        
                        if ( isset( $_COOKIE[session_name()] ) ) $this->SNAP->delete_cookie( session_name(), true );
                        $this->SNAP->delete_user_session();
                        unset( $_SESSION['ohsnap_username'] );
                        session_regenerate_id( true );
                        header( "Location: " . $this->SNAP->seo_url( 'forums' ) ); exit();
                    }
                    else
                    {
                        $this->SNAP->SESSION['expires']  = ( time() + $this->_session_dur ); $this->SNAP->SESSION['last_click'] = time();
                        $this->SNAP->SESSION['location'] = $this->SNAP->fetch_server_var( 'REQUEST_URI' );
                        unset( $_SESSION['ohsnap_username'] );
                        $this->update_user_session();
                    }
                    break;
                    
                    case 0:
                    $this->SNAP->SESSION['expires']  = ( time() + $this->_session_dur ); $this->SNAP->SESSION['last_click'] = time();
                    $this->SNAP->SESSION['location'] = $this->SNAP->fetch_server_var( 'REQUEST_URI' );
                    unset( $_SESSION['ohsnap_username'] );
                    $this->update_user_session();
                    break;
                }
                break;
                
                case 0:
                $this->SNAP->MEMBER['id'] = 0; $this->SNAP->MEMBER['username'] = 'Guest';
                $this->SNAP->MEMBER['display_name'] = 'Guest'; $this->SNAP->SESSION['expires'] = ( time() + $this->_session_dur );
                $this->SNAP->SESSION['last_click'] = time(); $this->SNAP->SESSION['location'] = $this->SNAP->fetch_server_var( 'REQUEST_URI' );
                $this->SNAP->MEMBER['anonymous'] = 0; $this->SNAP->SESSION['admin_session'] = 0;
                unset( $_SESSION['ohsnap_username'] );
                $this->new_user_session();
                break;
            }
        }
    }
    
    public function new_user_session() { $this->SNAP->DB->db_query( $this->SNAP->SQL->sql_insert_session_new() ); $this->SNAP->validate_insertion( 'sessions' ); }
    public function update_user_session() { $this->SNAP->DB->db_query( $this->SNAP->SQL->sql_update_session() ); }
    public function delete_user_session() { $this->SNAP->DB->db_query( $this->SNAP->SQL->sql_delete_session() ); }   
    public function session_gc() { $this->SNAP->TOSQL = array( 'time' => time() ); $this->SNAP->DB->db_query( $this->SNAP->SQL->sql_delete_session_gc() ); } 
    public function session_open() { return true; }
    public function session_close() { return true; }
    
    public function session_read( $id )
    {
        $data = ''; $time = time();
        
        $this->SNAP->TOSQL = array( 'id' => $id, 'time' => $time );
        $sql               = $this->SNAP->DB->db_query( $this->SNAP->SQL->sql_fetch_session_data_from_store() );
        if ( $this->SNAP->DB->db_num_rows( $sql ) > 0 ) $r = $this->SNAP->DB->db_fetch_array( $sql ); $data = $r['data'];
        $this->DB->db_free_result( $sql );
        
        return $data;
    }
    
    public function session_write( $id, $data )
    {
        $time              = time() + $this->_session_lifetime;
        $this->SNAP->TOSQL = array( 'id' => $id );
        $sql               = $this->SNAP->DB->db_query( $this->SNAP->SQL->sql_fetch_session_from_store() );
        $total             = $this->SNAP->DB->db_num_rows( $sql ); $this->SNAP->DB->db_free_result( $sql );
        
        if ( $total == 0 )
        {
            $this->SNAP->TOSQL = array( 'id' => $id, 'data' => $data, 'lifetime' => $this->_session_lifetime );
            $this->SNAP->DB->db_query( $this->SNAP->SQL->sql_insert_session_store_new() );
            $this->SNAP->validate_insertion( 'session_store' );
        }
        else
        {
            $this->SNAP->TOSQL = array( 'id' => $id, 'data' => $data, 'lifetime' => $time );
            $this->SNAP->DB->db_query( $this->SNAP->SQL->sql_update_session_store_data() );
        }
        
        return true;
    }
    
    public function session_destroy( $id )
    {
        $this->SNAP->TOSQL = array( 'id' => $id ); $this->SNAP->DB->db_query( $this->SNAP->SQL->sql_delete_session_store() );
    }
    
    public function session_garbage_collection() { $this->SNAP->DB->db_query( $this->SNAP->SQL->sql_delete_session_store_gc() ); }
    
    public function check_online_record()
    {
        $r = $this->SNAP->get_data( 'statistics', 'statistic_id' );

        $sql = $this->SNAP->DB->db_query( $this->SNAP->SQL->sql_fetch_session_all() );
        
        $total = $this->SNAP->DB->db_num_rows( $sql );
        
        $this->SNAP->DB->db_free_result( $sql );

        if ( $total > $r[0]['most_users_online'] )
        {
            $this->SNAP->TOSQL = array( 'total' => $total, 'timestamp' => time() );
            
            $this->SNAP->DB->db_query( $this->SNAP->SQL->sql_update_statistics_record() );
            
            $this->SNAP->update_database_cache( 'statistics', 'statistic_id' );
        }
    }
}

?>