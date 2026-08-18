<jm:modal title="<?php echo $class->getString('CREATE');?>" icon="jm_group.png" style="width: 600px;">
<span style="display: inline-block; width: 80px; margin-top: -4px; text-decoration:underline;"><?php echo $class->getString('NAME');?> :</span>
<input class="input_text" name="object_name" id="object_name" style="width: 400px" value="" type="text">
<br>
<div class="line"><!-- --></div>
<span style="display: inline-block; width: 80px; margin-top: -4px; text-decoration:underline;"><?php echo $class->getString('TYPE');?> :</span>
<div style="display: inline; width: 180px;">
<jm:dropdownlist name="type" id="type" style="width: 150px;">
<jm:option value="jm_group"><?php echo $class->getString('GROUP');?></jm:option>
<?php if ($class->isWorld() == 'false') { ?>
<jm:option value="jm_user"><?php echo $class->getString('USER');?></jm:option>
<?php } ?>
</jm:dropdownlist>
</div>
<br>
<div class="line"><!-- --></div>
<span style="display: inline-block; width: 80px; margin-top: -4px; text-decoration:underline;"><?php echo $class->getString('LOGIN');?> :</span>
<input class="input_text" name="login" id="login" style="width: 400px" value="" type="text">
<br><br>
<span style="display: inline-block; width: 80px; margin-top: -4px; text-decoration:underline;"><?php echo $class->getString('PASSWORD');?> :</span>
<input class="input_text" name="password" id="password" style="width: 400px" value="" type="password">
<br><br>
<span style="display: inline-block; width: 80px; margin-top: -4px; text-decoration:underline;"><?php echo $class->getString('ADDRESS');?> :</span>
<input class="input_text" name="address" id="address" style="width: 400px" value="" type="text">
<br><br>
<div id="message"></div>
<div class="buttons">
<jm:button action="postServerEvent('creategroup', 'return', null, 'onOk', null);" nlsid="OK" src="ok_16.png" cssclass="right"/>
<jm:button action="postServerEvent('objectlist', 'return', null, null, null);" nlsid="CANCEL" src="cancel_16.png" cssclass="right"/>
</div>
</jm:modal>
