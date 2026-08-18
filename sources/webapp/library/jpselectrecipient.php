<jm:modal title="<?php echo $class->getString('SELECT_USER');?>" icon="jm_group.png" cssclass="selectrecipient">
<?php $class->init();?>
<jm:breadcrumb values="<?php echo htmlentities(serialize($class->getBreadCrumbValues()));?>" displayLinks=""/>
<jm:datagrid id="nestedobjectgrid" data="<?php echo htmlentities(serialize($class->getDataGridValues()));?>"/>
<div style="border-top: 1px solid grey; padding-top: 6px;">
<jm:mailfield action="doAddUser(this);" nlsid="TO"><?php echo $class->getRecipients('TO');?></jm:mailfield>
<jm:mailfield action="doAddUser(this);" nlsid="CC"><?php echo $class->getRecipients('CC');?></jm:mailfield>
<jm:mailfield action="doAddUser(this);" nlsid="BCC"><?php echo $class->getRecipients('BCC');?></jm:mailfield>
</div>
<div class="buttons">
<jm:button action="postServerEvent('selectrecipient', 'return', null, 'onOk', null);" nlsid="SAVE" src="ok_16.png" cssclass="right"/>
<jm:button action="postServerEvent('selectuser', 'return', null, 'onClose', null);" nlsid="CLOSE" src="cancel_16.png" cssclass="right"/>
</div>
</jm:modal>