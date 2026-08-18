<?php $class->init();?>
<jm:modal title="<?php $class->getTitle();?>" icon="<?php $class->getObjectIcon();?>">
<div class="content">
	<div class="labeltitle">
		<span><?php echo $class->getString('DESCRIPTION');?> :&nbsp;</span>
		<span class="label"><?php echo $class->getDescription();?></span>
		<span><?php echo $class->getString('SENT_DATE');?> :&nbsp;</span>
		<span class="label"><?php echo $class->getValue('date_sent');?></span>
	</div>
	<div class="line"><!-- --></div>
	<div class="labeltitle">
		<span><?php echo $class->getString('FROM');?> :&nbsp;</span>
		<span class="label"><?php echo $class->getValue('sent_by');?></span>
		<span><?php echo $class->getString('TO');?> :&nbsp;</span>
		<span class="label"><?php echo $class->getValue('name');?></span>
	</div>
	<div class="line"><!-- --></div>
	<div class="labeltitle">
		<span><?php echo $class->getString('ID');?> :&nbsp;</span>
		<span style="width: 450px;" class="label"><?php echo $class->getValue('r_object_id');?></span>
	</div>
</div>
<div class="buttons">
<!--jm:button action="postServerEvent('objectlist', 'nest', 'about', null, 'folderId=0000000000000000');" nlsid="SAVE" src="ok_16.png" cssclass="right"/-->
<jm:button action="postServerEvent('objectlist', 'return', null, null, null);" nlsid="CLOSE" src="cancel_16.png" cssclass="right"/>
</div>
</jm:modal>