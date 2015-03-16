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

class authenticate {
    
    public $SNAP;
    public $LANG = array();
    
    public function class_init()
    {
        $LANG = array();
        require( $this->SNAP->lang_path . 'authenticate.lang.' . $this->SNAP->php_ext );
        $this->LANG = $LANG;
        
        require_once( $this->SNAP->theme_path . 'authenticate.theme.' . $this->SNAP->php_ext );
        $this->THEME       = new OHSNAPThemeAuthenticate;
        $this->THEME->SNAP =& $this->SNAP;
        $this->THEME->LANG = $this->LANG;
        
        switch ( $this->SNAP->INC['sact'] )
        {
            default:
            $this->member_sign_in_form();
            break;
            
            case 'signin':
            $this->authenticate_member();
            break;
            
            case 'signout':
            $this->deauthorize_member();
            break;
        }
    }
    
    public function member_sign_in_form( $signin_error = false, $error = '' )
    {
        $this->SNAP->T = array( 'signin_link' => $this->SNAP->seo_url( 'authenticate' ) );        
        $this->SNAP->header( $this->THEME->html_member_sign_in_form_nav(), $this->LANG['member_sign_in'] );
        
        switch ( $signin_error )
        {
            case true:
            $this->SNAP->T    = array( 'error' => $error );
            $signin_error_box = $this->THEME->html_sign_in_error_box();
            break;
            
            case false:
            $signin_error_box = '';
            break;
        }
        
        if ( preg_match( "#" . $this->SNAP->CFG['application_url'] . "#i", $this->SNAP->fetch_server_var( 'HTTP_REFERER' ) ) )
        {
            $referer = $this->SNAP->fetch_server_var( 'HTTP_REFERER' );
        }
        else
        {
            $referer = '';
        }
        
        $this->SNAP->T = array( 'create_link' => $this->SNAP->seo_url( 'createaccount' ) );
        
        $this->SNAP->LANG['dont_have_account'] = $this->SNAP->language_replace( $this->SNAP->LANG['dont_have_account'], 'create_account_link', $this->THEME->html_create_account_link() );
        
        $this->SNAP->T = array( 'referer'       => $referer,
                                'error_box'     => $signin_error_box,
                                'forgotpw_link' => $this->SNAP->seo_url( 'authenticate', 'forgotpw' ) );
                                
        echo $this->THEME->html_sign_in_form();
        
        $this->SNAP->footer();
    }
    
    private function authenticate_member()
    {
        $username    = $this->SNAP->INC['username'];
        $password    = $this->SNAP->INC['password'];
        $remember_me = $this->SNAP->INC['rememberme'];
        $anonymous   = $this->SNAP->INC['anonymous'];
        $referer     = $this->SNAP->INC['referer'];
        
        $found = false;
        
        $r = $this->SNAP->get_data( 'members', 'member_id' );
        
        if ( $r != false )
        {
            foreach ( $r as $k => $v )
            {
                if ( ( $v['username'] == $username ) OR ( $v['email_address'] == $username ) )
                {
                    $found = true;
                    $member_username     = $v['username'];
                    $member_password     = $v['password'];
                    $member_salt         = $v['salt'];
                    $member_id           = $v['member_id'];
                    $member_display_name = $v['display_name'];                    
                }
            }
        }
        
        if ( ! $found )
        {
            $this->member_sign_in_form( true, $this->SNAP->ERRORS['sign_in_not_found'] );
            exit();
        }
        
        $enc_password = md5( $password . $member_salt );
        
        if ( $enc_password != $member_password )
        {
            $this->member_sign_in_form( true, $this->SNAP->ERRORS['sign_in_invalid_pw'] );
            exit();
        }
        
        $_SESSION['ohsnap_username'] = $member_username;
        
        $this->SNAP->MEMBER['id']           = $member_id;
        $this->SNAP->MEMBER['username']     = $member_username;
        $this->SNAP->MEMBER['display_name'] = $member_display_name;
        
        $dur             = $this->SNAP->CFG['session_timeout'];
        $dur             = ( $dur * 60 );
        $session_expires = ( time() + $dur );
        $expires         = '';
        
        if ( $remember_me == 1 )
        {
            $token   = $this->SNAP->generate_sign_in_token( $member_username );
            $expires = ( time() + 60 * 60 * 24 * 365 );
            $this->SNAP->new_cookie( 'ohsnap_token', $token, $expires );
            
            $this->SNAP->TOSQL = array( 'token' => $token,
                                        'time'  => time(),
                                        'id'    => $member_id );
                                        
            $this->SNAP->DB->db_query( $this->SNAP->SQL->sql_update_members_sign_in_token() );
            
            $this->SNAP->update_database_cache( 'members', 'member_id' );
        }
        else
        {
            $token   = $this->SNAP->generate_sign_in_token( $member_username );
            $expires = $session_expires;
            $this->SNAP->new_cookie( 'ohsnap_token', $token, $expires );
            
            $this->SNAP->DB->db_query( $this->SNAP->SQL->sql_update_members_sign_in_token() );
            
            $this->SNAP->update_database_cache( 'members', 'member_id' );
        }
        
        if ( $anonymous == 1 )
        {
            $this->SNAP->new_cookie( 'ohsnap_anonymous', true, $expires );
            $anonymous = 1;
        }
        else
        {
            $anonymous = 0;
        }
        
        $this->SNAP->TOSQL = array( 'id'         => $member_id,
                                    'username'   => $member_username,
                                    'expires'    => $expires,
                                    'last_click' => time(),
                                    'location'   => $this->SNAP->fetch_server_var( 'REQUEST_URI' ),
                                    'anonymous'  => $anonymous,
                                    'session_id' => $this->SNAP->SESSION['id'] );
                                    
        $this->SNAP->DB->db_query( $this->SNAP->SQL->sql_update_session_new_sign_in() );
                                    
        $r = $this->SNAP->get_data( 'statistics', 'statistic_id' );
        
        if ( $r != false )
        {
            foreach ( $r as $k => $v )
            {
                if ( $v['statistic_id'] == 1 ) $total_sign_ins = $v['total_sign_ins'];
            }
        }
        
        $total_sign_ins++;
        
        $this->SNAP->TOSQL = array( 'total' => $total_sign_ins );        
        $this->SNAP->DB->db_query( $this->SNAP->SQL->sql_update_statistics_sign_ins() );        
        $this->SNAP->update_database_cache( 'statistics', 'statistic_id' );
                                    
        header( "Location: " . $this->SNAP->seo_url( 'index' ) );
        exit();
    }
    
    private function deauthorize_member()
    {
        $this->SNAP->TOSQL = array( 'id' => $this->SNAP->MEMBER['id'] );
        $this->SNAP->DB->db_query( $this->SNAP->SQL->sql_update_members_remove_token() );
        $this->SNAP->update_database_cache( 'members', 'member_id' );
        
        $this->SNAP->DB->db_query( $this->SNAP->SQL->sql_delete_session() );
        
        session_unset();
        session_destroy();
        session_regenerate_id( true );
        
        if ( isset( $_COOKIE[session_name()] ) ) $this->SNAP->delete_cookie( session_name(), true );
        
        $this->SNAP->delete_cookie( 'ohsnap_token' );
        $this->SNAP->delete_cookie( 'ohsnap_anonymous' );
        
        header( "Location: " . $this->SNAP->seo_url( 'index' ) );
        exit();
    }
}

?>