<?php $class->init();?>
<jm:modal title="<?php echo $class->getString('CONFIGURATION');?>" icon="configuration_48.png" style="width: 750px;">
<div>
<span style="display: inline-block; width: 150px; text-decoration:underline;"><?php echo $class->getString('LANGUAGE');?> :</span>
<div style="display: inline; width: 200px;">
<jm:dropdownlist name="language" id="language" style="width: 150px;">
<jm:option value="fr"<?php echo $class->getDefault('language', 'fr');?>><?php echo $class->getString('FRENCH');?></jm:option>
<jm:option value="en"<?php echo $class->getDefault('language', 'en');?>><?php echo $class->getString('ENGLISH');?></jm:option>
<jm:option value="es"<?php echo $class->getDefault('language', 'es');?>><?php echo $class->getString('SPANISH');?></jm:option>
<jm:option value="de"<?php echo $class->getDefault('language', 'de');?>><?php echo $class->getString('GERMAN');?></jm:option>
</jm:dropdownlist>
</div>
<span style="display: inline-block; width: 150px; text-decoration:underline;"><?php echo $class->getString('NUMBER_RESULTS');?> :</span>
<div style="display: inline; width: 200px;">
<jm:dropdownlist name="results" id="results">
<jm:option value="5"<?php echo $class->getDefault('results', '5');?>><?php echo $class->getString('5');?></jm:option>
<jm:option value="10"<?php echo $class->getDefault('results', '10');?>><?php echo $class->getString('10');?></jm:option>
<jm:option value="20"<?php echo $class->getDefault('results', '20');?>><?php echo $class->getString('20');?></jm:option>
<jm:option value="30"<?php echo $class->getDefault('results', '30');?>><?php echo $class->getString('30');?></jm:option>
<jm:option value="50"<?php echo $class->getDefault('results', '50');?>><?php echo $class->getString('50');?></jm:option>
<jm:option value="100"<?php echo $class->getDefault('results', '100');?>><?php echo $class->getString('100');?></jm:option>
</jm:dropdownlist>
</div>
</div>
<div style="margin-top: 8px;">
<span style="display: inline-block; width: 150px; text-decoration:underline;"><?php echo $class->getString('INITIAL_VIEW');?> :</span>
<div style="display: inline; width: 200px;">
<jm:dropdownlist name="home" id="home" style="width: 150px;">
<jm:option value="home"<?php echo $class->getDefault('home', 'home');?>><?php echo $class->getString('HOME');?></jm:option>
<jm:option value="inbox"<?php echo $class->getDefault('home', 'inbox');?>><?php echo $class->getString('INBOX');?></jm:option>
<jm:option value="mydocuments"<?php echo $class->getDefault('home', 'mydocuments');?>><?php echo $class->getString('MY_DOCUMENTS');?></jm:option>
</jm:dropdownlist>
</div>
<span style="display: inline-block; width: 150px; text-decoration:underline;"><?php echo $class->getString('PREFERRED_DISPLAY');?> :</span>
<div style="display: inline; width: 200px;">
<jm:dropdownlist name="display" id="display" style="width: 150px;">
<jm:option value="thumbnails"<?php echo $class->getDefault('display', 'thumbnails');?>><?php echo $class->getString('THUMBNAILS');?></jm:option>
<jm:option value="details"<?php echo $class->getDefault('display', 'details');?>><?php echo $class->getString('DETAILS');?></jm:option>
</jm:dropdownlist>
</div>
</div>
<span class="warningrefresh"><?php echo $class->getString('WARNING_REFRESH');?></span>
<div id="message"></div>
<div class="buttons">
<jm:button action="postServerEvent('configuration', 'return', null, 'onOk', null);" nlsid="SAVE" src="ok_16.png" cssclass="right"/>
<jm:button action="postServerEvent('configuration', 'return', null, null, null);" nlsid="CANCEL" src="cancel_16.png" cssclass="right"/>
</div>
</jm:modal>