<jm:modal title="<?php echo $class->getString('HISTORY');?>" icon="history.png">
<?php $class->init();?>
<jm:breadcrumb values="<?php echo htmlentities(serialize($class->getBreadCrumbValues()));?>" displayLinks=""/>
<jm:datagrid id="nestedobjectgrid" data="<?php echo htmlentities(serialize($class->getDataGridValues()));?>"/>
<div class="buttons">
<jm:button action="postServerEvent('objectlist', 'return', null, null, null);" nlsid="CLOSE" src="cancel_16.png" cssclass="right"/>
</div>
</jm:modal>