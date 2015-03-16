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

class OHSNAPSQL {
    
public $SNAP;
    
public function sql_fetch_stored_cache()
{
return <<<EOF
SELECT * FROM {$this->SNAP->db_prefix}stored_cache
EOF;
}

public function sql_fetch_forums_with_depth()
{
return <<<EOF
SELECT node.*, (COUNT(parent.title) - 1) AS depth
FROM {$this->SNAP->db_prefix}forums AS node, 
{$this->SNAP->db_prefix}forums AS parent 
WHERE node.lft BETWEEN parent.lft AND parent.rgt 
GROUP BY node.title 
ORDER BY node.lft
EOF;
}

public function sql_fetch_for_cache()
{
return <<<EOF
SELECT * FROM {$this->SNAP->db_prefix}{$this->SNAP->TOSQL['table']}{$this->SNAP->TOSQL['sorting']}
EOF;
}

public function sql_update_cache_data()
{
return <<<EOF
UPDATE {$this->SNAP->db_prefix}stored_cache 
SET 
data = '{$this->SNAP->TOSQL['to_cache']}' 
WHERE title = '{$this->SNAP->TOSQL['title']}'
EOF;
}

public function sql_fetch_sessions_member_id()
{
return <<<EOF
SELECT * FROM {$this->SNAP->db_prefix}sessions 
WHERE member_id = '{$this->SNAP->TOSQL['member_id']}'
EOF;
}

public function sql_update_member_clear_token()
{
return <<<EOF
UPDATE {$this->SNAP->db_prefix}members 
SET 
token = '' 
WHERE member_id = '{$this->SNAP->TOSQL['member_id']}'
EOF;
}

public function sql_fetch_sessions_from_id()
{
return <<<EOF
SELECT * FROM {$this->SNAP->db_prefix}sessions 
WHERE session_id = '{$this->SNAP->SESSION['id']}'
EOF;
}

public function sql_insert_session_new()
{
return <<<EOF
INSERT INTO {$this->SNAP->db_prefix}sessions 
VALUES ( 
'{$this->SNAP->SESSION['id']}',
'{$this->SNAP->MEMBER['id']}',
'{$this->SNAP->MEMBER['username']}',
'{$this->SNAP->SESSION['expires']}',
'{$this->SNAP->SESSION['last_click']}',
'{$this->SNAP->SESSION['location']}',
'{$this->SNAP->AGENT['ip']}',
'{$this->SNAP->AGENT['agent']}',
'{$this->SNAP->AGENT['hostname']}',
'{$this->SNAP->MEMBER['anonymous']}',
'{$this->SNAP->SESSION['search_bot']}',
'{$this->SNAP->SESSION['search_bot_name']}',
'{$thos->SNAP->SESSION['admin_session']}'
)
EOF;
}

public function sql_update_session()
{
return <<<EOF
UPDATE {$this->SNAP->db_prefix}sessions 
SET 
expires = '{$this->SNAP->SESSION['expires']}',
last_click = '{$this->SNAP->SESSION['last_click']}',
location = '{$this->SNAP->SESSION['location']}' 
WHERE session_id = '{$this->SNAP->SESSION['id']}'
EOF;
}

public function sql_delete_session()
{
return <<<EOF
DELETE FROM {$this->SNAP->db_prefix}sessions 
WHERE session_id = '{$this->SNAP->SESSION['id']}'
EOF;
}

public function sql_delete_session_gc()
{
return <<<EOF
DELETE FROM {$this->SNAP->db_prefix}sessions 
WHERE expires < '{$this->SNAP->TOSQL['time']}'
EOF;
}

public function sql_fetch_session_data_from_store()
{
return <<<EOF
SELECT data FROM {$this->SNAP->db_prefix}session_store 
WHERE session_id = '{$this->SNAP->TOSQL['id']}' 
AND expires > '{$this->SNAP->TOSQL['time']}'
EOF;
}

public function sql_fetch_session_from_store()
{
return <<<EOF
SELECT session_id FROM {$this->SNAP->db_prefix}session_store 
WHERE session_id = '{$this->SNAP->TOSQL['id']}'
EOF;
}

public function sql_insert_session_store_new()
{
return <<<EOF
INSERT INTO {$this->SNAP->db_prefix}session_store 
VALUES (
'{$this->SNAP->TOSQL['id']}',
'{$this->SNAP->TOSQL['data']}',
'{$this->SNAP->TOSQL['lifetime']}'
)
EOF;
}

public function sql_update_session_store_data()
{
return <<<EOF
UPDATE {$this->SNAP->db_prefix}session_store 
SET 
session_id = '{$this->SNAP->TOSQL['id']}',
data = '{$this->SNAP->TOSQL['data']}',
expires = '{$this->SNAP->TOSQL['time']}' 
WHERE session_id = '{$this->SNAP->TOSQL['id']}'
EOF;
}

public function sql_delete_session_store()
{
return <<<EOF
DELETE FROM {$this->SNAP->db_prefix}session_store 
WHERE session_id = '{$this->SNAP->TOSQL['id']}'
EOF;
}

public function sql_delete_session_store_gc()
{
return <<<EOF
DELETE FROM {$this->SNAP->db_prefix}session_store 
WHERE expires < UNIX_TIMESTAMP();
EOF;
}

public function sql_select_session_whos_online_snapin()
{
return <<<EOF
SELECT * FROM {$this->SNAP->db_prefix}sessions 
ORDER BY last_click DESC
EOF;
}

public function sql_update_members_sign_in_token()
{
return <<<EOF
UPDATE {$this->SNAP->db_prefix}members 
SET 
token = '{$this->SNAP->TOSQL['token']}',
last_sign_in = '{$this->SNAP->TOSQL['time']}'
WHERE member_id = '{$this->SNAP->TOSQL['id']}'
EOF;
}

public function sql_update_session_new_sign_in()
{
return <<<EOF
UPDATE {$this->SNAP->db_prefix}sessions
SET
member_id = '{$this->SNAP->TOSQL['id']}',
member_username = '{$this->SNAP->TOSQL['username']}',
expires = '{$this->SNAP->TOSQL['expires']}',
last_click = '{$this->SNAP->TOSQL['last_click']}',
location = '{$this->SNAP->TOSQL['location']}',
anonymous = '{$this->SNAP->TOSQL['anonymous']}'
WHERE session_id = '{$this->SNAP->TOSQL['session_id']}'

EOF;
}

public function sql_update_statistics_sign_ins()
{
return <<<EOF
UPDATE {$this->SNAP->db_prefix}statistics
SET
total_sign_ins = '{$this->SNAP->TOSQL['total']}'
WHERE statistic_id = '1'
EOF;
}

public function sql_update_members_remove_token()
{
return <<<EOF
UPDATE {$this->SNAP->db_prefix}members
SET
token = ''
WHERE member_id = '{$this->SNAP->TOSQL['id']}'
EOF;
}

}

?>