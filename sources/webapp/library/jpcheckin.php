<jm:modal title="<?php echo $class->getString('CHECKIN');?>" icon="checkin.png">
<?php $class->init();?>
<jm:datagrid id="nestedobjectgrid" data="<?php echo htmlentities(serialize($class->getDataGridValues()));?>"/>
<div id="message"></div>
<div class="buttons">
<!--jm:button action="postServerEvent('checkin', 'return', null, 'onOk', null);" nlsid="OK" src="ok_16.png" cssclass="right"/-->
<jm:button action="ajaxFileUpload('checkin');" nlsid="OK" src="ok_16.png" cssclass="right"/>
<jm:button action="postServerEvent('objectlist', 'return', null, null, null);" nlsid="CANCEL" src="cancel_16.png" cssclass="right"/>
</div>
</jm:modal>