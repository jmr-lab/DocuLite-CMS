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
<jm:actionlink action="postServerEvent('objectlist', 'nest', 'home', null, 'folderId=0000000000000000');" nlsid="OPEN"/>
<jm:actionlink action="postServerEvent('objectlist', 'return', 'home', null, 'folderId=0000000000000000');" nlsid="CLOSE"/>
1<br>
2<br>
3<br>
4<br>
5<br>
6<br>
7<br>
8<br>
9<br>
10<br>
11<br>
12<br>
13<br>
14<br>
15<br>
16<br>
17<br>
18<br>
19<br>
20<br>
21
</div>