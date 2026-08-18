<?php $class->init();?>
<jm:modal title="<?php echo $class->getString('PROPERTIES');?>" icon="<?php $class->getObjectIcon();?>" style="width: 600px;">
<div class="content">
	<div class="labeltitle">
		<span><?php echo $class->getString('ID');?> :&nbsp;</span>
		<span class="label"><?php echo $class->getValue('r_object_id');?></span>
	</div>
	<div class="line"><!-- --></div>
	<div class="labeltitle">
		<span><?php echo $class->getString('TYPE');?> :&nbsp;</span>
		<span class="label"><?php echo $class->getObjectType();?></span>
	</div>
</div>
<div id="message"></div>
<div class="buttons">
<jm:button action="postServerEvent('properties', 'return', null, null, null);" nlsid="CANCEL" src="cancel_16.png" cssclass="right"/>
</div>
</jm:modal>