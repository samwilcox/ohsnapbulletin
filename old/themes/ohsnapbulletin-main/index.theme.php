<?php

class OHSNAPThemeIndex
{

public $SNAP;
public $LANG;

public function html_islands_js()
{
return <<<EOF
		  function toggle_island()
		  {
			  if ($("#l_island").hasClass('islandTableLeftFull'))
			  {
				  	$("#r_island").fadeIn('slow');
					$("#l_island").removeClass('islandTableLeftFull');
					$("#l_island").addClass('islandTableLeft');
					$("#l_island").fadeIn('slow').delay(300);
					$("#island_toggle_img").html('<a href="javascript:void();" title="{$this->LANG['collapse_right_island']}" onClick="toggle_island();"><img src="{$this->SNAP->imageset_url}/icons/close-island.png" alt="{$this->LANG['collapse_right_island']}"></a>');
			  }
			  else
			  {
				  	$("#r_island").fadeOut('slow');
					$("#l_island").removeClass('islandTableLeft');
					$("#l_island").addClass('islandTableLeftFull');
					$("#l_island").fadeIn('slow');
					$("#island_toggle_img").html('<a href="javascript:void();" title="{$this->LANG['expand_right_island']}" onClick="toggle_island();"><img src="{$this->SNAP->imageset_url}/icons/open-island.png" alt="{$this->LANG['expand_right_island']}"></a>');
			  }
		  }
          
EOF;
}

public function html_left_island_start()
{
return <<<EOF
          <div class="islandContainer">
        <table class="islandTable" cellspacing="0" cellpadding="0">
          <tr>
            <td class="islandTableLeft" valign="top" id="l_island">
          <div class="islandLeft" id="left_island"><span class="islandToggle" id="island_toggle_img"><a href="javascript:void();" title="{$this->LANG['collapse_right_island']}" onClick="toggle_island();"><img src="{$this->SNAP->imageset_url}/icons/close-island.png"  alt="{$this->LANG['collapse_right_island']}"></a></span>
EOF;
}

public function html_left_island_class()
{
return <<<EOF
class="islandLeft"
EOF;
}

public function html_no_islands_class()
{
return <<<EOF
class="islandLeftFull"
EOF;
}

public function html_index_start()
{
return <<<EOF
        <script language="javascript" type="text/javascript">
		  {$this->SNAP->T['islands_js']}		  
		  function toggle_me(eb,ei,et)
		  {
			  if ($("#" + eb).hasClass('hidden'))
			  {
				  $("#" + et).css('margin-bottom','0px');
				  $("#" + eb).fadeIn();
				  $("#" + eb).removeClass('hidden');
				  $("#" + ei).html('<a href="javascript:void();" title="{$this->LANG['collapse']}" onClick="toggle_me(\'' + eb + '\',\'' + ei + '\',\'' + et + '\');"><img src="{$this->SNAP->imageset_url}/icons/collapse.png" alt="{$this->LANG['collapse']}" class="imgNoBorder"></a>');
			  }
			  else
			  {
				  $("#" + eb).fadeOut();
				  $("#" + eb).addClass('hidden');
				  $("#" + ei).html('<a href="javascript:void();" title="{$this->LANG['expand']}" onClick="toggle_me(\'' + eb + '\',\'' + ei + '\',\'' + et + '\');"><img src="{$this->SNAP->imageset_url}/icons/expand.png" alt="{$this->LANG['expand']}" class="imgNoBorder"></a>');
				  $("#" + et).css('margin-bottom','12px');
			  }
		  }
		  </script>
          {$this->SNAP->T['left_island_start']}
EOF;
}

public function html_category_header()
{
return <<<EOF
          	<div class="mainContentTitle" id="cat_{$this->SNAP->T['id']}_title">
            	<div class="mainContentTitleLeft">{$this->SNAP->T['name']}</div>
                <div class="mainContentTitleRight" id="cat_{$this->SNAP->T['id']}_img"><a href="javascript:void();" title="{$this->LANG['collapse']}" onClick="toggle_me('cat_{$this->SNAP->T['id']}_body','cat_{$this->SNAP->T['id']}_img','cat_{$this->SNAP->T['id']}_title');"><img src="{$this->SNAP->imageset_url}/icons/collapse.png" alt="{$this->LANG['collapse']}" class="imgNoBorder"></a></div>
                <div class="clear"></div>
            </div> 
            <div id="cat_{$this->SNAP->T['id']}_body">
            <div class="mainContent">
EOF;
}

public function html_forum_image()
{
return <<<EOF
<img src="{$this->SNAP->T['image']}" alt="*" class="imgAlign"> 
EOF;
}

public function html_forum_redirect_listing()
{
return <<<EOF
              <div class="forumBubble">
           	    <table width="100%" border="0" cellpadding="0" cellspacing="0">
           	      <tr>
           	        <td class="forumBubbleIcon"><img src="{$this->SNAP->imageset_url}/icons/status/forums/{$this->SNAP->CFG['img_redirect']}" alt="{$this->LANG['redirect_img']}" title="{$this->LANG['redirect_img']}"></td>
           	        <td class="forumBubbleInfo"><span class="forumBubbleName">{$this->SNAP->T['image']}<a href="{$this->SNAP->T['forum_link']}" title="{$this->SNAP->T['title']}">{$this->SNAP->T['title']}</a></span><br>
       	            {$this->SNAP->T['description']}</td>
           	        <td class="forumBubbleTopics">&nbsp;</td>
           	        <td class="forumBubbleReplies">{$this->SNAP->T['hits']}<br>
       	            <span class="forumBubbleStatsSmlTxt">{$this->LANG['clicks']}</span></td>
           	        <td class="forumBubbleLastPost"><em>{$this->LANG['redirect_info']}</em></td>
       	          </tr>
       	        </table>
           	  </div>
EOF;
}

public function html_forum_archived_icon()
{
return <<<EOF
<img src="{$this->SNAP->imageset_url}/icons/status/forums/{$this->SNAP->CFG['img_forum_archived']}" alt="{$this->LANG['archived_img']}" title="{$this->LANG['archived_img']}">
EOF;
}

public function html_forum_no_unread_icon()
{
return <<<EOF
<img src="{$this->SNAP->imageset_url}/icons/status/forums/{$this->SNAP->CFG['img_forum_no_unread']}" alt="{$this->LANG['no_new_posts']}" title="{$this->LANG['no_new_posts']}">
EOF;
}

public function html_forum_unread_icon()
{
return <<<EOF
<img src="{$this->SNAP->imageset_url}/icons/status/forums/{$this->SNAP->CFG['img_forum_unread']}" alt="{$this->LANG['new_posts']}" title="{$this->LANG['new_posts']}">
EOF;
}

public function html_sub_forums_start()
{
return <<<EOF
<br><span class="forumBubbleSubForums"><strong>{$this->LANG['sub_forums']}</strong> 
EOF;
}

public function html_sub_forums_listing()
{
return <<<EOF
{$this->SNAP->T['seperator']}<a href="{$this->SNAP->T['forum_link']}" title="{$this->SNAP->T['title']}">{$this->SNAP->T['title']}</a>
EOF;
}

public function html_last_post_information()
{
return <<<EOF
{$this->SNAP->T['avatar']}<a href="{$this->SNAP->T['topic_link']}" title="{$this->SNAP->T['topic_title']}">{$this->SNAP->T['topic_title']}</a> <a href="{$this->SNAP->T['topic_lp_link']}" title="{$this->LANG['go_to_last_post']}"><img src="{$this->SNAP->imageset_url}/icons/go-last-post.png" width="8" height="8" alt="*" class="imgTop"></a><br>
<strong>{$this->LANG['by']}</strong> {$this->SNAP->T['member_link']}<br>
{$this->SNAP->T['timestamp']}
EOF;
}

public function html_forum_listing()
{
return <<<EOF
            <div class="forumBubble">
           	    <table width="100%" border="0" cellpadding="0" cellspacing="0">
           	      <tr>
           	        <td class="forumBubbleIcon">{$this->SNAP->T['icon']}</td>
           	        <td class="forumBubbleInfo"><span class="forumBubbleName">{$this->SNAP->T['image']}<a href="{$this->SNAP->T['link']}" title="{$this->SNAP->T['title']}">{$this->SNAP->T['title']}</a></span><br>
       	            {$this->SNAP->T['description']}{$this->SNAP->T['sub_forums']}</td>
           	        <td class="forumBubbleTopics">{$this->SNAP->T['total_topics']}<br>
       	            <span class="forumBubbleStatsSmlTxt">{$this->LANG['topics']}</span></td>
           	        <td class="forumBubbleReplies">{$this->SNAP->T['total_replies']}<br>
           	          <span class="forumBubbleStatsSmlTxt">{$this->LANG['replies']}</span></td>
           	        <td class="forumBubbleLastPost">{$this->SNAP->T['last_post']}</td>
       	          </tr>
       	        </table>
           	  </div>
EOF;
}

public function html_category_footer()
{
return <<<EOF
            </div>
            </div>
EOF;
}

public function html_forum_seperator()
{
return <<<EOF
<div class="forumSeperator"></div>
EOF;
}

public function html_whos_online_snapin()
{
return <<<EOF
            <div class="mainContentNoTitle">
            	<div class="mainContentBubble">
                	<div class="whosOnlineTitle"><span class="whosOnlineLink"><a href="{$this->SNAP->T['online_link']}" title="{$this->LANG['whos_online_view']}">{$this->LANG['whos_online']}</a></span> ({$this->LANG['whos_online_list']})</div>
                    <div class="whosOnlineContent">{$this->SNAP->T['online_listing']}</div>
                </div>
            </div>
EOF;
}

public function html_left_island_end()
{
return <<<EOF
            </div>
            </td>
EOF;
}

public function html_right_island_start()
{
return <<<EOF
            <td class="islandTableRight" valign="top" id="r_island">
            <div class="islandRight" id="right_island">
            	<div class="rightIslandInsider">
EOF;
}

public function html_right_island_end()
{
return <<<EOF
                </div>
        </div>
            </td>
          </tr>
EOF;
}

public function html_islands_welcome()
{
return <<<EOF
                	<div class="rightIslandTitle">{$this->SNAP->CFG['community_welcome_title']}</div>
                    <div class="rightIslandContent">{$this->SNAP->CFG['community_welcome_message']}</div>
EOF;
}

public function html_islands_seperator()
{
return <<<EOF
                        <div class="rightIslandContentDivider"></div>
EOF;
}

public function html_islands_latest_topics_listing()
{
return <<<EOF
                        {$this->SNAP->T['seperator']}
                    	<div class="rightIslandContentDiv">
                        	<div class="rightIslandLatestTopicsLeft">{$this->SNAP->T['avatar']}</div>
                            <div class="rightIslandLatestTopicsRight"><a href="{$this->SNAP->T['topic_link']}" title="{$this->SNAP->T['topic_title']}">{$this->SNAP->T['topic_title']}</a><br>
                              <strong>{$this->LANG['by']}</strong> {$this->SNAP->T['topic_poster']}<br>
                              <span class="rightIslandDateTime">{$this->SNAP->T['timestamp']}</span>
                            </div>
                            <div class="clear"></div>
                        </div>
EOF;
}

public function html_islands_latest_topics_no_topics()
{
return <<<EOF
                        <div class="rightIslandContentDiv">
                            {$this->LANG['no_latest_topics']}
                        </div>
EOF;
}

public function html_islands_latest_topics()
{
return <<<EOF
                	<div class="rightIslandTitle">Latest Topics</div>
                    <div class="rightIslandContent">
                    	{$this->SNAP->T['topics']}
                    </div>
EOF;
}

public function html_islands_community_statistics()
{
return <<<EOF
                    <div class="rightIslandTitle">{$this->LANG['community_statistics']}</div>
                    <div class="rightIslandContent">
                    	<div class="rightIslandContentDiv">
                        	<div class="rightIslandStatsLeft">{$this->LANG['most_online']}</div>
                            <div class="rightIslandStatsRight">{$this->SNAP->T['most_online']}<br>
                              <span class="rightIslandDateTime">{$this->SNAP->T['most_online_timestamp']}</span></div>
                            <div class="clear"></div>
                        </div>
                        <div class="rightIslandContentDivider"></div>
                        <div class="rightIslandContentDiv">
                        	<div class="rightIslandStatsLeft">{$this->LANG['total_members']}</div>
                            <div class="rightIslandStatsRight">{$this->SNAP->T['total_members']}</div>
                            <div class="clear"></div>
                        </div>
                        <div class="rightIslandContentDivider"></div>
                        <div class="rightIslandContentDiv">
                        	<div class="rightIslandStatsLeft">{$this->LANG['newest_member']}</div>
                            <div class="rightIslandStatsRight">{$this->SNAP->T['newest_member']}<br>
                            <span class="rightIslandDateTime">{$this->SNAP->T['newest_member_joined']}</span></div>
                            <div class="clear"></div>
                        </div>
                        <div class="rightIslandContentDivider"></div>
                        <div class="rightIslandContentDiv">
                        	<div class="rightIslandStatsLeft">{$this->LANG['total_sign_ins']}</div>
                            <div class="rightIslandStatsRight">{$this->SNAP->T['total_sign_ins']}</div>
                            <div class="clear"></div>
                        </div>
                    </div>
EOF;
}

public function html_islands_bulletin_board_statistics()
{
return <<<EOF
                    <div class="rightIslandTitle">{$this->LANG['bb_statistics']}</div>
                    <div class="rightIslandContent">
                    	<div class="rightIslandContentDiv">
                        	<div class="rightIslandStatsLeft">{$this->LANG['total_topics']}</div>
                            <div class="rightIslandStatsRight">{$this->SNAP->T['total_topics']}</div>
                            <div class="clear"></div>
                        </div>
                        <div class="rightIslandContentDivider"></div>
                        <div class="rightIslandContentDiv">
                        	<div class="rightIslandStatsLeft">{$this->LANG['total_posts']}</div>
                            <div class="rightIslandStatsRight">{$this->SNAP->T['total_posts']}</div>
                            <div class="clear"></div>
                        </div>
                    </div>
EOF;
}

public function html_index_end()
{
return <<<EOF
        </tr>
        </table>
        {$this->SNAP->T['islands_end']}
EOF;
}

public function html_islands_end()
{
return <<<EOF
        </div>
        </div>
EOF;
}

}

?>