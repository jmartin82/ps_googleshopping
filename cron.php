<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/ps_googleshopping.php');

$module=new GoogleShopping();
$module->generateFileList();
die ('OK');

?>
