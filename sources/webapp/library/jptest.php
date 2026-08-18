<div class="panel">
<?php
$request = new JcHttpServletRequest();
echo 'You have called the component <strong>'.$request->getParameter('component').'</strong>';
echo '<br>';
		echo $machin;
		echo $truc;
		echo $bidule;
		echo $chose;
echo 'with a <strong>'.$request->getParameter('event').'</strong> event!<br>';
echo 'And this is a list of users : ';
?>
<jm:datadropdownlist name="BtnContent" query="select r_object_id, user_name from jm_user_sp order by user_name" />
</div>