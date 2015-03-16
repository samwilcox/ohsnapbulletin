<?php

class OHSNAPThemeAuthenticate
{

public $SNAP;
public $LANG;

public function html_member_sign_in_form_nav()
{
return <<<EOF
 &raquo; <a href="{$this->SNAP->T['signin_link']}" title="{$this->LANG['member_sign_in']}">{$this->LANG['member_sign_in']}</a>
EOF;
}

public function html_sign_in_error_box()
{
return <<<EOF
<div class="signInErrorBox"><img src="{$this->SNAP->imageset_url}/icons/warning.png" width="16" height="16" alt="*" class="imgMiddle"> {$this->SNAP->T['error']}</div>
EOF;
}

public function html_create_account_link()
{
return <<<EOF
<a href="{$this->SNAP->T['create_link']}" title="{$this->SNAP->LANG['create_account']}">{$this->SNAP->LANG['create_account']}</a>
EOF;
}

public function html_sign_in_form()
{
return <<<EOF
        	<form name="signin" id="signin" method="post" action="{$this->SNAP->script_url}">
            	<div class="mainContentTitle">{$this->LANG['member_sign_in']}</div>
                <div class="mainContent">
                	<div class="bubbleStatusBar">{$this->SNAP->LANG['dont_have_account']}</div>{$this->SNAP->T['error_box']}
                    <div class="signInFieldBar">
                    	<div class="signInFieldName">{$this->SNAP->LANG['username_email']}</div>
                        <div class="signInFieldValue">
                          <input name="username" type="text" id="username" size="32">
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="signInFieldBar">
                    	<div class="signInFieldName">{$this->SNAP->LANG['password']}</div>
                        <div class="signInFieldValue">
                          <input name="password" type="password" id="password" size="32">
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="signInOptionsBar">
                    	<div class="signInOptionsField">
                    	  <input name="rememberme" type="checkbox" id="rememberme" value="1" checked>
                    	</div>
                        <div class="signInOptionsValue"><strong>{$this->SNAP->LANG['remember_me']}</strong><br>
                          <span class="smlFieldInfo">{$this->SNAP->LANG['remember_me_info']}</span>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="signInOptionsBar">
                    	<div class="signInOptionsField">
                    	  <input name="anonymous" type="checkbox" id="anonymous" value="1">
                    	</div>
                        <div class="signInOptionsValue">{$this->SNAP->LANG['hide_me']}</div>
                        <div class="clear"></div>
                    </div>
                    <div class="signInButtons">
                    	<input type="hidden" name="action" value="authenticate">
                        <input type="hidden" name="sact" value="signin">
                        <input type="hidden" name="referer" value="{$this->SNAP->T['referer']}">
                      <input type="submit" name="button3" id="button3" value="{$this->SNAP->LANG['sign_in_button']}">
                      <br>
                      <br>
                    <a href="{$this->SNAP->T['forgotpw_link']}" title="{$this->SNAP->LANG['forgot_password_info']}">{$this->SNAP->LANG['forgot_password']}</a></div>
                </div>
        	</form>
   	  </div>
EOF;
}

}

?>