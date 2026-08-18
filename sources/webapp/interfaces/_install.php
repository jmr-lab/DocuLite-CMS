<?php
@session_start();

if (!isset($_SESSION[user_name]) || $_SESSION[user_name] == '')
{
	if ($_POST['user_name'] == 'install' && md5($_POST['user_password']) == 'ccaa700adbc7f357d9ba783853827c7c')	{$_SESSION[user_name] = 'install';}
	else {return;}
}

if ($_POST['myname'] <> '')
{
//	$msg .= " MyName: " . $_POST['myname'];
//	$msg .= " Root: " . $_POST['root'];
	$folder = substr($_POST['myname'], strlen($_POST['root']) + 1);
	$msg .= " Folder: " . $folder;
//	mkdir($folder, 0700);
//	echo	"msg: " . $msg ;
}
else
{
	if(!empty($_FILES['userfile']['error']))
	{
		switch($_FILES['userfile']['error'])
		{
			case '1':
				$error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
				break;
			case '2':
				$error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
				break;
			case '3':
				$error = 'The uploaded file was only partially uploaded';
				break;
			case '4':
				$error = 'No file was uploaded.';
				break;
			case '6':
				$error = 'Missing a temporary folder';
				break;
			case '7':
				$error = 'Failed to write file to disk';
				break;
			case '8':
				$error = 'File upload stopped by extension';
				break;
			case '999':
			default:
				$error = 'No error code avaiable';
		}
		echo	"error: " . $error;
	}
	elseif(empty($_FILES['userfile']['tmp_name']) || $_FILES['userfile']['tmp_name'] == 'none')
	{
		$error = 'No file was uploaded..';
		echo	"error: " . $error;
	}
	else
	{
//			$msg .= " File path: " . $_POST['mypath'] . ", ";
//			$msg .= " File root: " . $_POST['root'];
//			$msg .= " File Name: " . $_FILES['userfile']['name'] . ", ";
//			$msg .= " File Size: " . @filesize($_FILES['userfile']['tmp_name']) . ", ";
//			$target_path = $GLOBALS[_SERVER_ROOT_].'/temp/'.basename($_FILES['myfile']['name']);
//			!@move_uploaded_file($_FILES['myfile']['tmp_name'], $target_path);

			$target = $_FILES['userfile'] ['name'];
			$target = $_SERVER['DOCUMENT_ROOT'].substr($_POST['mypath'], strlen($_POST['root']) + 2);
			$target = str_replace('\\\\', '/', $target);

			@move_uploaded_file ($_FILES['userfile'] ['tmp_name'], $target);

			$filename = $target;
			echo '<br>target : '.$target;
			$pos = strrpos($filename, ".end");
			echo '<br>pos : '.$pos;
			if (!($pos === false))
			{
				unlink($filename);
				$filename = substr($filename, 0, $pos);
				$pos = strrpos($filename, ".");
				$filenb = substr($filename, $pos + 1);
				$filename = substr($filename, 0, $pos);
				echo '<br>filename : '.$filename;
				if (file_exists($filename))	{unlink($filename);}
				for($i = 0; $i < $filenb; $i++)
				{
					$file = fopen($filename.'.'.$i, 'r') or die("can't open file");
					$theData = fread($file, filesize($filename.'.'.$i));
					fclose($file);
					unlink($filename.'.'.$i);
					$file = fopen($filename, 'a') or die("can't open file");
					fwrite($file, $theData);
					fclose($file);
				}
			}
	}
}
?>
