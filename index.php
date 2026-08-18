<?php
/**
 * This is the new version of Estancia CMS (Content Management System)
 * The current file is used to display the content of the main page.
 *
 * @author	Jean-Marie Roy
 * @version	3.0
 */

include_once './webapp/interfaces/interface.php';
$servereventmgr = new JcServerEventManager(null);
$component = $servereventmgr->getComponent();
?>
<body>
<jm:panel component="toolbar" id="toolbar" cssclass="toolbar"/>
<jm:panel component="navigationpane" id="navigationpane" cssclass="navigationpane"/>
<div id="mainpanel">
	<jm:panel component="<?php echo $component;?>" id="objectlist" cssclass="objectlist"/>
	<jm:tabs id="tabs" />
</div>
</body>
</html>