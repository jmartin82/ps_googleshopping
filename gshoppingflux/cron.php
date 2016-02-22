<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/gshoppingflux.php');

$start = (float) array_sum(explode(' ',microtime()));
$module=new GShoppingFlux();
$shop_id = Shop::getContextShopID();
$module->generateShopFileList($shop_id);
$end = (float) array_sum(explode(' ',microtime()));
die ('OK, export completed successfully. '.($end-$start).'sec');

?>
