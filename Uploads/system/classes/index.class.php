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
        echo 'If you see this, then it is working... Your IP is: ' . $this->SNAP->AGENT['ip'] . ' and your hostname: ' . $this->SNAP->AGENT['hostname'] . '<br><br>Browser is: ' . $this->SNAP->AGENT['browser_title'] . ' and user agent is: ' . $this->SNAP->AGENT['agent'];
    }
}

?>