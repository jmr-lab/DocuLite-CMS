<?php $class->init();?>
<jm:modal title="<?php $class->getObjectName();?>" icon="<?php $class->getObjectIcon(false);?>" style="width: 600px;">
<span style="display: inline-block; width: 100px; text-decoration:underline;"><?php echo $class->getString('SIZE');?> :</span><span style="display: inline-block; width: 180px;"><?php echo $class->getSize('long');?></span>
<span style="display: inline-block; width: 100px; text-decoration:underline;"><?php echo $class->getString('OWNER');?> :</span><span style="display: inline-block; width: 180px;"><?php echo $class->getValue('owner_name');?></span>
<br>
<span style="display: inline-block; width: 100px; text-decoration:underline;"><?php echo $class->getString('TYPE');?> :</span><span style="display: inline-block; width: 180px;"><?php echo $class->getValue('r_object_type');?></span>
<span style="display: inline-block; width: 100px; text-decoration:underline;"><?php echo $class->getString('MODIFIED');?> :</span><span style="display: inline-block; width: 180px;"><?php echo $class->getValue('r_modify_date');?></span>
<br>
<div class="line"><!-- --></div>
<?php echo $class->getLink();?>
<br>
<div class="buttons">
<jm:button action="postServerEvent('objectlist', 'return', null, null, null);" nlsid="CLOSE" src="cancel_16.png" cssclass="right"/>
</div>
</jm:modal>
