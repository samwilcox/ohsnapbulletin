<?php

class OHSNAPThemeGlobal
{

public $SNAP;
public $LANG;

public function html_select_option()
{
return <<<EOF
<option value="{$this->SNAP->T['value']}"{$this->SNAP->T['selected']}>{$this->SNAP->T['name']}</option>
EOF;
}

public function html_create_account_dialog_coppa_verification()
{
return <<<EOF
<!-- Account Creation COPPA Verification Dialog //-->
<div class="popUpAgeVerify" id="popupverify">
	<div class="popUpTitle">
    	<div class="popUpTitleLeft">{$this->LANG['coppa_age_verification']}</div>
        <div class="popUpTitleRight"><a href="javascript:void();" title="{$this->LANG['close_dialog']}" onClick="closeDialog('popupverify');"><img src="{$this->SNAP->imageset_url}/icons/close-dialog.png" width="16" height="16" alt="*" class="imgFaded"></a></div>
        <div class="clear"></div>
    </div>
    <div class="popUpContent">
    	<div class="popUpAgeVerifyCentered">{$this->LANG['coppa_enter_dob']}<br>
    	  <br>
    	  <form name="coppaverify" id="coppaverify" method="post" action="{$this->SNAP->script_url}">
    	    <select name="month" id="month">
    	      <option value="1" selected>January</option>
    	      <option value="2">February</option>
    	      <option value="3">March</option>
    	      <option value="4">April</option>
    	      <option value="5">May</option>
    	      <option value="6">June</option>
    	      <option value="7">July</option>
    	      <option value="8">August</option>
    	      <option value="9">September</option>
    	      <option value="10">October</option>
    	      <option value="11">November</option>
    	      <option>December</option>
  	        </select> 
    	    <select name="day" id="day">
    	      {$this->SNAP->T['day']}
  	        </select>
    	  , 
    	  <select name="year" id="year">
    	    {$this->SNAP->T['year']}
  	      </select>
    	  <br>
    	  <br>
    	  <input type="submit" name="button2" id="button2" value="{$this->LANG['continue_button']}" title="{$this->LANG['continue_button_info']}">
    	  </form>
    	</div>
  </div>
</div>
<!-- Account Creation COPPA Verification Dialog END //-->
EOF;
}

public function html_create_account_link_coppa()
{
return <<<EOF
<span class="createAccountLink"><a href="javascript:void();" title="{$this->LANG['create_account']}" onClick="openDialog('popupverify');">{$this->LANG['create_account']}</a></span>
EOF;
}

public function html_create_account_link()
{
return <<<EOF
<span class="createAccountLink"><a href="{$this->SNAP->T['create_account_link']}" title="{$this->LANG['create_account']}">{$this->LANG['create_account']}</a></span>
EOF;
}

public function html_create_account_disabled()
{
return <<<EOF
<span class="createAccountDisabled">{$this->LANG['create_account_disabled']}</span>
EOF;
}

public function html_create_account_url()
{
return <<<EOF
<a href="{$this->SNAP->T['create_account_link']}" title="{$this->LANG['create_account']}">{$this->LANG['create_account']}</a>
EOF;
}

public function html_sign_in_dialog()
{
return <<<EOF
<!-- Member Sign In Dialog //-->
<div class="popUpSignIn" id="popupsignin">
  <div class="popUpTitle">
   	<div class="popUpTitleLeft">{$this->LANG['sign_in_title']}</div>
        <div class="popUpTitleRight"><a href="javascript:void();" title="{$this->LANG['close_dialog']}" onClick="closeDialog('popupsignin');"><img src="{$this->SNAP->imageset_url}/icons/close-dialog.png" width="16" height="16" alt="*" class="imgFaded"></a></div>
        <div class="clear"></div>
  </div>
  <div class="popUpContent">
  	<div class="bubbleStatusBar">{$this->SNAP->T['lang_dont_have_account']}</div>
    <form name="signin" id="signin" method="post" action="{$this->SNAP->script_url}">
      <div class="signInFieldBar">
      	<div class="signInFieldName">{$this->LANG['username_email']}</div>
        <div class="signInFieldValue">
          <input name="username" type="text" id="username" size="32" maxlength="32">
        </div>
        <div class="clear"></div>
      </div>
      <div class="signInFieldBar">
      	<div class="signInFieldName">{$this->LANG['password']}</div>
        <div class="signInFieldValue">
          <input name="password" type="password" id="password" size="32" maxlength="32">
        </div>
        <div class="clear"></div>
      </div>
      <div class="signInOptionsBar">
      	<div class="signInOptionsField">
      	  <input name="rememberme" type="checkbox" id="rememberme" value="1" checked>
      	</div>
        <div class="signInOptionsValue"><strong>{$this->LANG['remember_me']}</strong><br>
          <span class="smlFieldInfo">{$this->LANG['remember_me_info']}</span>
        </div>
        <div class="clear"></div>
      </div>
      <div class="signInOptionsBar">
      	<div class="signInOptionsField">
      	  <input name="anonymous" type="checkbox" id="anonymous" value="1">
      	</div>
        <div class="signInOptionsValue">{$this->LANG['hide_me']}</div>
        <div class="clear"></div>
      </div>
      <div class="signInButtons">
      	<input type="hidden" name="action" value="authenticate">
        <input type="hidden" name="sact" value="signin">
        <input type="hidden" name="referer" value="URLHERE">
        <input type="submit" name="button" id="button" value="{$this->LANG['sign_in_button']}" title="{$this->LANG['sign_in_button']}">
        <br>
        <br>
      <a href="{$this->SNAP->T['lost_pw_link']}" title="{$this->LANG['forgot_password_info']}">{$this->LANG['forgot_password']}</a></div>
    </form>
  </div>
</div>
<!-- Member Sign In Dialog END //-->

EOF;
}

public function html_guest_status()
{
return <<<EOF
<span class="signInLink"><a href="{$this->SNAP->script_url}?action=authenticate" title="{$this->LANG['sign_in']}" onClick=" return openDialog('popupsignin');">{$this->LANG['sign_in']}</a></span>{$this->SNAP->T['create_account']}
EOF;
}

public function html_top_header()
{
return <<<EOF
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>{$this->SNAP->T['title']}Oh Snap! Bulletin Development Forums</title>
<link href="{$this->SNAP->theme_url}/css/index.css" rel="stylesheet" type="text/css" media="screen">
<script language="javascript" type="text/javascript" src="{$this->SNAP->base_url}/public/jscripts/jquery-1.11.2.min.js"></script>
{$this->SNAP->T['member_menu_js']}
<script language="javascript" type="text/javascript">
$(document).ready(function() {
    $("#popupbg").click(function() {
        $("#popupbg").fadeOut();
		$("div:visible[id*='popup']").fadeOut();
    });
});
</script>

</head>

<body>
<script language="javascript" type="text/javascript">
function openDialog(el)
{
	$("#popupbg").fadeIn();
	$("#" + el).fadeIn();
    return false;
}

function closeDialog(el)
{
	$("#popupbg").fadeOut();
	$("#" + el).fadeOut();
    return false;
}
</script>
<div class="popUpBackground" id="popupbg"></div>
{$this->SNAP->T['sign_in_dialog']}
{$this->SNAP->T['coppa_dialog']}
<div class="topBarWrapper">
	<div class="topBar">
	  <table class="topBarTable" cellpadding="0" cellspacing="0">
	    <tr>
	      <td class="topBarCellLeft"><a href="{$this->SNAP->T['index_link']}" title="{$this->SNAP->CFG['bulletin_title']}"><img src="{$this->SNAP->imageset_url}/images/{$this->SNAP->CFG['logo_image']}" alt="{$this->SNAP->CFG['bulletin_title']}" class="imgNoBorder"></a></td>
	      <td class="topBarCellCenter"><ul>
	        <li><a href="{$this->SNAP->T['members_link']}" title="Members"><img src="{$this->SNAP->imageset_url}/icons/menu-members.png" width="22" height="22" alt="*" class="imgAlign"> {$this->LANG['members']}</a></li>{$this->SNAP->T['calendar']}
	        <li><a href="{$this->SNAP->T['search_link']}" title="Search"><img src="{$this->SNAP->imageset_url}/icons/menu-search.png" width="22" height="22" alt="*" class="imgAlign"> {$this->LANG['search']}</a></li>
	        <li><a href="{$this->SNAP->T['help_link']}" title="Help"><img src="{$this->SNAP->imageset_url}/icons/menu-help.png" width="22" height="22" alt="*" class="imgAlign"> {$this->LANG['help']}</a></li>
          </ul></td>
	      <td class="topBarCellRight">{$this->SNAP->T['user_status']}</td>
        </tr>
      </table>
	</div>
</div>
<div class="wrapper">
  <div class="contentWrapper">
   	  <div class="contentBody">
        	<div class="navBar">
            	<div class="navBarLeft"><img src="{$this->SNAP->imageset_url}/icons/nav-house.png" width="16" height="16" alt="*" class="imgMiddle"> <a href="{$this->SNAP->T['index_link']}" title="{$this->SNAP->CFG['bulletin_title']}">{$this->SNAP->CFG['bulletin_title']}</a> {$this->SNAP->T['nav']}</div>
                <div class="navBarRight"><a href="{$this->SNAP->T['rss_link']}" title="RSS Feed"><img src="{$this->SNAP->imageset_url}/icons/rss.png" width="16" height="16" alt="RSS Feed" class="imgNoBorder"></a></div>
                <div class="clear"></div>
          </div>
EOF;
}

public function html_member_link()
{
return <<<EOF
{$this->SNAP->T['seperator']}<a href="{$this->SNAP->T['member_link']}" title="{$this->SNAP->T['title']}">{$this->SNAP->T['display_name']}</a>
EOF;
}

public function html_member_link_tags()
{
return <<<EOF
{$this->SNAP->T['seperator']}<a href="{$this->SNAP->T['member_link']}" title="{$this->SNAP->T['title']}">{$this->SNAP->T['between_tags']}</a>
EOF;
}

public function html_no_avatar_thumb_no_limit()
{
return <<<EOF
<img src="{$this->SNAP->T['filename']}" alt="*" width="55" height="35" class="{$this->SNAP->T['class']}">
EOF;
}

public function html_no_avatar_thumb_limit()
{
return <<<EOF
<img src="{$this->SNAP->T['filename']}" alt="*" width="{$this->SNAP->T['width']}" height="{$this->SNAP->T['height']}" class="{$this->SNAP->T['class']}">
EOF;
}

public function html_no_avatar_no_limit()
{
return <<<EOF
<img src="{$this->SNAP->T['filename']}" alt="*">
EOF;
}

public function html_no_avatar_limit()
{
return <<<EOF
<img src="{$this->SNAP->T['filename']}" alt="*" width="{$this->SNAP->T['width']}" height="{$this->SNAP->T['height']}">
EOF;
}

public function html_avatar_thumb_no_limit()
{
return <<<EOF
<img src="{$this->SNAP->T['filename']}" alt="*" class="{$this->SNAP->T['class']}">
EOF;
}

public function html_avatar_thumb_limit()
{
return <<<EOF
<img src="{$this->SNAP->T['filename']}" alt="*" width="{$this->SNAP->T['width']}" height="{$this->SNAP->T['height']}" class="{$this->SNAP->T['class']}">
EOF;
}

public function html_avatar_no_limit()
{
return <<<EOF
<img src="{$this->SNAP->T['filename']}" alt="*">
EOF;
}

public function html_avatar_limit()
{
return <<<EOF
<img src="{$this->SNAP->T['filename']}" alt="*" width="{$this->SNAP->T['width']}" height="{$this->SNAP->T[height]}">
EOF;
}

public function html_avatar_facebook_thumb_no_limit()
{
return <<<EOF
<fb:profile-pic uid="{$this->SNAP->T['oauth_id']}" size="normal" facebook-logo="true"></fb:profile-pic>
EOF;
}

public function html_avatar_facebook_thumb_limit()
{
return <<<EOF
<fb:profile-pic uid="{$this->SNAP->T['oauth_id']}" size="normal" width="{$this->SNAP->T['width']}" facebook-logo="true"></fb:profile-pic>
EOF;
}

public function html_avatar_facebook_no_limit()
{
return <<<EOF
<fb:profile-pic uid="{$this->SNAP->T['oauth_id']}" size="normal" facebook-logo="true"></fb:profile-pic>
EOF;
}

public function html_avatar_facebook_limit()
{
return <<<EOF
<fb:profile-pic uid="{$this->SNAP->T['oauth_id']}" size="normal" width="{$this->SNAP->T['width']}" facebook-logo="true"></fb:profile-pic>
EOF;
}

public function html_calendar_link()
{
return <<<EOF
<li><a href="{$this->SNAP->T['calendar_link']}" title="Calendar"><img src="{$this->SNAP->imageset_url}/icons/menu-calendar.png" width="22" height="22" alt="*" class="imgAlign"> {$this->LANG['calendar']}</a></li>
EOF;
}

public function html_system_debug_information()
{
return <<<EOF
{$this->LANG['page_processed']}<br>
EOF;
}

public function html_footer()
{
return <<<EOF
    <div class="bottomBar">
        	<div class="bottomBarLeft">
        	  <select name="theme" id="theme">
              	<optgroup label="{$this->LANG['choose_theme']}">
        	    {$this->SNAP->T['theme_options']}
                </optgroup>
      	      </select> 
        	  <select name="language" id="language">
              	<optgroup label="{$this->LANG['choose_language']}">
        	    {$this->SNAP->T['language_options']}
                </optgroup>
      	      </select>
        	</div>
            <div class="bottomBarRight">
              <ul>
                <li><a href="{$this->SNAP->T['new_content_link']}" title="{$this->LANG['newest_content']}">{$this->LANG['newest_content']}</a></li>
                <li><a href="{$this->SNAP->T['mark_all_link']}" title="{$this->LANG['mark_all_read']}">{$this->LANG['mark_all_read']}</a></li>
                <li><a href="{$this->SNAP->T['view_leaders_link']}" title="{$this->LANG['view_leaders']}">{$this->LANG['view_leaders']}</a></li>
                <li><a href="#top" title="{$this->LANG['go_to_top']}">{$this->LANG['go_to_top']}</a></li>
              </ul>
            </div>
            <div class="clear"></div>
        </div>
        <div class="underBottom">{$this->SNAP->T['debug_information']}
        {$this->LANG['all_times']}
        <br>
        {$this->LANG['powered_by']} <a href="http://www.ohsnapbulletin.com" title="Oh Snap! Bulletin" target="_blank">Oh Snap! Bulletin</a> {$this->LANG['version']} {$this->SNAP->T['version']}</div>
    </div>
</div>
</body>
</html>
EOF;
}

public function html_member_messenger_link()
{
return <<<EOF
<a href="{$this->SNAP->T['messenger_link']}" title="{$this->LANG['my_messages']}">{$this->LANG['my_messages']}</a>
EOF;
}

public function html_member_status_bar()
{
return <<<EOF
            <table class="memberStatusTable" cellspacing="0" cellpadding="0" align="right">
	            <tr>
	              <td class="memberStatusLeft">{$this->SNAP->T['member_avatar']}</td>
	              <td class="memberStatusRight">{$this->LANG['signed_in_as']}</strong><br>
                  <span class="memberMenuLink" id="mlink"><a href="javascript:void();" title="{$this->LANG['member_menu_info']}" onClick="toggleMemberMenu();" id="mmlink"><strong>{$this->LANG['member_menu']}</strong> <span id="mmenuimg"><img src="{$this->SNAP->imageset_url}/icons/expand-menu.png" alt="*" class="imgAlign"></span></a>
                  <div class="memberMenu" id="member_menu"><span class="dropDownMenuRel"><img src="{$this->SNAP->imageset_url}/icons/drop-down-arrow.png" class="dropDownMenuArrow"></span><span class="dropDownFirstLink"><a href="{$this->SNAP->T['profile_link']}" title="{$this->LANG['view_my_profile']}">{$this->LANG['view_my_profile']}</a></span><span class="dropDownLink">{$this->SNAP->T['messenger_link']}<a href="{$this->SNAP->T['settings_link']}" title="{$this->LANG['my_settings']}">{$this->LANG['my_settings']}</a><a href="{$this->SNAP->T['signout_link']}" title="{$this->LANG['sign_out']}">{$this->LANG['sign_out']}</a></span></div></span></td>
	              </tr>
              </table>
EOF;
}

public function html_member_menu_js()
{
return <<<EOF
<script language="javascript" type="text/javascript" src="{$this->SNAP->base_url}/public/jscripts/snap-member-drop-downs.js"></script>

EOF;
}

}

?>