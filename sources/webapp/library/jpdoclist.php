<?php $class->init();?>
<jm:title icon="<?php echo $class->getTitleImage();?>" title="<?php echo htmlentities($class->getTitleName());?>" path="<?php echo htmlentities(serialize($class->getTitlePath()));?>" configuration="true"/>
<jm:breadcrumb values="<?php echo htmlentities(serialize($class->getBreadCrumbValues()));?>" displayLinks="true"/>
<jm:datagrid id="objectgrid" data="<?php echo htmlentities(serialize($class->getDataGridValues()));?>"/>
