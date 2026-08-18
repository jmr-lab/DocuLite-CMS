<jm:modal title="<?php echo $class->getString('IMPORT');?>" icon="checkin.png" style="width: 600px;">
<span style="display: inline-block; width: 80px; margin-top: -4px; text-decoration:underline;"><?php echo $class->getString('NAME');?> :</span>
<input class="input_text" name="object_name" id="object_name" style="width: 400px" value="" type="text">
<br>
<div class="line"><!-- --></div>
<span style="display: inline-block; width: 80px; margin-top: -4px; text-decoration:underline;"><?php echo $class->getString('FILE');?> :</span>
<input name="file[]" id="file" type="file" size="60" style="width: 400px" class="select_text" onchange="javascript:postServerEvent('import', null, null, 'selectFormat', null);">
<br>
<div class="line"><!-- --></div>
<span style="display: inline-block; width: 80px; margin-top: -4px; text-decoration:underline;"><?php echo $class->getString('FORMAT');?> :</span>
<jm:datadropdownlist name="formats" id="formats" query="SELECT r_object_id, description FROM jm_format_sp ORDER BY description" style="width: 400px"/>
<br>
<div class="line"><!-- --></div>
<span style="display: inline-block; width: 80px; margin-top: -4px; text-decoration:underline;"><?php echo $class->getString('PERMISSION');?> :</span>
<jm:datadropdownlist name="permissions" query="SELECT r_object_id, object_name FROM jm_acl_s WHERE r_is_internal = false OR (r_is_internal = true AND owner_name = '<?php echo $class->getUserName(); ?>') ORDER BY object_name" style="width: 150px;"/>
<br>
<div id="message"></div>
<div class="buttons">
<jm:button action="ajaxFileUpload('import');" nlsid="OK" src="ok_16.png" cssclass="right"/>
<jm:button action="postServerEvent('objectlist', 'return', null, null, null);" nlsid="CANCEL" src="cancel_16.png" cssclass="right"/>
</div>
</jm:modal>