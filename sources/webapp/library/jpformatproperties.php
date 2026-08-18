<?php $class->init();?>
<jm:modal title="<?php echo $class->getString('FORMAT');?>" icon="jm_format.png">
<div class="content">
	<div class="labeltitle">
		<span><?php echo $class->getString('NAME');?> :&nbsp;</span>
		<span style="width: 450px;" class="label"><?php echo $class->getValue('name');?></span>
	</div>
	<div class="labeltitle">
		<span><?php echo $class->getString('DESCRIPTION');?> :&nbsp;</span>
		<span style="width: 450px;" class="label"><?php echo $class->getValue('description');?></span>
	</div>
	<div class="line"><!-- --></div>
	<div class="labeltitle">
		<span><?php echo $class->getString('ID');?> :&nbsp;</span>
		<span class="label"><?php echo $class->getValue('r_object_id');?></span>
		<span><?php echo $class->getString('DOS_EXTENSION');?> :&nbsp;</span>
		<span class="label"><?php echo $class->getValue('dos_extension');?></span>
	</div>
</div>
<div class="buttons">
<!--jm:button action="postServerEvent('objectlist', 'nest', 'about', null, 'folderId=0000000000000000');" nlsid="SAVE" src="ok_16.png" cssclass="right"/-->
<jm:button action="postServerEvent('objectlist', 'return', null, null, null);" nlsid="CLOSE" src="cancel_16.png" cssclass="right"/>
</div>
</jm:modal>