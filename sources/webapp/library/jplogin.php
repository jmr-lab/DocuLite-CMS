<jm:modal title="<?php echo $class->getString('LOGIN');?>" icon="login_48.png" style="width: 600px;">
<div class="attribute">
<span><?php echo $class->getString('NAME');?> : </span>
<input type="text" class="input_text" name="user_name" id="user_name" size="30" value="" />
</div>
<div class="attribute">
<span><?php echo $class->getString('PASSWORD');?> : </span>
<input type="password" class="input_text" name="password" id="password" size="30" value="" onkeypress="if (event.keyCode == 13) {postServerEvent('login', null, 'login', 'onOk', null);}" />
</div>
<div class="attribute">
<span><?php echo $class->getString('DOCBROKER');?> : </span>
<jm:dropdownlist name="docbroker" id="docbroker" style="width: 200px; font-size: 11px;">
<?php $class->getRepositories();?>
</jm:dropdownlist>
</div>
<div class="checkbox">
<input type="checkbox" name="keeploggedin" id="keeploggedin" size="30" checked />
<span style="display: inline-block; width: 300px;"><?php echo $class->getString('KEEP_LOGGED_IN');?></span>
</div>
<div class="attribute" id="message"></div>
<div class="buttons">
<jm:button action="postServerEvent('login', null, 'login', 'onOk', null);" nlsid="LOGIN" src="ok_16.png" cssclass="right"/>
</div>
</jm:modal>