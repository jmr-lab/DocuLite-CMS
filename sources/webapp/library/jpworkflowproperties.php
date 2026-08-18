<?php $class->init();?>
<jm:modal title="<?php echo $class->getString('WORKFLOW');?>" icon="jm_workflow.png">
<div class="content">
	<div class="labeltitle">
		<span><?php echo $class->getString('NAME');?> :&nbsp;</span>
		<span style="width: 450px;" class="label"><?php echo $class->getValue('object_name');?></span>
	</div>
	<div class="line"><!-- --></div>
	<div class="labeltitle">
		<span><?php echo $class->getString('CREATOR');?> :&nbsp;</span>
		<span class="label"><?php echo $class->getValue('r_creator_name');?></span>
		<span><?php echo $class->getString('SUPERVISOR');?> :&nbsp;</span>
		<span class="label"><?php echo $class->getValue('supervisor_name');?></span>
	</div>
	<div class="line"><!-- --></div>
	<div class="labeltitle">
		<span><?php echo $class->getString('STARTED');?> :&nbsp;</span>
		<span class="label"><?php echo $class->getValue('r_start_date');?></span>
		<span><?php echo $class->getString('ID');?> :&nbsp;</span>
		<span class="label"><?php echo $class->getValue('r_object_id');?></span>
	</div>
</div>
<div class="buttons">
<!--jm:button action="postServerEvent('objectlist', 'nest', 'about', null, 'folderId=0000000000000000');" nlsid="SAVE" src="ok_16.png" cssclass="right"/-->
<jm:button action="postServerEvent('objectlist', 'return', null, null, null);" nlsid="CLOSE" src="cancel_16.png" cssclass="right"/>
</div>
</jm:modal>