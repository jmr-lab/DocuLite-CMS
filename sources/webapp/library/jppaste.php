<jm:modal title="<?php echo $class->getString('PASTE');?>" icon="paste.png">
<?php $class->init();?>
<jm:breadcrumb values="<?php echo htmlentities(serialize($class->getBreadCrumbValues()));?>" displayLinks=""/>
<jm:datagrid id="nestedobjectgrid" data="<?php echo htmlentities(serialize($class->getDataGridValues()));?>"/>
<div id="message"></div>
<div class="buttons">
<jm:button action="postServerEvent('paste', 'return', null, 'onOk', null);" nlsid="OK" src="ok_16.png" cssclass="right"/>
<jm:button action="postServerEvent('objectlist', 'return', null, null, null);" nlsid="CANCEL" src="cancel_16.png" cssclass="right"/>
</div>
</jm:modal>