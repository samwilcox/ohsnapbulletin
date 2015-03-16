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

class forums {
    
    public $SNAP;
    public $LANG = array();
    
    public function class_init()
    {
        $LANG = array();
        require( $this->SNAP->lang_path . 'forums.lang.' . $this->SNAP->php_ext );
        $this->LANG = $LANG;
        
        require_once( $this->SNAP->skin_path . 'forums.skin.' . $this->SNAP->php_ext );
        $this->SKIN = new OhSnapSkinForums;
        $this->SKIN->SNAP =& $this->SNAP;
        $this->SKIN->LANG = $this->LANG;
        
        switch ( $this->SNAP->INC['do'] )
        {
            default:
            $this->bulletin_index();
            break;
        }
    }
    
    private function bulletin_index()
    {
        $this->SNAP->header( $this->LANG['forums'] );
        
        $depth = 0;
        
        $r = $this->SNAP->get_data( 'forums', 'forum_id' );
        
        if ( $r != false )
        {
            foreach ( $r as $k => $v )
            {
                if ( $v['hidden'] == 0 )
                {
                    if ( $v['depth'] == 0 )
                    {
                        if ( $depth > 0 ) echo $this->SKIN->skin_category_footer(); 
                        $this->SNAP->T = array( 'id' => $v['forum_id'], 'title' => $v['title'] );
                        echo $this->SKIN->skin_category_header();
                        $depth++; 
                    }
                    elseif ( $v['depth'] == 1 )
                    {
                        if ( strlen( $v['image_location'] ) > 0 )
                        {
                            $this->SNAP->T = array( 'image' => $v['image_location'] );
                            $forum_image   = $this->SKIN->skin_forum_image();
                        }
                        else
                        {
                            $forum_image = '';
                        }
                        
                        if ( $v['redirect'] == 1 )
                        {
                            $this->SNAP->T = array(
                                'image'       => $forum_image,
                                'forum_link'  => $this->SNAP->seo_url( 'forum', str_replace( ' ', '_', $v['title'] ) . '_' . $v['forum_id'] ),
                                'title'       => $v['title'],
                                'description' => $v['description'],
                                'redirects'   => $this->SNAP->lang_replace( $this->LANG['redirects'], 'total_redirects', number_format( $v['total_redirects'] ) )
                            );
                            
                            echo $this->SKIN->skin_forum_redirect_listing();
                        }
                        else
                        {
                            if ( $v['archived'] == 1 )
                            {
                                $this->SNAP->T = array( 'icon' => $this->SNAP->CFG['forum_status_archived_icon'], 'title' => $this->LANG['archived'] );
                                $forum_icon    = $this->SKIN->skin_forum_status_icon();
                            }
                            else
                            {
                                if ( ! $this->SNAP->MEMBER['status'] )
                                {
                                    $this->SNAP->T = array( 'icon' => $this->SNAP->CFG['forum_status_read_icon'], 'title' => $this->LANG['no_new_posts'] );
                                    $forum_icon    = $this->SKIN->skin_forum_status_icon();
                                }
                                else
                                {
                                    if ( $this->SNAP->check_for_unread( 'forum', $v['forum_id'] ) )
                                    {
                                        $this->SNAP->T = array( 'icon' => $this->SNAP->CFG['forum_status_unread_icon'], 'title' => $this->LANG['new_posts'] );
                                        $forum_icon    = $this->SKIN->skin_forum_status_icon();
                                    }
                                    else
                                    {
                                        $this->SNAP->T = array( 'icon' => $this->SNAP->CFG['forum_status_read_icon'], 'title' => $this->LANG['no_new_posts'] );
                                        $forum_icon    = $this->SKIN->skin_forum_status_icon();
                                    }
                                }
                            }
                            
                            $row = $this->SNAP->get_data( 'forums', 'forum_id' ); $subs = false;
                            
                            if ( $row != false )
                            {
                                foreach ( $row as $key => $val )
                                {
                                    if ( ( ( $val['parent_id'] == $v['forum_id'] ) AND ( $val['hidden'] == 0 ) AND ( $v['show_subs'] == 1 ) ) ) $subs = true;
                                }
                            }
                            
                            if ( $subs )
                            {
                                $sub_start = $this->SKIN->skin_sub_forums_start(); $x = 0;
                                
                                if ( $row != false )
                                {
                                    foreach ( $row as $key => $val )
                                    {
                                        if ( ( ( $val['parent_id'] == $v['forum_id'] ) AND ( $val['hidden'] == 0 ) AND ( $v['show_subs'] == 1 ) ) )
                                        {
                                            ( $x == 0 ) ? ( $sep = '' ) && ( $x++ ) : $sep = ', ';
                                            
                                            $this->SNAP->T = array(
                                                'seperator'  => $sep,
                                                'forum_link' => $this->SNAP->seo_url( 'forum', str_replace( ' ', '_', $val['title'] . '_' . $val['id'] ) ),
                                                'title'      => $val['title']
                                            );
                                            
                                            $sub_forums .= $this->SKIN->skin_sub_forums_listing();
                                        }
                                    }
                                }
                                
                                $sub_forums = $sub_start . $sub_forums . $this->SKIN->skin_sub_forums_end();
                            }
                            else
                            {
                                $sub_forums = '';
                            }
                            
                            if ( $v['last_post_timestamp'] != 0 )
                            {
                                if ( $v['last_post_member_id'] == 0 )
                                {
                                    $last_poster_name = $this->LANG['guest']; $last_poster_avatar = $this->SNAP->get_member_avatar( 0, true );
                                }
                                else
                                {
                                    $last_poster_name   = $this->SNAP->get_member_link( $v['last_post_member_id'] );
                                    $last_poster_avatar = $this->SNAP->get_member_avatar( $v['last_post_member_id'], true );
                                }
                                
                                $row = $this->SNAP->get_data( 'topics', 'topic_id' );
                                
                                if ( $row != false )
                                {
                                    foreach ( $row as $key => $val )
                                    {
                                        if ( $val['topic_id'] == $v['last_post_topic_id'] )
                                        {
                                            $last_post_topic_id = $val['topic_id']; $last_post_topic_title = $val['title'];
                                        }
                                    }
                                }
                                
                                $row = $this->SNAP->get_data( 'posts', 'post_id' ); $posts = 0;
                                
                                if ( $row != false )
                                {
                                    foreach ( $row as $key => $val ) if ( $val['topic_id'] == $last_post_topic_id ) $posts++;
                                }
                                
                                $post_id = $v['last_post_id'];
                                $pages   = ceil( $posts / $this->SNAP->CFG['perpage_posts'] );
                                if ( $pages == 0 ) $pages = 1;
                                
                                $this->SNAP->T = array(
                                    'topic_link'    => $this->SNAP->seo_url( 'topic', str_replace( ' ', '_', $last_post_topic_title ) . '_' . $last_post_topic_id ),
                                    'topic_lp_link' => $this->SNAP->seo_url( 'topic', str_replace( ' ', '_', $last_post_topic_title ) . '_' . $last_post_topic_id ) . '#' . $post_id,
                                    'topic_title'   => $last_post_topic_title,
                                    'timestamp'     => $this->SNAP->parse_timestamp( $v['last_post_timestamp'], true ),
                                    'member_link'   => $this->SNAP->lang_replace( $this->LANG['by'], 'member_name', $last_poster_name ),
                                    'avatar'        => $last_poster_avatar
                                );
                                
                                $last_post = $this->SKIN->skin_forum_last_post();
                            }
                            else
                            {
                                $last_post = '---';
                            }
                            
                            $this->SNAP->T = array(
                                'icon'          => $forum_icon,
                                'image'         => $forum_image,
                                'forum_link'    => $this->SNAP->seo_url( 'forum', str_replace( ' ', '_', $v['title'] ) . '_' . $v['forum_id'] ),
                                'title'         => $v['title'],
                                'description'   => $v['description'],
                                'sub_forums'    => $sub_forums,
                                'last_post'     => $last_post,
                                'total_topics'  => number_format( $v['total_topics'] ),
                                'total_replies' => number_format( $v['total_replies'] )
                            );
                            
                            echo $this->SKIN->skin_forum_listing();
                        }
                    }                  
                }
            }
            
            echo $this->SKIN->skin_category_footer();
        }
        
        if ( $this->SNAP->check_feature_permissions( 'bbic' ) )
        {
            $this->SNAP->T = array(
                'online_link' => $this->SNAP->seo_url( 'online' ),
                'day'         => date( 'j', time() - 3600 ),
                'month'       => date( 'n', time() - 3600 ),
                'year'        => date( 'Y', time() - 3600 )
            );
            
            echo $this->SKIN->skin_bbic_start();
            
            $total_online = 0; $total_members = 0; $total_anonymous = 0; $total_guests = 0; $total_bots = 0; $x = 0; $online_listing = '';
            
            $sql = $this->SNAP->DB->db_query( $this->SNAP->SQL->sql_fetch_session_whos_online() );
            
            while ( $r = $this->SNAP->DB->db_fetch_object( $sql ) )
            {
                $total_online++;
                
                if ( ( $r->member_id == 0 ) AND ( $r->bot == 0 ) )
                {
                    $total_guests++;
                }
                elseif ( ( $r->member_id == 0 ) AND ( $r->bot == 1 ) )
                {
                    $total_bots++;
                    
                    if ( $this->SNAP->CFG['display_search_bots'] == 1 )
                    {
                        if ( $x == 0 ) { $sep = ''; $x++; } else { $sep = ', '; }
                        $online_listing .= $sep . $r->bot_name;
                    }
                }
                elseif ( ( ( $r->member_id != 0 ) AND ( $r->bot == 0 ) AND ( $r->anonymous == 1 ) ) )
                {
                    $total_anonymous++;
                }
                else
                {
                    $total_members++;
                    
                    ( $x == 0 ) ? ( $sep = '' ) && ( $x++ ) : $sep = ', ';                    
                    $online_listing .= $this->SNAP->get_member_link( $r->member_id, $this->SNAP->lang_replace( $this->LANG['last_click'], 'timestamp', $this->SNAP->parse_timestamp( $r->last_click, false, false, true ) ), $sep );
                }
            }
            
            $this->SNAP->DB->db_free_result( $sql );
            
            if ( strlen( $online_listing ) < 1 )
            {
                $this->SNAP->T  = array( 'text' => $this->LANG['no_members_online'] );
                $online_listing = $this->SNAP->SKIN->skin_italic_text();
            }

            $this->SKIN->LANG['online_now'] = $this->SNAP->lang_replace( $this->SKIN->LANG['online_now'], 'total', number_format( $total_online ) );
            $this->SKIN->LANG['accurate']   = $this->SNAP->lang_replace( $this->SKIN->LANG['accurate'], 'minutes', $this->SNAP->CFG['session_timeout'] );
            $this->SKIN->LANG['counts']     = $this->SNAP->lang_replace( $this->SKIN->LANG['counts'], 'members', number_format( $total_members ) );
            $this->SKIN->LANG['counts']     = $this->SNAP->lang_replace( $this->SKIN->LANG['counts'], 'anonymous', number_format( $total_anonymous ) );
            $this->SKIN->LANG['counts']     = $this->SNAP->lang_replace( $this->SKIN->LANG['counts'], 'guests', number_format( $total_guests ) );
            $this->SKIN->LANG['counts']     = $this->SNAP->lang_replace( $this->SKIN->LANG['counts'], 'bots', number_format( $total_bots ) );
            
            $this->SNAP->T = array( 'listing' => $online_listing );
            echo $this->SKIN->skin_whos_online_snapin();
            
            $r = $this->SNAP->get_data( 'members', 'member_id' );
            
            array_reverse( $r ); end( $r );
            
            $newest_member    = $this->SNAP->get_member_link( $r[0]['member_id'] );
            $newest_timestamp = $this->SNAP->parse_timestamp( $r[0]['joined_timestamp'], true );            
            $r = $this->SNAP->get_data( 'members', 'member_id' ); $total_members = count( $r );
            $r = $this->SNAP->get_data( 'posts', 'post_id' );     $total_posts   = count( $r );
            $r = $this->SNAP->get_data( 'topics', 'topic_id' );   $total_topics  = count( $r );
            $total_replies = ( $total_posts - $total_topics ); 
                       
            $r = $this->SNAP->get_data( 'statistics', 'statistic_id' );
            
            $most_users_online = number_format( $r[0]['most_users_online'] );
            $most_users_timestamp = $this->SNAP->parse_timestamp( $r[0]['most_users_timestamp'], true ); 
             
            $date_today = date( 'm/d/Y', time() - 3600 ); 
                     
            $r = $this->SNAP->get_data( 'visits', 'visit_id' ); $total_visits = 0;
            
            if ( $r != false )
            {
                foreach ( $r as $k => $v )
                {
                    if ( date( 'm/d/Y', $v['timestamp'] - 3600 ) == $date_today )
                    {
                        $total_visits++;
                    }
                }
            }
            
            $this->SKIN->LANG['welcome_member'] = $this->SNAP->lang_replace( $this->SKIN->LANG['welcome_member'], 'member_name', $newest_member );  
                      
            $this->SNAP->T = array( 'text' => $newest_timestamp ); $newest_timestamp = $this->SNAP->SKIN->skin_timestamp_text();
            
            $this->SKIN->LANG['welcome_member'] = $this->SNAP->lang_replace( $this->SKIN->LANG['welcome_member'], 'timestamp', $newest_timestamp );
            $this->SKIN->LANG['total_members']  = $this->SNAP->lang_replace( $this->SKIN->LANG['total_members'], 'total', number_format( $total_members ) );
            $this->SKIN->LANG['total_posts']    = $this->SNAP->lang_replace( $this->SKIN->LANG['total_posts'], 'posts', number_format( $total_posts ) );
            $this->SKIN->LANG['total_posts']    = $this->SNAP->lang_replace( $this->SKIN->LANG['total_posts'], 'replies', number_format( $total_replies ) );
            $this->SKIN->LANG['total_posts']    = $this->SNAP->lang_replace( $this->SKIN->LANG['total_posts'], 'topics', number_format( $total_topics ) );
            
            $this->SNAP->T = array( 'text' => $most_users_timestamp ); $most_users_timestamp = $this->SNAP->SKIN->skin_timestamp_text();
            
            $this->SKIN->LANG['most_users']  = $this->SNAP->lang_replace( $this->SKIN->LANG['most_users'], 'total', number_format( $most_users_online ) );
            $this->SKIN->LANG['most_users']  = $this->SNAP->lang_replace( $this->SKIN->LANG['most_users'], 'timestamp', $most_users_timestamp );
            $this->SKIN->LANG['users_today'] = $this->SNAP->lang_replace( $this->SKIN->LANG['users_today'], 'total', number_format( $total_visits ) );
            
            echo $this->SKIN->skin_statistics_snapin();
            
            $birthday_listing = ''; $total_birthdays = 0; $x = 0;
            $month = date( 'n', time() - 3600 ); $day = date( 'j', time() - 3600 );
            
            $r = $this->SNAP->get_data( 'members', 'member_id' );
            
            if ( $r != false )
            {
                foreach ( $r as $k => $v )
                {
                    if ( ( $v['dob_month'] == $month ) AND ( $v['dob_day'] == $day ) )
                    {
                        $total_birthdays++;
                        
                        $age = $this->SNAP->calculate_age( $v['dob_month'], $v['dob_day'], $v['dob_year'] );
                        
                        $this->SKIN->LANG['years_old'] = $this->SNAP->lang_replace( $this->SKIN->LANG['years_old'], 'years', $age );
                        
                        ( $x == 0 ) ? ( $sep = '' ) && ( $x++ ) : $sep = ', ';
                        
                        $birthday_listing .= $this->SNAP->get_member_link( $v['member_id'], '', $sep );
                        
                        if ( $v['dob_show'] == 1 ) $this->SNAP->T = array( 'age' => $age ); $birthday_listing .= $this->SKIN->skin_years_old();
                    }
                }
            }

            if ( $total_birthdays == 0 )
            {
                $this->SNAP->T = array( 'text' => $this->LANG['no_members_celebrating'] ); $birthday_listing = $this->SNAP->SKIN->skin_italic_text();
            }
 
            $this->SKIN->LANG['members_celebrating'] = $this->SNAP->lang_replace( $this->SKIN->LANG['members_celebrating'], 'total', number_format( $total_birthdays ) );
            
            $this->SNAP->T = array( 'listing' => $birthday_listing ); echo $this->SKIN->skin_birthdays_snapin();
            
            echo $this->SKIN->skin_bbic_end();
        }
        
        $this->SNAP->footer();
    }
}

?>