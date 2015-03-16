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

class index {
    
    public $SNAP;
    public $LANG = array();
    
    public function class_init()
    {
        $LANG = array();
        require( $this->SNAP->lang_path . 'index.lang.' . $this->SNAP->php_ext );
        $this->LANG = $LANG;
        
        require_once( $this->SNAP->theme_path . 'index.theme.' .$this->SNAP->php_ext );
        $this->THEME = new OHSNAPThemeIndex;
        $this->THEME->SNAP =& $this->SNAP;
        $this->THEME->LANG = $this->LANG;
        
        switch ( $this->SNAP->INC['sact'] )
        {
            default:
            $this->bulletin_board_index();
            break;
        }
    }
    
    private function bulletin_board_index()
    {
        $this->SNAP->header();
        
        if ( $this->SNAP->CFG['islands_enabled'] == 1 )
        {
            $islands_js        = $this->THEME->html_islands_js();
            $left_island_start = $this->THEME->html_left_island_start();
        }
        else
        {
            $islands_js        = '';
            $left_island_start = '';
        }
        
        $this->SNAP->T = array( 'islands_js'        => $islands_js,
                                'left_island_start' => $left_island_start );
                                
        echo $this->THEME->html_index_start();
        
        $depth = 0;
        $y     = 0;
        
        $r = $this->SNAP->get_data( 'forums', 'forum_id' );
        
        if ( $r != false )
        {
            foreach ( $r as $k => $v )
            {
                if ( $v['hidden'] == 0 )
                {
                    if ( $v['depth'] == 0 )
                    {
                        if ( $depth > 0 )
                        {
                            echo $this->THEME->html_category_footer();
                            
                            $y = 0;
                        }
                        
                        $this->SNAP->T = array( 'id'   => $v['forum_id'],
                                                'name' => $v['title'] );
                                                
                        echo $this->THEME->html_category_header();
                        
                        $depth++;
                    }
                    elseif ( $v['depth'] == 1 )
                    {                        
                        if ( $v['image_url'] != '' )
                        {
                            $this->SNAP->T = array( 'image' => $v['image_url'] );
                            
                            $forum_image = $this->THEME->html_forum_image();
                        }
                        else
                        {
                            $forum_image = '';
                        }
                        
                        if ( $v['redirect'] == 1 )
                        {
                            if ( $y > 0 )
                            {
                                echo $this->THEME->html_forum_seperator();
                            }
                            
                            $y++;
                            
                            $this->SNAP->T = array( 'image'       => $forum_image,
                                                    'forum_link'  => $this->SNAP->seo_url( 'forum', $v['forum_id'] ),
                                                    'title'       => $v['title'],
                                                    'description' => $v['description'],
                                                    'hits'        => number_format( $v['redirect_hits'] ) );
                                                    
                            echo $this->THEME->html_forum_redirect_listing();
                        }
                        else
                        {
                            if ( $v['archived'] == 1 )
                            {
                                $forum_icon = $this->THEME->html_forum_archived_icon();
                            }
                            else
                            {
                                if ( ! $this->SNAP->MEMBER['status'] )
                                {
                                    $forum_icon = $this->THEME->html_forum_no_unread_icon();
                                }
                                else
                                {
                                    if ( $this->SNAP->check_for_unread( 'forum', $v['forum_id'] ) )
                                    {
                                        $forum_icon = $this->THEME->html_forum_unread_icon();
                                    }
                                    else
                                    {
                                        $forum_icon = $this->THEME->html_forum_no_unread_icon();
                                    }
                                }
                            }
                            
                            $have_subs = false;
                            
                            $row = $this->SNAP->get_data( 'forums', 'forum_id' );
                            
                            if ( $row != false )
                            {
                                foreach ( $row as $key => $val )
                                {
                                    if ( ( ( $val['parent_id'] == $v['forum_id'] ) AND ( $val['hidden'] == 0 ) AND ( $v['show_subs'] == 1 ) ) )
                                    {
                                        $have_subs = true;
                                    }
                                }
                            }
                            
                            switch ( $have_subs )
                            {
                                case true:
                                $x = 0;
                                
                                $sub_start = $this->THEME->html_sub_forums_start();
                                
                                $row = $this->SNAP->get_data( 'forums', 'forum_id' );
                                
                                if ( $row != false )
                                {
                                    foreach ( $row as $key => $val )
                                    {
                                        if ( ( ( $val['parent_id'] == $v['forum_id'] ) AND ( $val['hidden'] == 0 ) AND ( $v['show_subs'] == 1 ) ) )
                                        {
                                            if ( $x == 0 ) { $sep = ''; $x++; } else { $sep = ', '; }
                                            
                                            $this->SNAP->T = array( 'seperator'  => $sep,
                                                                    'forum_link' => $this->SNAP->seo_url( 'forum', $val['forum_id'] ),
                                                                    'title'      => $val['title'] );
                                                                    
                                            $subs .= $this->THEME->html_sub_forums_listing();
                                        }
                                    }
                                }
                                
                                $sub_forums = $sub_start . $subs;
                                break;
                                
                                case false:
                                $sub_forums = '';
                                break;
                            }
                            
                            if ( $v['last_post_timestamp'] != 0 )
                            {
                                $last_post_timestamp = $this->SNAP->parse_timestamp( $v['last_post_timestamp'], true );
                                
                                if ( $v['last_post_member_id'] == 0 )
                                {
                                    $last_post_name   = $this->LANG['guest'];
                                    $last_post_avatar = $this->SNAP->get_member_avatar( 0, true );
                                }
                                else
                                {
                                    $last_post_name   = $this->SNAP->get_member_link( $v['last_post_member_id'] );
                                    $last_post_avatar = $this->SNAP->get_member_link( $v['last_post_member_id'], '', '', $this->SNAP->get_member_avatar( $v['last_post_member_id'], true ) );
                                }
                                
                                $row = $this->SNAP->get_data( 'topics', 'topic_id' );
                                
                                if ( $row != false )
                                {
                                    foreach ( $row as $key => $val )
                                    {
                                        if ( $val['topic_id'] == $v['last_post_topic_id'] )
                                        {
                                            $last_post_topic_id    = $val['topic_id'];
                                            $last_post_topic_title = $val['title'];
                                        }
                                    }
                                }
                                
                                $total_posts = 0;
                                
                                $row = $this->SNAP->get_data( 'posts', 'post_id' );
                                
                                if ( $row != false )
                                {
                                    foreach ( $row as $key => $val )
                                    {
                                        if ( $val['topic_id'] == $last_post_topic_id )
                                        {
                                            $total_posts++;
                                        }
                                    }
                                }
                                
                                $post_id     = $v['last_post_id'];
                                $per_page    = $this->SNAP->CFG['perpage_posts'];
                                $total_pages = ceil( $total_posts / $per_page );
                                
                                if ( $total_pages == 0 ) { $total_pages = 1; }
                                
                                $this->SNAP->T = array( 'topic_link'    => $this->SNAP->seo_url( 'topic', $last_post_topic_id ),
                                                        'topic_lp_link' => $this->SNAP->seo_url( 'topic', $last_post_topic_id, $total_pages ) . '#' . $post_id,
                                                        'topic_title'   => $last_post_topic_title,
                                                        'timestamp'     => $last_post_timestamp,
                                                        'member_link'   => $last_post_name,
                                                        'avatar'        => $last_post_avatar );
                                                        
                                $last_post = $this->THEME->html_last_post_information();
                            }
                            else
                            {
                                $last_post = '---';
                            }
                        
                            if ( $y > 0 )
                            {
                                echo $this->THEME->html_forum_seperator();
                            }
                            
                            $y++;
                            
                            $this->SNAP->T = array( 'icon'          => $forum_icon,
                                                    'image'         => $forum_image,
                                                    'link'          => $this->SNAP->seo_url( 'forum', $v['forum_id'] ),
                                                    'title'         => $v['title'],
                                                    'description'   => $v['description'],
                                                    'sub_forums'    => $sub_forums,
                                                    'last_post'     => $last_post,
                                                    'total_topics'  => number_format( $v['topics'] ),
                                                    'total_replies' => number_format( $v['replies'] ) );
                                                    
                            echo $this->THEME->html_forum_listing();
                        }
                    }
                }
            }
            
            echo $this->THEME->html_category_footer();
        }
        
        switch ( $this->SNAP->check_feature_permissions( 'whos_online_snapin') )
        {
            case true:
            $total_online    = 0;
            $total_members   = 0;
            $total_anonymous = 0;
            $total_guests    = 0;
            $total_bots      = 0;
            $x               = 0;
            $online_listing  = '';
            
            $sql = $this->SNAP->DB->db_query( $this->SNAP->SQL->sql_select_session_whos_online_snapin() );
            
            while ( $r = $this->SNAP->DB->db_fetch_object( $sql ) )
            {
                $total_online++;
                
                if ( ( $r->member_id == 0 ) AND ( $r->search_bot == 0 ) )
                {
                    $total_guests++;
                }
                elseif ( ( $r->member_id == 0 ) AND ( $r->search_bot == 1 ) )
                {
                    $total_bots++;
                    
                    switch ( $this->SNAP->CFG['search_bot_display'] )
                    {
                        case 1:
                        if ( $x == 0 ) { $sep = ''; $x++; } else { $sep = ', '; }
                        
                        $online_listing .= $sep . $r->search_bot_name;
                        break;
                    }
                }
                elseif ( ( ( $r->member_id != 0 ) AND ( $r->search_bot == 0 ) AND ( $r->anonymous == 1 ) ) )
                {
                    $total_anonymous++;
                }
                else
                {
                    $total_members++;
                    
                    if ( $x == 0 ) { $sep = ''; $x++; } else { $sep = ', '; }
                    
                    $online_listing .= $this->SNAP->get_member_link( $r->member_id, $this->SNAP->language_replace( $this->LANG['last_click'], 'timestamp', $this->SNAP->parse_timestamp( $r->last_click, false, false, true ) ), $sep );
                }
            }
            
            $this->SNAP->DB->db_free_result( $sql );
            
            if ( strlen( $online_listing ) < 1 ) { $online_listing = $this->LANG['none_online']; }
            
            $this->THEME->LANG['whos_online_list'] = $this->SNAP->language_replace( $this->THEME->LANG['whos_online_list'], 'total', number_format( $total_online ) );
            $this->THEME->LANG['whos_online_list'] = $this->SNAP->language_replace( $this->THEME->LANG['whos_online_list'], 'total_members', number_format( $total_members ) );
            $this->THEME->LANG['whos_online_list'] = $this->SNAP->language_replace( $this->THEME->LANG['whos_online_list'], 'total_guests', number_format( $total_guests ) );
            $this->THEME->LANG['whos_online_list'] = $this->SNAP->language_replace( $this->THEME->LANG['whos_online_list'], 'total_anonymous', number_format( $total_anonymous ) );
            $this->THEME->LANG['whos_online_list'] = $this->SNAP->language_replace( $this->THEME->LANG['whos_online_list'], 'total_bots', number_format( $total_bots ) );
            
            $this->SNAP->T = array( 'online_listing' => $online_listing,
                                    'online_link'    => $this->SNAP->seo_url( 'online' ) );
            
            echo $this->THEME->html_whos_online_snapin();
            break;
        }
        
        echo $this->THEME->html_left_island_end();
        
        switch ( $this->SNAP->CFG['islands_enabled'] )
        {
            case 1:
            if ( ( ( ( $this->SNAP->CFG['islands_welcome_enabled'] == 1 ) OR ( $this->SNAP->CFG['islands_latest_topics_enabled'] == 1 ) OR ( $this->SNAP->CFG['islands_community_statistics_enabled'] == 1 ) OR ( $this->SNAP->CFG['islands_statistics_enabled'] == 1 ) ) ) )
            {
                echo $this->THEME->html_right_island_start();
                
                switch ( $this->SNAP->CFG['islands_welcome_enabled'] )
                {
                    case 1:
                    echo $this->THEME->html_islands_welcome();
                    break;
                }
                
                switch ( $this->SNAP->CFG['islands_latest_topics_enabled'] )
                {
                    case 1:
                    $latest_topics = '';
                    $x             = 0;
                                        
                    $r = $this->SNAP->get_data( 'topics', 'topic_id' );
                    
                    if ( $r != false )
                    {
                        foreach ( $r as $k => $v )
                        {
                            $sort['started_timestamp'][$k] = $v['started_timestamp'];
                        }
                        
                        array_multisort( $sort['started_timestamp'], SORT_DESC, $r );
                        
                        foreach ( $r as $k => $v )
                        {
                            $x++;
                            
                            if ( $x > 1 )
                            {
                                $sep = $this->THEME->html_islands_seperator();
                            }
                            else
                            {
                                $sep = '';
                            }
                            
                            if ( $x <= $this->SNAP->CFG['islands_latest_topics_total'] )
                            {
                                if ( $v['member_id'] == 0 )
                                {
                                    $topic_poster = $this->LANG['guest'];
                                    $topic_poster_avatar = $this->SNAP->get_member_avatar( 0, true, false );
                                }
                                else
                                {
                                    $topic_poster = $this->SNAP->get_member_link( $v['member_id'] );
                                    $topic_poster_avatar = $this->SNAP->get_member_link( $v['member_id'], '', '', $this->SNAP->get_member_avatar( $v['member_id'], true, false ) );
                                }
                                
                                $this->SNAP->T = array( 'topic_poster' => $topic_poster,
                                                        'avatar'       => $topic_poster_avatar,
                                                        'topic_title'  => $v['title'],
                                                        'topic_link'   => $this->SNAP->seo_url( 'topic', $v['topic_id'] ),
                                                        'timestamp'    => $this->SNAP->parse_timestamp( $v['started_timestamp'], true ),
                                                        'seperator'    => $sep );
                                                        
                                $latest_topics .= $this->THEME->html_islands_latest_topics_listing();
                            }
                        }
                    }
                    else
                    {
                        $latest_topics = $this->THEME->html_islands_latest_topics_no_topics();
                    }
                    
                    $this->SNAP->T = array( 'topics' => $latest_topics );
                    
                    echo $this->THEME->html_islands_latest_topics();
                    break;
                }
                
                switch ( $this->SNAP->CFG['islands_community_statistics_enabled'] )
                {
                    case 1:
                    $r = $this->SNAP->get_data( 'members', 'member_id' );
                    
                    $total_members = number_format( count( $r ) );
                    
                    $r = $this->SNAP->get_data( 'statistics', 'statistic_id' );
                    
                    if ( $r != false )
                    {
                        foreach ( $r as $k => $v )
                        {
                            if ( $v['statistic_id'] == 1 )
                            {
                                $most_online           = number_format( $v['most_online'] );
                                $most_online_timestamp = $this->SNAP->parse_timestamp( $v['most_online_timestamp'], true );
                                $total_sign_ins        = number_format( $v['total_sign_ins'] );
                                $newest_member         = $this->SNAP->get_member_link( $v['newest_member_id'] );
                                
                                $row = $this->SNAP->get_data( 'members', 'member_id' );
                                
                                if ( $row != false )
                                {
                                    foreach ( $row as $key => $val )
                                    {
                                        if ( $val['member_id'] == $v['newest_member_id'] )
                                        {
                                            $newest_member_joined = $this->SNAP->parse_timestamp( $val['joined'], true );
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    $this->SNAP->T = array( 'most_online'           => $most_online,
                                            'most_online_timestamp' => $most_online_timestamp,
                                            'total_members'         => $total_members,
                                            'newest_member'         => $newest_member,
                                            'newest_member_joined'  => $newest_member_joined,
                                            'total_sign_ins'        => $total_sign_ins );
                                            
                    echo $this->THEME->html_islands_community_statistics();
                    break;
                }
                
                switch ( $this->SNAP->CFG['islands_statistics_enabled'] )
                {
                    case 1:
                    $r = $this->SNAP->get_data( 'topics', 'topic_id' );
                    
                    $total_topics = number_format( count( $r ) );
                    
                    $r = $this->SNAP->get_data( 'posts', 'post_id' );
                    
                    $total_posts = number_format( count( $r ) );
                    
                    $this->SNAP->T = array( 'total_topics' => $total_topics,
                                            'total_posts'  => $total_posts );
                                            
                    echo $this->THEME->html_islands_bulletin_board_statistics();
                    break;
                }
                
                echo $this->THEME->html_right_island_end();
            }
            break;
        }
        
        switch ( $this->SNAP->CFG['islands_enabled'] )
        {
            case 1:
            $islands_end = $this->THEME->html_islands_end();
            break;
            
            case 0:
            $islands_end = '';
            break;
        }
       
        $this->SNAP->T = array( 'islands_end' => $islands_end );
        
        echo $this->THEME->html_index_end();
        
        $this->SNAP->footer();
    }
}

?>