<?php
$sessionmanager = new JfSessionManager();
$session = $sessionmanager->getSession('www_jmroy');
?>
<jm:actionlink action="postServerEvent('objectlist', 'jump', 'estancia', null, null);" cssclass="navigationlink" src="estancia_16.png" nlsid="<?php echo $session->getDocbaseName(); ?>"/>

<jm:actionlink action="postServerEvent('objectlist', 'jump', 'doclist', null, null);" cssclass="navigationsublink" src="repository_16.png" nlsid="REPOSITORY"/>
<jm:actionlink action="postServerEvent('objectlist', 'jump', 'home', null, null);" cssclass="navigationsublink" src="home_16.png" nlsid="HOME"/>
<jm:actionlink action="postServerEvent('objectlist', 'jump', 'checkedout', null, null);" cssclass="navigationsublink" src="lock_16.png" nlsid="CHECKED_OUT"/>
<!--jm:actionlink action="postServerEvent('objectlist', 'jump', 'mydocuments', null, null);" cssclass="navigationsublink" src="mydocuments_16.png" nlsid="MY_DOCUMENTS"/-->
<jm:actionlink action="postServerEvent('objectlist', 'jump', 'favorites', null, null);" cssclass="navigationsublink" src="favorites_16.png" nlsid="FAVORITES"/>
<jm:actionlink action="postServerEvent('objectlist', 'jump', 'clipboard', null, null);" cssclass="navigationsublink" src="clipboard_16.png" nlsid="CLIPBOARD"/>
<!--jm:actionlink action="postServerEvent('objectlist', 'nest', 'search', null, null);" cssclass="navigationsublink" src="search_16.png" nlsid="SEARCH"/-->
<jm:actionlink action="postServerEvent('objectlist', 'jump', 'recyclebin', null, null);" cssclass="navigationsublink" src="recyclebin_16.png" nlsid="RECYCLE_BIN"/>

<jm:actionlink action="postServerEvent('objectlist', 'jump', 'communication', null, null);" cssclass="navigationlink" src="communication_16.png" nlsid="COMMUNICATIONS"/>

<jm:actionlink action="postServerEvent('objectlist', 'jump', 'inbox', null, null);" cssclass="navigationsublink" src="inbox_16.png" nlsid="INBOX"/>
<!--jm:actionlink action="postServerEvent('objectlist', 'jump', 'draft', null, null);" cssclass="navigationsublink" src="create_16.png" nlsid="DRAFTS"/-->
<jm:actionlink action="postServerEvent('objectlist', 'jump', 'outbox', null, null);" cssclass="navigationsublink" src="outbox_16.png" nlsid="SENT"/>
<!--jm:actionlink action="postServerEvent('objectlist', 'jump', 'inbox', null, null);" cssclass="navigationsublink" src="recyclebin_16.png" nlsid="DELETED"/-->

<jm:actionlink action="postServerEvent('objectlist', 'jump', 'administration', null, null);" cssclass="navigationlink" src="administration_16.png" nlsid="ADMINISTRATION"/>

<jm:actionlink action="postServerEvent('objectlist', 'jump', 'usermanagement', null, null);" cssclass="navigationsublink" src="user_management_16.png" nlsid="USER_MANAGEMENT"/>
<jm:actionlink action="postServerEvent('objectlist', 'jump', 'security', null, null);" cssclass="navigationsublink" src="security_16.png" nlsid="SECURITY"/>
<jm:actionlink action="postServerEvent('objectlist', 'jump', 'formats', null, null);" cssclass="navigationsublink" src="jm_format_16.png" nlsid="FORMATS"/>
<jm:actionlink action="postServerEvent('objectlist', 'jump', 'types', null, null);" cssclass="navigationsublink" src="jm_type_16.png" nlsid="TYPES"/>
<jm:actionlink action="postServerEvent('objectlist', 'jump', 'workflows', null, null);" cssclass="navigationsublink" src="jm_workflow_16.png" nlsid="WORKFLOWS"/>
<!--jm:actionlink action="postServerEvent('objectlist', 'nest', 'configuration', null, null);" cssclass="navigationsublink" src="configuration_16.png" nlsid="CONFIGURATION"/-->

<jm:actionlink action="postServerEvent('objectlist', 'nest', 'about', null, null);" cssclass="navigationlink" src="about_16.png" nlsid="ABOUT"/>
