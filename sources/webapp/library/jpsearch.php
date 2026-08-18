<jm:modal title="<?php echo $class->getString('SEARCH');?>" icon="search_48.png">
<div>
<span style="display: inline-block; width: 100px; text-decoration:underline;"><?php echo $class->getString('TYPE');?> :</span>
<div style="display: inline; width: 180px;">
<jm:dropdownlist name="type" id="type" style="width: 150px;">
<jm:option value="jm_document"><?php echo $class->getString('DOCUMENT');?></jm:option>
<jm:option value="jm_folder"><?php echo $class->getString('FOLDER');?></jm:option>
<jm:option value="jmi_queue_item"><?php echo $class->getString('MAIL');?></jm:option>
<jm:option value="jm_format"><?php echo $class->getString('FORMAT');?></jm:option>
</jm:dropdownlist>
</div>
</div>
<div style="margin-top: 8px;">
<span style="display: inline-block; width: 100px; text-decoration:underline;"><?php echo $class->getString('FIELD');?> :</span>
<div style="display: inline; width: 180px;">
<jm:dropdownlist name="field" id="field" style="width: 150px;">
<jm:option value="object_name"><?php echo $class->getString('NAME');?></jm:option>
<jm:option value="authors"><?php echo $class->getString('AUTHOR');?></jm:option>
<jm:option value="owner"><?php echo $class->getString('OWNER');?></jm:option>
</jm:dropdownlist>
</div>
<div style="display: inline; width: 180px;">
<jm:dropdownlist name="condition" id="condition" style="width: 150px;">
<jm:option value="contains"><?php echo $class->getString('CONTAINS');?></jm:option>
<jm:option value="doesnotcontain"><?php echo $class->getString('DOES NOT CONTAIN');?></jm:option>
<jm:option value="is"><?php echo $class->getString('IS');?></jm:option>
<jm:option value="isnot"><?php echo $class->getString('IS NOT');?></jm:option>
</jm:dropdownlist>
</div>
<div style="display: inline; width: 250px;">
<input type="text" class="input_text" name="input_text" id="input_text" size="30" value="" style="width: 200px;" />
</div>
</div>
<div id="message"></div>
<div class="buttons">
<jm:button action="postServerEvent(null, 'return', 'searchresults', null, null);" nlsid="SEARCH" src="ok_16.png" cssclass="right"/>
<jm:button action="postServerEvent(null, 'return', null, null, null);" nlsid="CANCEL" src="cancel_16.png" cssclass="right"/>
</div>
</jm:modal>