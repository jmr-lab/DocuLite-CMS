<?php $class->init();?>
<div style="background-color: #F3F3F3; position: absolute; top: 0px; bottom: 0px; left: 0px; right: 0px; padding-top: 6px;">
<!-- Mail header -->
<jm:title icon="eml.png" title="<?php echo htmlentities($class->getTitle());?>">
	<div class="header-actions">
		<jm:actionbutton action="postServerEvent('writemessage', null, 'writemessage', 'doSend', null);" nlsid="SEND" src="forward_16.png"/>
		<jm:actionbutton action="postServerEvent('writemessage', 'close', null, null, null);" nlsid="CANCEL" src="cancelmail_16.png"/>
	</div>
	<div class="filigrane"><img src="/estancia/webapp/themes/default/images/icons/network.png"></div>
</jm:title>
<!-- Mail fields -->
<jm:mailfield action="postServerEvent('writemessage', 'nest', 'selectrecipient', null, null);" nlsid="TO">
	<jm:actionbutton target="TO" cssclass="user" id="123456" nlsid="WORLD" src="jm_group_16.png" delete="true"/>
</jm:mailfield>
<jm:mailfield action="postServerEvent('writemessage', null, 'writemessage', 'doAddUser', null);" nlsid="CC"/>
<jm:mailfield action="postServerEvent('writemessage', null, 'writemessage', 'doAddUser', null);" nlsid="BCC"/>
<jm:mailfield nlsid="SUBJECT">
	<jm:datatextvalue value="<?php echo $class->getSubject();?>" name="subject" readonly="false"/>
</jm:mailfield>
<!-- Mail content -->
<div class="mailtextarea"><jm:datatextareavalue value="" class="" name="writebody" readonly="false"/></div>
</div>
