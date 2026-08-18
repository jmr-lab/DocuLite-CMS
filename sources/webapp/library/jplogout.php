<jm:modal title="<?php echo $class->getString('LOGOUT');?>" icon="logout_48.png" style="width: 600px;">
<div class="attribute"><?php echo $class->getString('MSG_LOGOUT'); ?></div>
<div class="attribute" id="message"></div>
<div class="buttons">
<jm:button action="postServerEvent('logout', null, 'logout', 'onOk', null);" nlsid="LOGOUT" src="ok_16.png" cssclass="right"/>
<jm:button action="postServerEvent('logout', 'return', 'home', null, null);" nlsid="CANCEL" src="cancel_16.png" cssclass="right"/>
</div>
</jm:modal>