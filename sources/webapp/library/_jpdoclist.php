<div class="panel">
<?php
echo '--- Begin ('.getTime().') --- <br>';

 /**
 * Get the current date time
 *
 * This function returns the current date time including milliseconds.
 *
 * @return String the date time
 */
function getTime()
{
	$milli = floor(1000 * microtime());
	while (strlen($milli) < 3)	{$milli = '0'.$milli;}
	return date("H:i:s").','.$milli;
}

/**
 * Get the current date time
 *
 * This function returns the current date time including milliseconds.
 *
 * @return String the date time
 */
function getMessage($message)
{
	return '<br>'.$message.';'.getTime();
}

/**
 * Tests the performances of the JMFC (main)
 */
try
{
	// Tests modification of a repeating attribute of an object (v3.0)
	echo '<br>Repeating v3.0 ('.getTime().')<br>';
	$sessionmanager = new JfSessionManager();
	$session = $sessionmanager->getSession('www_jmroy');
	$perObj = $session->getObject(new JfId('09001e240ffe1ca9'));
	$perObj->setValue('subject', 'Subject changed by JMR');
	$perObj->setRepeatingValue('i_folder_id', '1', '0b001e2400786532');
	$perObj->setRepeatingValue('i_folder_id', '27', '0b001e2400AAAAAA');
	$perObj->setRepeatingValue('i_folder_id', '3', '0b001e2400BBBBBB');
	$perObj->setRepeatingValue('i_folder_id', '4', '0b001e2400CCCCCC');
	$perObj->save();
	echo '<br>';
	for ($i = 0; $i < $perObj->getValueCount('i_folder_id'); $i++)
	{
		echo '$perObj->getRepeatingValue(\'i_folder_id\', '.$i.') : '.$perObj->getRepeatingValue('i_folder_id', $i);
		echo '<br>';
	}
	echo '<br>';
	$sysObj = JfUtils::cast($perObj, 'JfSysObject');
	echo '$sysObj : '.get_class($sysObj).'<br>';
	$sysObj->removeAll('i_folder_id');
	$sysObj->setRepeatingValue('i_folder_id', '0', '0b001e240ff52128');
	$sysObj->save();
	echo '<br>';
	for ($i = 0; $i < $sysObj->getValueCount('i_folder_id'); $i++)
	{
		echo '$sysObj->getRepeatingValue(\'i_folder_id\', '.$i.') : '.$sysObj->getRepeatingValue('i_folder_id', $i);
		echo '<br>';
	}
	echo '<br>';
	echo '<jm:button value="BtnTest" label="Ok"/>';
	// SELECT * FROM jm_sysobject_s WHERE r_object_id = '09001e240ffe1ca9'
	// SELECT * FROM jm_sysobject_r WHERE r_object_id = '09001e240ffe1ca9'
}
catch (Exception $e)
{
	echo '<br><span style="color: red; font-weight: bold;">'.$e->getMessage().'</span><br>';
}
echo '<br> --- End ('.getTime().') --- <br>';
?>
<br>
<jm:button value="BtnEnd" name="test name" label="Cancel" id="test id" />
<br>
<jm:datadropdownlist name="BtnContent" query="select r_object_id, user_name from jm_user_sp where user_name like 'a%' order by user_name" />
<br>
<jm:datadropdownlist name="BtnContent" query="select r_object_id, user_name from jm_user_sp order by user_name" />
<br>
<span>This is the doclist component</span>
<jm:dropdownlist name="BtnContent">
<jm:option value="BtnAnotherContent">This is option 1</jm:option>
<jm:option value="BtnAnotherContent">This is option 2</jm:option>
<jm:option value="BtnAnotherContent">This is option 3</jm:option>
</jm:dropdownlist>
<br>
And this is my first link : <jm:actionlink action="postServerEvent('objectlist', 'jump', 'doclist', null, 'folderId=0000000000000000');" nlsid="CLICK_HERE"/>
</div>