<jm:modal title="<?php echo $class->getString('HELP');?>" icon="help_48.png">
<div id="nestedobjectgridcontent">
<?php $class->render();?>
</div>
<div class="buttons">
<jm:button action="postServerEvent('help', 'return', null, 'onClose', null);" nlsid="CLOSE" src="cancel_16.png" cssclass="right"/>
</div>
</jm:modal>