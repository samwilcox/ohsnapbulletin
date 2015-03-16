<?php

class OhSnapSkinGlobal {

public $SNAP;
public $LANG = array();

public function skin_calendar_link()
{
return <<<EOF
<li><a href="{$this->SNAP->T['calendar_link']}" title="{$this->LANG['calendar']}">{$this->LANG['calendar']}</a></li>
EOF;
}

public function skin_gallery_link()
{
return <<<EOF
<li><a href="{$this->SNAP->T['gallery_link']}" title="{$this->LANG['gallery']}">{$this->LANG['gallery']}</a></li>
EOF;
}

public function skin_redirect_meta()
{
return <<<EOF
<META http-equiv="refresh" content="5;URL={$this->SNAP->T['url']}">
EOF;
}

public function skin_skn_lng_popout_option()
{
return <<<EOF
<a href="{$this->SNAP->wrapper}?action=toggle&amp;do=skin&amp;id={$this->SNAP->T['id']}" title="{$this->SNAP->T['name']}">{$this->SNAP->T['name']}</a>
EOF;
}

public function skin_first_option_class()
{
return <<<EOF
popOutFirstItem
EOF;
}

public function skin_option_class()
{
return <<<EOF
popOutItems
EOF;
}

public function skin_option_checkmark()
{
return <<<EOF
<img src="{$this->SNAP->imageset_url}/icons/checkmark.png" class="imgAlign" alt="*"> 
EOF;
}

public function skin_skin_option()
{
return <<<EOF
<span class="{$this->SNAP->T['item_class']}"><a href="{$this->SNAP->wrapper}?action=toggle&amp;do=skin&amp;id={$this->SNAP->T['id']}" title="{$this->SNAP->T['title']}">{$this->SNAP->T['checkmark']} {$this->SNAP->T['title']}</a></span>
EOF;
}

public function skin_language_option()
{
return <<<EOF
<span class="{$this->SNAP->T['item_class']}"><a href="{$this->SNAP->wrapper}?action=toggle&amp;do=skin&amp;id={$this->SNAP->T['id']}" title="{$this->SNAP->T['title']}">{$this->SNAP->T['checkmark']}<img src="{$this->SNAP->T['flag_icon']}" class="imgAlignMiddle" alt="*"> {$this->SNAP->T['title']}</a></span>
EOF;
}

public function skin_navigation_tree()
{
return <<<EOF
<img src="{$this->SNAP->imageset_url}/icons/nav-folder.png" alt="*" class="imgAlign"> <a href="{$this->SNAP->T['forums_link']}" title="{$this->SNAP->CFG['application_title']}">{$this->SNAP->CFG['application_title']}</a> / {$this->SNAP->T['nav']}
EOF;
}

public function skin_member_link()
{
return <<<EOF
{$this->SNAP->T['seperator']}<a href="{$this->SNAP->T['member_link']}" title="{$this->SNAP->T['title']}">{$this->SNAP->T['display_name']}</a>
EOF;
}

public function skin_notification_item()
{
return <<<EOF
<a href="{$this->SNAP->T['notification_link']}" title="{$this->SNAP->T['title']}">{$this->SNAP->T['title']}</a>
EOF;
}

public function skin_notification_item_none()
{
return <<<EOF
<a href="{$this->SNAP->T['notification_link']}" title="{$this->LANG['no_notifications']}">{$this->LANG['no_notifications']}</a>
EOF;
}

public function skin_notification_icon()
{
return <<<EOF
<img src="{$this->SNAP->imageset_url}/icons/notifications.png" alt="*" class="notificationIcon">
EOF;
}

public function skin_notification_icon_new()
{
return <<<EOF
<img src="{$this->SNAP->imageset_url}/icons/notifications-new.png" alt="*" class="notificationIcon">
EOF;
}

public function skin_avatar_thumb_no_limits()
{
return <<<EOF
<img src="{$this->SNAP->T['filename']}" class="memberAvatarThumb" alt="*">
EOF;
}

public function skin_avatar_thumb_limits()
{
return <<<EOF
<img src="{$this->SNAP->T['filename']}" class="memberAvatarThumb" alt="*" width="{$this->SNAP->T['width']}" height="{$this->SNAP->T['height']}">
EOF;
}

public function skin_avatar_no_limits()
{
return <<<EOF
<img src="{$this->SNAP->T['filename']}" class="memberAvatar" alt="*">
EOF;
}

public function skin_avatar_limits()
{
return <<<EOF
<img src="{$this->SNAP->T['filename']}" class="memberAvatar" alt="*" width="{$this->SNAP->T['width']}" height="{$this->SNAP->T['height']}">
EOF;
}

public function skin_avatar_facebook_no_limits()
{
return <<<EOF
<fb:profile-pic uid="{$this->SNAP->T['fb_id']}" size="normal" facebook-logo="true"></fb:profile-pic>
EOF;
}

public function skin_avatar_facebook_limits()
{
return <<<EOF
<fb:profile-pic uid="{$this->SNAP->T['fb_id']}" size="normal" width="{$this->SNAP->T['width']}" facebook-logo="true"></fb:profile-pic>
EOF;
}

public function skin_guest_header()
{
return <<<EOF
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>{$this->SNAP->T['title']}{$this->SNAP->CFG['application_title']}</title>
<link href="{$this->SNAP->skin_url}/css/index.css" rel="stylesheet" type="text/css" media="screen">
<script type="application/javascript" src="{$this->SNAP->base_url}/public/jscripts/jquery-1.11.2.min.js"></script>
<script type="application/javascript" src="{$this->SNAP->base_url}/public/jscripts/jquery-ui.min.js"></script>

<script type="application/javascript">
$(document).ready(function(e) {
    $("#id-dialog-bg").click(function(e) {
        $("div:visible[id*='dialog']").fadeOut(700);
        $("#id-dialog-bg").fadeOut(700);
    });
});

$(document).click(function(e) {
    if (e.target.id != $("div:visible[id*='slnk']").attr('id') && !$("div:visible[id*='slnk']").find(e.target).length) {
		$("div:visible[id*='popout']").fadeOut(300);
	}
});

function toggle( icn, bdy )
{
	if ( $("#" + bdy).hasClass("toggleOn") )
	{
		$("#" + bdy).removeClass("toggleOn");
		$("#" + bdy).addClass("toggleOff");
		$("#" + bdy).fadeIn(700);
		$("#" + icn).html('<a href="javascript:void(0);" title="{$this->LANG['collapse']}" onclick="toggle(\'' + icn + '\',\'' + bdy + '\');"><img src="{$this->SNAP->imageset_url}/icons/collapse.png" alt="*" /></a>');
	}
	else
	{
		$("#" + bdy).removeClass("toggleOff");
		$("#" + bdy).addClass("toggleOn");
		$("#" + bdy).fadeOut(700);
		$("#" + icn).html('<a href="javascript:void(0);" title="{$this->LANG['expand']}" onclick="toggle(\'' + icn + '\',\'' + bdy + '\');"><img src="{$this->SNAP->imageset_url}/icons/expand.png" alt="*" /></a>');
	}
}

function scrollToTop()
{
	event.preventDefault();
	
	$("html, body").animate({
		scrollTop:0
	}, "slow");
}

function togglePopOut(pu,lk)
{	
    $("div:visible[id*='popout']").fadeOut(300);
    
	$("#" + pu).fadeToggle(300).position({
		my:       "left top",
		at:       "left bottom",
		of:       $("#" + lk),
		collison: "fit"
	});
}

function openDialog(el)
{
	$("#id-dialog-bg").fadeIn(700);
	$("#" + el).fadeIn(700);
	
	var elHeight = $("#" + el).height();
	var elHeight = (elHeight / 2);
	$("#" + el).css({"margin-top":-elHeight});
	
	return false;
}
</script>

</head>

<body>
<!-- START PopOut Menus //-->
<div class="skinLangPopOut" id="id-skin-popout">
	<div class="popOutContainer">{$this->SNAP->T['skin_options']}</div>
</div>

<div class="skinLangPopOut" id="id-lang-popout">
	<div class="popOutContainer">{$this->SNAP->T['language_options']}</div>
</div>
<!-- END PopOut Menus //-->

<!-- START PopUp Dialogs //-->
<div class="popUpBackground" id="id-dialog-bg"></div>
<div class="signInPopUp" id="id-signin-dialog">
	<div class="popUpContainer">
    	<div class="contentTitlePopUp">{$this->LANG['member_signin']}</div>
      <div class="signInPopUpBody">
        	<div class="signInPopUpLeft">
        	  <form name="form1" method="post" action="{$this->SNAP->wrapper}">
        	    <input name="username" type="text" class="longTextField" id="username" placeholder="{$this->LANG['username_email']}" maxlength="32">
        	    <br>
        	    <br>
        	    <input name="password" type="password" class="longTextField" id="password" placeholder="{$this->LANG['password']}" maxlength="32">
        	    <br>
                <br>
                <input name="rememberme" type="checkbox" id="rememberme" value="1" checked><label for="rememberme">{$this->LANG['remember_me']}</label>
                <br>
                <input name="anonymous" type="checkbox" id="anonymous" value="1"><label for="anonymous">{$this->LANG['signin_anonymous']}</label>
                <br>
        	    <br>
        	    <input type="submit" name="button" id="button" value="{$this->LANG['signin']}" title="{$this->LANG['signin']}">
        	  </form>
        	</div>
            <div class="signInPopUpRight">{$this->LANG['sign_in_with']}<br>
              <br>
          <img src="{$this->SNAP->imageset_url}/images/smedia/login-with-fb.png" width="154" height="22" alt="*">
          <br>
          <br>
          {$this->LANG['no_account']}
          <a href="{$this->SNAP->T['create_account_link']}" title="{$this->LANG['sign_up']}">{$this->LANG['sign_up']}</a><br>
          <br>
          <a href="{$this->SNAP->T['lostpw_link']}" title="{$this->LANG['forgot_pw']}">{$this->LANG['forgot_pw']}</a></div>
        </div>
    </div>
</div>
<!-- END PopUp Dialogs //-->
<div class="pageWrapper">
<div class="topBarWrapper">
	<div class="topBar">
    	<div class="topBarLeft"><a href="{$this->SNAP->T['forums_link']}" title="{$this->SNAP->CFG['application_title']}"><img src="{$this->SNAP->imageset_url}/images/{$this->SNAP->CFG['application_logo']}" alt="{$this->SNAP->CFG['application_title']}" class="imgNoBorder"></a></div>
        <div class="topBarRight">
          <ul>
            <li><a href="{$this->SNAP->T['signin_link']}" title="{$this->LANG['signin']}" onClick="return openDialog('id-signin-dialog');">{$this->LANG['signin']}</a></li>
            <li><a href="{$this->SNAP->T['create_account_link']}" title="{$this->LANG['create_account']}">{$this->LANG['create_account']}</a></li>
          </ul>
        </div>
    </div>
</div>
<div class="menuBarWrapper">
	<div class="menuBar">
    	<div class="menuBarLeft">
        	<ul>
                <li><a href="{$this->SNAP->T['forums_link']}" title="{$this->LANG['forums']}">{$this->LANG['forums']}</a></li>
                <li><a href="{$this->SNAP->T['members_link']}" title="{$this->LANG['members']}">{$this->LANG['members']}</a></li>{$this->SNAP->T['calendar_link']}{$this->SNAP->T['gallery_link']}
                <li><a href="{$this->SNAP->T['search_link']}" title="{$this->LANG['search']}">{$this->LANG['search']}</a></li>
                <li><a href="{$this->SNAP->T['help_link']}" title="{$this->LANG['help']}">{$this->LANG['help']}</a></li>
            </ul>
        </div>
        <div class="menuBarRight">{$this->SNAP->T['nav']}</div>
    </div>
</div>
<div class="pageTitleBarWrapper">
	<div class="pageTitleBar">{$this->SNAP->T['page_title']}</div>
</div>
<div class="wrapper">
	<div class="container">
EOF;
}

public function skin_member_header()
{
return <<<EOF
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>{$this->SNAP->T['title']}{$this->SNAP->CFG['application_title']}</title>
<link href="{$this->SNAP->skin_url}/css/index.css" rel="stylesheet" type="text/css" media="screen">
<script type="application/javascript" src="{$this->SNAP->base_url}/public/jscripts/jquery-1.11.2.min.js"></script>
<script type="application/javascript" src="{$this->SNAP->base_url}/public/jscripts/jquery-ui.min.js"></script>

<script type="application/javascript">
$(document).ready(function(e) {
    $("#id-dialog-bg").click(function(e) {
        $("#id-dialog-bg").fadeOut(700);
		$("div:visible[id*='dialog']").fadeOut(700);
    });
});

$(document).click(function(e) {
    if (e.target.id != $("div:visible[id*='slnk']").attr('id') && !$("div:visible[id*='slnk']").find(e.target).length) {
		$("div:visible[id*='popout']").fadeOut(300);
	}
});

function toggle( icn, bdy )
{
	if ( $("#" + bdy).hasClass("toggleOn") )
	{
		$("#" + bdy).removeClass("toggleOn");
		$("#" + bdy).addClass("toggleOff");
		$("#" + bdy).fadeIn(700);
		$("#" + icn).html('<a href="javascript:void(0);" title="{$this->LANG['collapse']}" onclick="toggle(\'' + icn + '\',\'' + bdy + '\');"><img src="{$this->SNAP->imageset_url}/icons/collapse.png" alt="*" /></a>');
	}
	else
	{
		$("#" + bdy).removeClass("toggleOff");
		$("#" + bdy).addClass("toggleOn");
		$("#" + bdy).fadeOut(700);
		$("#" + icn).html('<a href="javascript:void(0);" title="{$this->LANG['expand']}" onclick="toggle(\'' + icn + '\',\'' + bdy + '\');"><img src="{$this->SNAP->imageset_url}/icons/expand.png" alt="*" /></a>');
	}
}

function scrollToTop()
{
	event.preventDefault();
	
	$("html, body").animate({
		scrollTop:0
	}, "slow");
}

function togglePopOut(pu,lk)
{	
	$("div:visible[id*='popout']").fadeOut(300);
	$("#" + pu).fadeToggle(300).position({
		my:       "left top",
		at:       "left bottom",
		of:       $("#" + lk),
		collison: "fit"
	});
}

function togglePopOutUnder(pu,lk)
{	
	$("#" + pu).fadeToggle(300).position({
		my:       "right bottom",
		at:       "right top",
		of:       $("#" + lk),
		collison: "fit"
	});
}

function openDialog(el)
{
	$("#id-dialog-bg").fadeIn(700);
	$("#" + el).fadeIn(700);
	
	var elHeight = $("#" + el).height();
	var elHeight = (elHeight / 2);
	$("#" + el).css({"margin-top":-elHeight});
	
	return false;
}
</script>

</head>

<body>
<!-- START PopOut Menus //-->
<div class="skinLangPopOut" id="id-skin-popout">
	<div class="popOutContainer">{$this->SNAP->T['skin_options']}</div>
</div>

<div class="skinLangPopOut" id="id-lang-popout">
	<div class="popOutContainer">{$this->SNAP->T['language_options']}</div>
</div>

<div class="notificationsPopOut" id="id-notifications-popout">
       	  <div class="contentTitlePopOut">{$this->LANG['notifications']}</div>
            <span class="underPopOutItems">{$this->SNAP->T['notifications']}</span>
            <div class="contentBodyPopOut"><div class="centered"><span class="popOutButton"><a href="{$this->SNAP->T['notifications_url']}" title="{$this->LANG['see_all_n']}">{$this->LANG['see_all_n']}</a></span></div></div>
</div>

<div class="memberMenuPopOut" id="id-member-menu-popout">
	<div class="contentTitlePopOut">{$this->LANG['member_menu']}</div>
    <span class="popOutItems">
    <a href="{$this->SNAP->T['view_profile_link']}" title="{$this->LANG['view_my_profile_info']}">{$this->LANG['view_my_profile']}</a>
    <a href="{$this->SNAP->T['settings_link']}" title="{$this->LANG['account_settings']}">{$this->LANG['account_settings']}</a>
    <a href="{$this->SNAP->T['messenger_link']}" title="{$this->SNAP->T['lang_pm_info']}">{$this->SNAP->T['lang_pm']}</a>
    <a href="{$this->SNAP->T['following_link']}" title="{$this->LANG['following_info']}">{$this->LANG['following']}</a>
    <a href="{$this->SNAP->T['signout_link']}" title="{$this->LANG['sign_out']}">{$this->LANG['sign_out']}</a>
    </span>
</div>
<!-- END PopOut Menus //-->

<div class="pageWrapper">
<div class="topBarWrapper">
	<div class="topBar">
    	<div class="topBarLeft"><a href="{$this->SNAP->T['forums_link']}" title="{$this->SNAP->CFG['application_title']}"><img src="{$this->SNAP->imageset_url}/images/{$this->SNAP->CFG['application_logo']}" alt="{$this->SNAP->CFG['application_title']}" class="imgAlignMiddleBoth"></a></div>
        <div class="topBarRight">
        	<div class="memberStatusBar">
            	<div class="memberStatusBarIcon"><div id="id-notifications-slnk" class="popOutRel"><a href="javascript:void(0);" title="{$this->LANG['notifications']}" onClick="togglePopOut('id-notifications-popout','id-notifications-slnk');">{$this->SNAP->T['notification_icon']}</a></div></div>
                <div class="memberStatusBarLink">
                	<div class="memberStatusBarLinkLeft">{$this->SNAP->T['avatar']}</div>
                    <div class="memberStatusBarLinkRight" id="id-member-menu-slnk"><a href="javascript:void();" title="{$this->LANG['open_menu']}" onClick="togglePopOut('id-member-menu-popout','id-member-menu-slnk');">{$this->SNAP->MEMBER['display_name']} <img src="{$this->SNAP->imageset_url}/icons/down-arrow.png" width="16" height="16" alt="*" class="imgAlign"></a></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="menuBarWrapper">
	<div class="menuBar">
    	<div class="menuBarLeft">
        	<ul>
                <li><a href="{$this->SNAP->T['forums_link']}" title="{$this->LANG['forums']}">{$this->LANG['forums']}</a></li>
                <li><a href="{$this->SNAP->T['members_link']}" title="{$this->LANG['members']}">{$this->LANG['members']}</a></li>{$this->SNAP->T['calendar_link']}{$this->SNAP->T['gallery_link']}
                <li><a href="{$this->SNAP->T['search_link']}" title="{$this->LANG['search']}">{$this->LANG['search']}</a></li>
                <li><a href="{$this->SNAP->T['help_link']}" title="{$this->LANG['help']}">{$this->LANG['help']}</a></li>
            </ul>
        </div>
        <div class="menuBarRight">{$this->SNAP->T['nav']}</div>
    </div>
</div>
<div class="pageTitleBarWrapper">
	<div class="pageTitleBar">{$this->SNAP->T['page_title']}</div>
</div>
<div class="wrapper">
	<div class="container">
EOF;
}

public function skin_italic_text()
{
return <<<EOF
<span class="italicText">{$this->SNAP->T['text']}</span>
EOF;
}

public function skin_timestamp_text()
{
return <<<EOF
<span class="timestampTxt">{$this->SNAP->T['text']}</span>
EOF;
}

public function skin_debug_information()
{
return <<<EOF
 <ul>
    <li>{$this->LANG['page_processed']}</li>
    <li>{$this->LANG['sql_queries']}</li>
    <li>{$this->LANG['gzip']}</li>
</ul>
EOF;
}

public function skin_footer()
{
return <<<EOF
      </div>
</div>
</div>
<div class="pushFooter"></div>
</div>
<div class="pageFooter">
	<div class="footerContainer">
    	<div class="topFooterWrapper">
    	<div class="topFooter">
        	<div class="footerLeft"><div id="id-skin-slnk" class="skinLangWrapper"><a href="javascript:void(0);" title="{$this->LANG['choose_skin_info']}" class="popOutLink" onClick="togglePopOut('id-skin-popout','id-skin-slnk');">{$this->LANG['choose_skin']}</a> <img src="{$this->SNAP->imageset_url}/icons/up-dialog.png" width="16" height="16" alt="*" class="imgAlign"></div>
        	    <div class="skinLangWrapper" id="id-lang-slnk"><a href="javascript:void(0);" title="{$this->LANG['choose_language_info']}" onClick="togglePopOut('id-lang-popout','id-lang-slnk');">{$this->LANG['choose_language']}</a> <img src="{$this->SNAP->imageset_url}/icons/up-dialog.png" width="16" height="16" alt="*" class="imgAlign"></div>
        	</div>
          <div class="footerRight">
            <ul>
              <li><a href="{$this->SNAP->T['mark_all_link']}" title="{$this->LANG['mark_all_read_info']}">{$this->LANG['mark_all_read']}</a></li>
              <li><a href="{$this->SNAP->T['new_content_link']}" title="{$this->LANG['new_content_info']}">{$this->LANG['new_content']}</a></li>
              <li><a href="{$this->SNAP->T['leaders_link']}" title="{$this->LANG['leaders_info']}">{$this->LANG['leaders']}</a></li>
              <li><a href="{$this->SNAP->T['rules_link']}" title="{$this->LANG['community_rules_info']}">{$this->LANG['community_rules']}</a></li>
              <li><a href="{$this->SNAP->T['privacy_link']}" title="{$this->LANG['privacy_policy_info']}">{$this->LANG['privacy_policy']}</a></li>
            </ul>
          </div>
        </div>
    	</div>
    	<div class="bottomFooterWrapper">
    	<div class="bottomFooter">
        	<div class="bottomFooterLeft">
        	  {$this->SNAP->T['debug_information']}
        	</div>
            <div class="bottomFooterMiddle">{$this->LANG['all_times']} | <a href="javascript:void(0);" title="{$this->LANG['go_to_top_info']}" onClick="scrollToTop();">{$this->LANG['go_to_top']}</a></div>
            <div class="bottomFooterRight">{$this->LANG['powered_by']} <a href="http://www.ohsnapbulletin.com" title="Oh Snap! Bulletin">Oh Snap! Bulletin</a> {$this->LANG['version']} {$this->SNAP->snap_version} &copy; 2015</div>
        </div>
    	</div>
    </div>
</div>
</body>
</html>
EOF;
}

}

?>