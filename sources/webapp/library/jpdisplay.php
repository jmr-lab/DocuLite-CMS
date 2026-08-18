<?php $class->init(); ?>
<div class="modal">
<?php $class->getContent();?>
<div style="display: inline; width: 50%; position: relative; top: 12px;">
<?php echo $class->getLink();?>
<jm:actionlink action="postServerEvent('objectlist', 'return', null, null, null);" cssclass="viewbutton" src="cancel_16.png" nlsid="CLOSE"/>
</div>
</div>
