<?php $class->init(); ?>
<!-- iFrame -->
<jm:frame>
<div style="background-color: #F3F3F3; padding: 4px 8px 8px 8px; height: 56px;">
<!-- Headers -->
<div style="display: block;">
<div style="width: 60%; margin-bottom: 4px; display: inline;"><span id="subject" style="font-size:175%; font-family: Verdana;"><?php echo $class->getSubject(); ?></span></div>
<!-- Action buttons -->
<div style="margin-bottom: 4px; display: inline; position: absolute; right: 0px;">
<jm:actionbutton action="window.parent.postServerEvent('objectlist', 'open', 'writemessage', null, 'type=reply;messageId=<?php echo $class->getMessageId(); ?>');" nlsid="REPLY" src="reply_16.png"/>
<jm:actionbutton action="window.parent.postServerEvent('objectlist', 'open', 'writemessage', null, 'type=forward;messageId=<?php echo $class->getMessageId(); ?>');" nlsid="FORWARD" src="forward_16.png"/>
<div style="display: inline-block; width: 8px; height: 8px;"></div>
<!--a href="javascript:window.parent.postServerEvent('objectlist', 'nest', 'about', null, null);" style="color: #5F5F5F; width: 125px;"><div class="maillink"><img src="/estancia/webapp/themes/default/images/icons/recyclebin_16.png"><span>Delete</span></div></a-->
</div>
</div>
<div style="display: inline;"><span style="display: inline-block; width: 60px; color: #5F5F5F; font-family: Verdana,Arial; font-size: 12px; font-weight: bold; letter-spacing: 0; height: 15px;"><?php echo $class->getString('FROM'); ?> : </span><span style="display: inline-block; width: 200px; color: #8F8F8F; font-family: Verdana,Arial; font-size: 11px; padding-left: 4px; height: 15px;"><?php echo $class->getSender(); ?></span></div>
<div style="display: inline; position: absolute; right: 16px;"><span style="display: inline-block; width: 60px; color: #5F5F5F; font-family: Verdana,Arial; font-size: 12px; font-weight: bold; letter-spacing: 0; height: 15px;"><?php echo $class->getString('SENT_DATE'); ?> : </span><span style="display: inline-block; color: #8F8F8F; font-family: Verdana,Arial; font-size: 11px; padding-left: 4px; height: 15px;"><?php echo $class->getDateSent(); ?></span></div>
<div><span style="display: inline-block; width: 60px; color: #5F5F5F; font-family: Verdana,Arial; font-size: 12px; font-weight: bold; letter-spacing: 0; height: 15px;"><?php echo $class->getString('TO'); ?> : </span><span style="display: inline-block; width: 200px; color: #8F8F8F; font-family: Verdana,Arial; font-size: 11px; padding-left: 4px; height: 15px;"><?php echo $class->getRecipient(); ?></span></div>
</div>
<!-- Content -->
<div id="mailbody" style="width: 100%; text-align: center; margin: 0 auto 0 auto; border-top: 1px solid #E5E5E5; overflow-y: scroll; display: none;">
<div style="margin: 16px 8px 8px 8px; color: #8F8F8F; font-family: Verdana,Arial; padding-left: 4px; text-align: justify; font-size: 12px;"><?php echo $class->getContent(); ?></div>
</div>
<!-- Close iFrame -->
</jm:frame>
