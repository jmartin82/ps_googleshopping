<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/gshoppingflux.php');

$module=new GShoppingFlux();
$shop_id = Shop::getContextShopID();
$module->generateShopFileList($shop_id);
die ('OK, export completed successfully.');

?>
