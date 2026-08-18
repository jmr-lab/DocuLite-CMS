<?php
/**
 * This is the new version of Estancia CMS (Content Management System)
 * The current file is used to display the result of each Ajax call.
 *
 * @author	Jean-Marie Roy
 * @version	3.0
 */

include 'interface.php';
$request = new JcHttpServletRequest();
$servereventmgr = new JcServerEventManager($request);
?>
<jm:panel component="viewmessage" id="viewmessage"/>
