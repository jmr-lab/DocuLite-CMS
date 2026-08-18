<jm:modal title="<?php echo $class->getString('CREATE');?>" icon="create.png" style="width: 600px;">
<span style="display: inline-block; width: 80px; margin-top: -4px; text-decoration:underline;"><?php echo $class->getString('NAME');?> :</span>
<input class="input_text" name="object_name" id="object_name" style="width: 400px" value="" type="text">
<br>
<div class="line"><!-- --></div>
<span style="display: inline-block; width: 80px; margin-top: -4px; text-decoration:underline;"><?php echo $class->getString('TYPE');?> :</span>
<div style="display: inline; width: 180px;">
<jm:dropdownlist name="type" id="type" style="width: 150px;">
<jm:option value="jm_document"><?php echo $class->getString('DOCUMENT');?></jm:option>
<jm:option value="jm_folder"><?php echo $class->getString('FOLDER');?></jm:option>
</jm:dropdownlist>
</div>
<br>
<div class="line"><!-- --></div>
<span style="display: inline-block; width: 80px; margin-top: -4px; text-decoration:underline;"><?php echo $class->getString('TEMPLATE');?> :</span>
<jm:datadropdownlist name="templates" query="SELECT r_object_id, object_name FROM jm_document_sp WHERE i_is_deleted = false AND r_object_id IN (SELECT r_object_id FROM jm_document_rp WHERE i_folder_id IN (SELECT r_object_id FROM jm_folder_sp WHERE object_name = 'Templates' AND r_object_id IN (SELECT DISTINCT r_object_id FROM jm_folder_rp WHERE i_folder_id = '123456'))) ORDER BY object_name"/>
<br>
<div class="line"><!-- --></div>
<span style="display: inline-block; width: 80px; margin-top: -4px; text-decoration:underline;"><?php echo $class->getString('PERMISSION');?> :</span>
<jm:datadropdownlist name="permissions" query="SELECT r_object_id, object_name FROM jm_acl_s WHERE r_is_internal = false OR (r_is_internal = true AND owner_name = '<?php echo $class->getUserName(); ?>') ORDER BY object_name" style="width: 150px;"/>
<br>
<div id="message"></div>
<div class="buttons">
<jm:button action="postServerEvent('create', 'return', null, 'onOk', null);" nlsid="OK" src="ok_16.png" cssclass="right"/>
<jm:button action="postServerEvent('objectlist', 'return', null, null, null);" nlsid="CANCEL" src="cancel_16.png" cssclass="right"/>
</div>
</jm:modal>
