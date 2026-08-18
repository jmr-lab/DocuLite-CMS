<?php $class->init();?>
<jm:modal title="<?php echo $class->getString('PERMISSION');?>" icon="<?php $class->getObjectIcon();?>">
<div class="content">
	<div class="labeltitle">
		<span><?php echo $class->getString('NAME');?> :&nbsp;</span>
		<span class="label"><?php echo $class->getValue('object_name');?></span>
		<span><?php echo $class->getString('OWNER');?> :&nbsp;</span>
		<span class="label"><?php echo $class->getValue('owner_name');?></span>
	</div>
	<div class="labeltitle">
		<span><?php echo $class->getString('ID');?> :&nbsp;</span>
		<span class="label"><?php echo $class->getValue('r_object_id');?></span>
	</div>
	<div class="line"><!-- --></div>
	<?php $class->getPermissionList();?>
</div>
<div class="buttons">
<!--jm:button action="postServerEvent('objectlist', 'nest', 'about', null, 'folderId=0000000000000000');" nlsid="SAVE" src="ok_16.png" cssclass="right"/-->
<jm:button action="postServerEvent('objectlist', 'return', null, null, null);" nlsid="CLOSE" src="cancel_16.png" cssclass="right"/>
</div>
</jm:modal>