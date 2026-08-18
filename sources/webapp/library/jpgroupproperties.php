<?php $class->init();?>
<jm:modal title="<?php $class->getTitle();?>" icon="<?php $class->getObjectIcon();?>">
<div class="content">
	<div class="labeltitle">
		<jm:datatextvalue nlsid="<?php echo $class->getString('NAME');?>" value="<?php echo $class->getObjectName();?>" name="object_name" width="450px" readonly="<?php echo $class->isReadOnly();?>"/>
	</div>
	<div class="labeltitle">
		<jm:datatextvalue nlsid="<?php echo $class->getString('EMAIL');?>" value="<?php echo $class->getValue('group_address');?>" name="email" width="450px" readonly="<?php echo $class->isReadOnly();?>"/>
	</div>
	<div class="labeltitle">
		<span><?php echo $class->getString('OWNER');?> :&nbsp;</span>
		<span class="label"><?php echo $class->getValue('owner_name');?></span>
	</div>
	<div class="line"><!-- --></div>
	<div class="labeltitle">
		<span><?php echo $class->getString('TYPE');?> :&nbsp;</span>
		<span class="label"><?php echo $class->getValue('r_object_type');?></span>
		<span><?php echo $class->getString('HISTORY');?> :&nbsp;</span>
		<span><a href="javascript:postServerEvent('objectlist', 'nest', 'history', null, 'folderId=<?php echo $class->getValue('r_object_id');?>');"><?php echo $class->getString('VIEW');?></a></span>
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