<jm:modal title="<?php echo $class->getString('ABOUT');?>" icon="about.png" style="width: 600px;">
<span style="font-weight: bold;">Estancia 3.1</span>
<br><br>
<span style="display: inline-block; width: 80px; text-decoration:underline;"><?php echo $class->getString('VERSION');?> :</span>3.1.2.091
<br>
<span style="display: inline-block; width: 80px; text-decoration:underline;"><?php echo $class->getString('AUTHOR');?> :</span>Jean-Marie Roy
<br><br>
Copyright © 2011 Jean-Marie Roy. <?php echo $class->getString('COPYRIGHT');?>
<br>
<div class="line"><!-- --></div>
<span style="display: inline-block; width: 130px; text-decoration:underline;"><?php echo $class->getString('DATABASE');?> :</span><span style="display: inline-block; width: 150px;">MySQL</span>
<span style="display: inline-block; width: 130px; text-decoration:underline;"><?php echo $class->getString('LANGUAGE');?> :</span><span style="display: inline-block; width: 150px;">PHP</span>
<br>
<span style="display: inline-block; width: 130px; text-decoration:underline;"><?php echo $class->getString('SCRIPTS');?> :</span><span style="display: inline-block; width: 150px;">jQuery & jQuery UI</span>
<span style="display: inline-block; width: 130px; text-decoration:underline;"><?php echo $class->getString('THEME');?> :</span><span style="display: inline-block; width: 150px;">Crystal Project</span>
<br>
<div class="buttons">
<!--jm:button action="postServerEvent('objectlist', 'nest', 'about', null, 'folderId=0000000000000000');" nlsid="OPEN" src="ok_16.png" cssclass="right"/-->
<jm:button action="postServerEvent('objectlist', 'return', null, null, null);" nlsid="CLOSE" src="cancel_16.png" cssclass="right"/>
</div>
</jm:modal>