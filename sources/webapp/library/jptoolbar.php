<?php
$sessionmanager = new JfSessionManager();
$session = $sessionmanager->getSession('www_jmroy');
$logininfo = $session->getLoginInfo();
?>
<!--img src="<?php echo _APP_ROOT_; ?>/webapp/themes/default/images/background/menubar.png" width="100%" height="32px"-->
<div class="toolbar-content" style="background-color: #1C767C;">

<div class="toolbar-search">
<jm:text onkeypress="if (event.keyCode == 13) {search();}" cssclass="toolbarsearchlink" id="txtsearch" nlsid="<?php echo $class->getString('SEARCH');?>"/>
<jm:actionlink action="search();" cssclass="toolbaradminlink" src="search_16.png"/>
</div>
<!--div class="toolbar-actions"></div-->

<div class="toolbar-admin">
<jm:actionlink action="properties('<?php echo $logininfo->getValue('r_object_id'); ?>');" cssclass="toolbarlink" src="user_16.png" nlsid="<?php echo $logininfo->getValue('user_name'); ?>"/>
<jm:actionlink action="postServerEvent('objectlist', 'nest', 'logout', null, null);" cssclass="toolbarlink" src="logout_16.png" nlsid="LOGOUT"/>
<jm:actionlink action="postServerEvent('objectlist', 'nest', 'help', null, null);" cssclass="toolbarlink" src="help_16.png" nlsid="HELP"/>
</div>

</div>
