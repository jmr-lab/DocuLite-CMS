<jm:modal title="<?php echo $class->getString('WELCOME');?>" icon="welcome.png">
<div id="nestedobjectgridcontent">
<?php $class->render();?>
</div>
<div class="line"><!-- --></div>
<div class="checkbox">
<input type="checkbox" name="showmessage" id="showmessage" size="30" checked />
<span style="display: inline-block; width: 300px;"><?php echo $class->getString('SHOW_MESSAGE');?></span>
</div>
<div class="buttons">
<jm:button action="closeWindow();" nlsid="CLOSE" src="cancel_16.png" cssclass="right"/>
</div>
</jm:modal>