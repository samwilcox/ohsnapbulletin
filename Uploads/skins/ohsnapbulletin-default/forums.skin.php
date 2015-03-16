<?php

class OhSnapSkinForums {

public $SNAP;
public $LANG = array();

public function skin_category_footer()
{
return <<<EOF
</div>
<div class="smlSeperator"></div>
EOF;
}

public function skin_category_header()
{
return <<<EOF
   	  <div class="contentTitle">
        	<div class="contentTitleLeft">{$this->SNAP->T['title']}</div>
            <div class="contentTitleRight" id="id-cat-{$this->SNAP->T['id']}-i"><a href="javascript:void(0);" title="{$this->LANG['collapse']}" onClick="toggle('id-cat-{$this->SNAP->T['id']}-i','id-cat-{$this->SNAP->T['id']}-b');"><img src="{$this->SNAP->imageset_url}/icons/collapse.png" alt="*" class="imgNoBorder"></a></div>
      </div>
      <div id="id-cat-{$this->SNAP->T['id']}-b" class="toggleOff">
      <div class="contentSubTitle">
      	<div class="forumTitleInfo">{$this->LANG['forum_info']}</div>
        <div class="forumTitleTopics">{$this->LANG['topics']}</div>
        <div class="forumTitleReplies">{$this->LANG['replies']}</div>
        <div class="forumTitleLastPost">{$this->LANG['last_post']}</div>
      </div>
EOF;
}

public function skin_forum_image()
{
return <<<EOF
<img src="{$this->SNAP->T['image']}" alt="*" class="imgAlign"> 
EOF;
}

public function skin_forum_redirect_listing()
{
return <<<EOF
      <div class="forumTableContainer">
      	<div class="forumIcon"><img src="{$this->SNAP->imageset_url}/icons/status-icons/forum/{$this->SNAP->CFG['forum_status_redirect_icon']}" alt="*" title="{$this->LANG['redirect_info']}"></div>
        <div class="forumInfo"><span class="forumNameLink">{$this->SNAP->T['image']}<a href="{$this->SNAP->T['forum_link']}" title="{$this->SNAP->T['title']}">{$this->SNAP->T['title']}</a></span><br>
          <span class="forumDesc">{$this->SNAP->T['description']}</span></div>
        <div class="forumTopics">---</div>
        <div class="forumReplies">---</div>
        <div class="forumLastPost">{$this->SNAP->T['redirects']}</div>
      </div>
EOF;
}

public function skin_forum_status_icon()
{
return <<<EOF
<img src="{$this->SNAP->imageset_url}/icons/status-icons/forum/{$this->SNAP->T['icon']}" alt="*" title="{$this->SNAP->T['title']}">
EOF;
}

public function skin_sub_forums_start()
{
return <<<EOF
<br><span class="subForumLinkText"><strong>{$this->LANG['sub_forums']}</strong> 
EOF;
}

public function skin_sub_forums_listing()
{
return <<<EOF
{$this->SNAP->T['sep']}<a href="{$this->SNAP->T['forum_link']}" title="{$this->SNAP->T['title']}">{$this->SNAP->T['title']}</a>
EOF;
}

public function skin_sub_forums_end()
{
return <<<EOF
</span>
EOF;
}

public function skin_forum_last_post()
{
return <<<EOF
<div class="forumLastPostAvatar">{$this->SNAP->T['avatar']}</div>
<div class="forumLastPostInfo"><a href="{$this->SNAP->T['topic_link']}" title="{$this->SNAP->T['topic_title']}">{$this->SNAP->T['topic_title']}</a> <a href="{$this->SNAP->T['topic_lp_link']}" title="{$this->LANG['go_to_last_post']}"><img src="{$this->SNAP->imageset_url}/icons/go-to-post.png" width="16" height="16" alt="*" class="imgAlign"></a><br>
{$this->SNAP->T['member_link']}<br>
<span class="timestampTxt">{$this->SNAP->T['timestamp']}</span>
</div>
EOF;
}

public function skin_forum_listing()
{
return <<<EOF
      <div class="forumTableContainer">
      	<div class="forumIcon">{$this->SNAP->T['icon']}</div>
        <div class="forumInfo"><span class="forumNameLink">{$this->SNAP->T['image']}<a href="{$this->SNAP->T['forum_link']}" title="{$this->SNAP->T['title']}">{$this->SNAP->T['title']}</a></span><br>
          <span class="forumDesc">{$this->SNAP->T['description']}</span>{$this->SNAP->T['sub_forums']}</div>
        <div class="forumTopics">{$this->SNAP->T['total_topics']}</div>
        <div class="forumReplies">{$this->SNAP->T['total_replies']}</div>
        <div class="forumLastPost">
        	{$this->SNAP->T['last_post']}
        </div>
      </div>
EOF;
}

public function skin_bbic_start()
{
return <<<EOF
      <div class="bigSeperator"></div>
      <div class="contentTitle">
      	<div class="contentTitleLeft">{$this->LANG['bbic']}</div>
        <div class="contentTitleRight" id="id-bbic-i"><a href="javascript:void(0);" title="{$this->LANG['collapse']}" onClick="toggle('id-bbic-i','id-bbic-b');"><img src="{$this->SNAP->imageset_url}/icons/collapse.png" alt="*" class="imgNoBorder"></a></div>
      </div>
      <div id="id-bbic-b" class="toggleOff">
      <div class="contentSubTitle">
      	<div class="bbicSubLeft"><a href="{$this->SNAP->T['online_link']}" title="{$this->LANG['whos_online_info']}">{$this->LANG['whos_online']}</a></div>
        <div class="bbicSubMiddle">{$this->LANG['stats']}</div>
        <div class="bbicSubRight"><a href="{$this->SNAP->wrapper}?action=calendar&amp;do=view&amp;year={$this->SNAP->T['year']}&amp;month={$this->SNAP->T['month']}&amp;day={$this->SNAP->T['day']}" title="{$this->LANG['birthdays_info']}">{$this->LANG['birthdays']}</a></div>
      </div>
      <div class="bbicContainer">
EOF;
}

public function skin_whos_online_snapin()
{
return <<<EOF
      	<div class="bbicLeft">
        	<div class="bbicInsideLeft"><img src="{$this->SNAP->imageset_url}/icons/bbic-online.png" width="32" height="32" alt="*"></div>
          <div class="bbicInsideRight">
            	<div class="halfLeft">{$this->LANG['online_now']}</div>
                <div class="halfRight">{$this->LANG['accurate']}</div>
            <div class="clear"></div>
            {$this->LANG['counts']}<br>
            <br>
          {$this->SNAP->T['listing']}</div>
        </div>
EOF;
}

public function skin_statistics_snapin()
{
return <<<EOF
        <div class="bbicMiddle">
        	<div class="bbicInsideLeft"><img src="{$this->SNAP->imageset_url}/icons/bbic-stats.png" width="32" height="32" alt="*"></div>
            <div class="bbicInsideRight">{$this->LANG['welcome_member']}<br>
            {$this->LANG['total_members']}<br>
            {$this->LANG['total_posts']}<br>
            {$this->LANG['most_users']}<br>
            {$this->LANG['users_today']}</div>
        </div>
EOF;
}

public function skin_years_old()
{
return <<<EOF
 <span class="questionMark" title="{$this->LANG['years_old']}">(<strong>{$this->SNAP->T['age']}</strong>)</span>
EOF;
}

public function skin_birthdays_snapin()
{
return <<<EOF
        <div class="bbicRight">
        	<div class="bbicInsideLeft"><img src="{$this->SNAP->imageset_url}/icons/bbic-birthdays.png" width="32" height="32" alt="*"></div>
            <div class="bbicInsideRight">{$this->LANG['members_celebrating']}<br>
              <br>
            {$this->SNAP->T['listing']}</div>
        </div>
EOF;
}

public function skin_bbic_end()
{
return <<<EOF
</div>
EOF;
}

}

?>