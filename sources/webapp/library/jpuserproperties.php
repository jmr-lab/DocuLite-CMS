<?php $class->init();?>
<jm:modal title="<?php $class->getTitle();?>" icon="<?php $class->getObjectIcon();?>">
<div class="content">
	<div class="labeltitle">
		<jm:datatextvalue nlsid="<?php echo $class->getString('NAME');?>" value="<?php echo $class->getObjectName();?>" name="object_name" width="450px" readonly="<?php echo $class->isReadOnly();?>"/>
	</div>
	<div class="labeltitle">
		<jm:datatextvalue nlsid="<?php echo $class->getString('EMAIL');?>" value="<?php echo $class->getValue('user_address');?>" name="email" width="450px" readonly="<?php echo $class->isReadOnly();?>"/>
	</div>
	<div class="labeltitle">
		<jm:datatextareavalue nlsid="<?php echo $class->getString('DESCRIPTION');?>" value="<?php echo $class->getValue('description');?>" name="description" width="450px" readonly="<?php echo $class->isReadOnly();?>"/>
	</div>
	<div class="line"><!-- --></div>
	<div class="labeltitle">
		<jm:datadropdownvalue nlsid="<?php echo $class->getString('USER_STATE');?>" value="<?php echo $class->getValue('user_state');?>" name="user_state" width="150px" readonly="<?php echo $class->isReadOnly();?>"/>
		<jm:datadropdownvalue nlsid="<?php echo $class->getString('CLIENT_CAPABILITY');?>" value="<?php echo $class->getValue('client_capability');?>" name="client_capability" readonly="<?php echo $class->isReadOnly();?>"/>
	</div>
	<div class="line"><!-- --></div>
	<div class="labeltitle">
		<span><?php echo $class->getString('TYPE');?> :&nbsp;</span>
		<span class="label"><?php echo $class->getValue('r_object_type');?></span>
		<span><?php echo $class->getString('HISTORY');?> :&nbsp;</span>
		<span class="label">
			<a onclick="javascript:postServerEvent('objectlist', 'nest', 'history', null, 'objectId=<?php echo $class->getValue('r_object_id');?>');" class="actionbutton"><img src="/estancia/webapp/themes/default/images/icons/history_16.png"><span><?php echo $class->getString('VIEW');?></span></a>
		</span>
	</div>
	<div class="labeltitle">
		<span><?php echo $class->getString('ID');?> :&nbsp;</span>
		<span class="label"><?php echo $class->getValue('r_object_id');?></span>
		<span><?php echo $class->getString('MODIFIED');?> :&nbsp;</span>
		<span class="label"><?php echo $class->getValue('r_modify_date');?></span>
	</div>
</div>
<div id="message"></div>
<div class="buttons">
<jm:button action="postServerEvent('userproperties', 'return', null, 'onOk', null);" nlsid="SAVE" src="ok_16.png" cssclass="right" hidden="<?php echo $class->isReadOnly();?>"/>
<jm:button action="postServerEvent('objectlist', 'return', null, null, null);" nlsid="CANCEL" src="cancel_16.png" cssclass="right"/>
</div>
</jm:modal>