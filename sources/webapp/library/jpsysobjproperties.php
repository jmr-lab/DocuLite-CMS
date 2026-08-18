<?php $class->init();?>
<jm:modal title="<?php $class->getObjectName();?>" icon="<?php $class->getObjectIcon();?>" rticon="<?php $class->getCheckedOutIcon();?>">
<div class="content">
	<div class="labeltitle">
		<jm:datatextvalue nlsid="<?php echo $class->getString('NAME');?>" value="<?php echo $class->getValue('object_name');?>" name="object_name" width="450px" readonly="<?php echo $class->isReadOnly();?>"/>
	</div>
	<div class="labeltitle">
		<jm:datatextvalue nlsid="<?php echo $class->getString('TITLE');?>" value="<?php echo $class->getValue('title');?>" name="title" width="450px" readonly="<?php echo $class->isReadOnly();?>"/>
	</div>
	<div class="labeltitle">
		<jm:datatextareavalue nlsid="<?php echo $class->getString('SUBJECT');?>" value="<?php echo $class->getValue('subject');?>" name="subject" width="450px" readonly="<?php echo $class->isReadOnly();?>"/>
	</div>
	<div class="line"><!-- --></div>
	<div class="labeltitle">
		<span><?php echo $class->getString('AUTHOR');?> :&nbsp;</span>
		<span class="label"><?php echo $class->getValue('r_creator_name', true);?></span>
		<span><?php echo $class->getString('CREATED');?> :&nbsp;</span>
		<span class="label"><?php echo $class->getValue('r_creation_date');?></span>
	</div>
	<div class="labeltitle">
		<span><?php echo $class->getString('MODIFIER');?> :&nbsp;</span>
		<span class="label"><?php echo $class->getValue('r_modifier', true);?></span>
		<span><?php echo $class->getString('MODIFIED');?> :&nbsp;</span>
		<span class="label"><?php echo $class->getValue('r_modify_date');?></span>
	</div>
	<div class="line"><!-- --></div>
	<div class="labeltitle">
		<span><?php echo $class->getString('OWNER');?> :&nbsp;</span>
		<span class="label">
			<?php if ($class->isReadOnly() == 'false')	{?>
				<!--div onclick="javascript:postServerEvent('writemessage', null, 'writemessage', 'doAddUser', null);" class="user"><img src="/estancia/webapp/themes/default/images/icons/jm_user_16.png"><span><?php echo $class->getValue('owner_name', true);?></span></div-->
				<a class="user"><img src="/estancia/webapp/themes/default/images/icons/jm_user_16.png"><span><?php echo $class->getValue('owner_name', true);?></span></a>
			<?php }
			else	{?>
				<?php echo $class->getValue('owner_name', true);?>
			<?php }?>
		</span>
		<!--jm:datalinkvalue nlsid="<?php echo $class->getString('PERMISSION');?>" value="<?php echo $class->getAttrValue('acl_name');?>" name="permission" width="140px" readonly="<?php echo $class->isReadOnly();?>"/-->
		<span><?php echo $class->getString('PERMISSION');?> :&nbsp;</span>
		<span class="label" style="width: auto;">
			<?php if ($class->isReadOnly() == 'false')	{?>
				<script>var _callback;</script>
				<a onclick="javascript:_callback = function(objectId, value)	{$('#permission').html(value);$('#permissionId').attr('value', objectId);};postServerEvent('objectlist', 'nest', 'selectpermission', null, 'objectId=<?php echo $class->getValue('r_object_id');?>');" class="actionbutton"><img src="/estancia/webapp/themes/default/images/icons/jm_acl_16.png"><span id="permission"><?php echo $class->getAttrValue('acl_name');?></span></a>
				<input type="hidden" value="" name="permissionId" id="permissionId">
			<?php }
			else	{?>
				<span id="permission" style="width: 120px;"><?php echo $class->getAttrValue('acl_name');?></span>
			<?php }?>
		</span>
	</div>
	<div class="line"><!-- --></div>
	<div class="labeltitle">
		<span><?php echo $class->getString('SIZE');?> :&nbsp;</span>
		<span class="label"><?php echo $class->getSize('long');?></span>
		<span><?php echo $class->getString('HISTORY');?> :&nbsp;</span>
		<span class="label">
			<a onclick="javascript:postServerEvent('objectlist', 'nest', 'history', null, 'objectId=<?php echo $class->getValue('r_object_id');?>');" class="actionbutton"><img src="/estancia/webapp/themes/default/images/icons/history_16.png"><span><?php echo $class->getString('VIEW');?></span></a>
		</span>
	</div>
	<div class="labeltitle">
		<span><?php echo $class->getString('TYPE');?> :&nbsp;</span>
		<span class="label"><?php echo $class->getValue('r_object_type');?></span>
		<span><?php echo $class->getString('VERSION');?> :&nbsp;</span>
		<span class="label"><?php echo $class->getAttrValue('r_version_label');?></span>
	</div>
	<div class="labeltitle">
		<span><?php echo $class->getString('ID');?> :&nbsp;</span>
		<span class="label"><?php echo $class->getValue('r_object_id');?></span>
	</div>
</div>
<div id="message"></div>
<div class="buttons">
<jm:button action="postServerEvent('sysobjproperties', 'return', null, 'onOk', null);" nlsid="SAVE" src="ok_16.png" cssclass="right" hidden="<?php echo $class->isReadOnly();?>"/>
<jm:button action="postServerEvent('properties', 'return', null, null, null);" nlsid="CANCEL" src="cancel_16.png" cssclass="right"/>
</div>
</jm:modal>