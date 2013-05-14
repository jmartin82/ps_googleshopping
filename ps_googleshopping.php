<?php
if (!defined('_PS_VERSION_'))
    exit;

/*
 * Google Shopping
 * @prestashop
 * Version 1.5.31
 * Jordi Martin (jordimartin@gmail.com)
 *  API
 * https://developers.google.com/shopping-content/getting-started/requirements-products
 * Based on idea:
 * (http://www.igwane.com/fr/license)
 */

class GoogleShopping extends Module
{
    function __construct()
    {
        $this->name    = 'ps_googleshopping';
        $this->tab     = 'export';
        $this->version = '1.0';
        $this->author  = '@jordi_martin';
        
        parent::__construct();
        
        $this->page        = basename(__FILE__, '.php');
        $this->displayName = $this->l('Google Shopping');
        $this->description = $this->l('Export Google Shoping products');
        
        $this->need_instance          = 0;
        $this->ps_versions_compliancy = array(
            'min' => '1.5',
            'max' => '1.6'
        );
        
        $this->uri = ToolsCore::getCurrentUrlProtocolPrefix() .$this->context->shop->domain_ssl.$this->context->shop->physical_uri;
    }
    
    function install()
    {
        if (!parent::install()) {
            return false;
        }
        return true;
    }
    
    
    public function getContent()
    {
        if (isset($_POST['generate'])) {
        	//shipping price
            if (isset($_POST['shipping'])) {
                Configuration::updateValue('GS_SHIPPING', $_POST['shipping']);
                
            }
            //image type
            if (isset($_POST['image'])) {
                Configuration::updateValue('GS_IMAGE', $_POST['image']);
            }
            
            //country
            if (isset($_POST['country'])) {
                Configuration::updateValue('GS_COUNTRY', $_POST['country']);
            }
            
            
            // Get installed languages
            $languages = Language::getLanguages();
            foreach ($languages as $i => $lang) {
                if (isset($_POST['product_type_' . $lang['iso_code']])) {
                    Configuration::updateValue('GS_PRODUCT_TYPE_' . $lang['iso_code'], $_POST['product_type_' . $lang['iso_code']]);
                }
            }
            
            
            // Get generation file route
            if (isset($_POST['generate_root']) && $_POST['generate_root'] === "on") {
                Configuration::updateValue('GENERATE_FILE_IN_ROOT', intval(1));
                
            } else {
                Configuration::updateValue('GENERATE_FILE_IN_ROOT', intval(0));
                @mkdir($path_parts["dirname"] . '/file_exports', 0755, true);
                @chmod($path_parts["dirname"] . '/file_exports', 0755);
            }

            //Code EAN13
            if (isset($_POST['gtin']) && $_POST['gtin'] === "on") {
                Configuration::updateValue('GTIN', intval(1));
            } else {
                Configuration::updateValue('GTIN', intval(0));
            }
            
            //Manufacturer Part Number (MPN)
            if (isset($_POST['mpn']) && $_POST['mpn'] === "on") {
                Configuration::updateValue('MPN', intval(1));
            } else {
                Configuration::updateValue('MPN', intval(0));
            }

            // QTY
            if (isset($_POST['quantity']) && $_POST['quantity'] === "on") {
                Configuration::updateValue('QUANTITY', intval(1));
            } else {
                Configuration::updateValue('QUANTITY', intval(0));
            }
            
            // Brand
            if (isset($_POST['brand']) && $_POST['brand'] === "on") {
                Configuration::updateValue('BRAND', intval(1));
            } else {
                Configuration::updateValue('BRAND', intval(0));
            }
            // Description
            if (isset($_POST['description']) && $_POST['description'] != 0) {
                Configuration::updateValue('DESCRIPTION', intval($_POST['description']));
            }
            
            //Feature products
            if (isset($_POST['featured_product']) && $_POST['featured_product'] === "on") {
                Configuration::updateValue('FEATURED_PRODUCT', intval(1));
            } else {
                Configuration::updateValue('FEATURED_PRODUCT', intval(0));
            }

            //Category shop
            if (isset($_POST['category_shop']) && $_POST['category_shop'] === "on") {
                Configuration::updateValue('CATEGORY_SHOP', intval(1));
            } else {
                Configuration::updateValue('CATEGORY_SHOP', intval(0));
            }
            
            $this->generateFileList();
        }
        
        $output = '<h2>' . $this->displayName . '</h2>';
        $output .= $this->_displayForm();
        
        // Link to generated files
        $output .= '<fieldset class="space width3">
						<legend>' . $this->l('Files') . '</legend>
						<p><b>' . $this->l('Generated link files') . '</b></p>';
        
        // Get active langs on shop
        $languages = Language::getLanguages();
        
        
        foreach ($languages as $i => $lang) {
            if (Configuration::get('GENERATE_FILE_IN_ROOT') == 1) {
                $get_file_url = $this->uri. 'googleshopping-' . $lang['iso_code'] . '.xml';
            } else {
                $get_file_url = $this->uri. 'modules/' . $this->getName() . '/file_exports/googleshopping-' . $lang['iso_code'] . '.xml';
            }
            
            $output .= '<a href="' . $get_file_url . '">' . $get_file_url . '</a><br />';
        }
        
        $output .= '<hr>';
        $output .='<p><b>'.$this->l('Automatic file generation').'</b></p>';
		$output .= $this->l('Install a CRON rule to update the feed frequently');
		$output .= '<br/>';
		$output .= $this->uri. 'modules/' . $this->getName() . '/cron.php' . '</p>';
		$output .= '</fieldset>';
        
        
        return $output;
    }
    
    private function _displayForm()
    {
        
        $options               = '';
        $mpn                   = '';
        $generate_file_in_root = '';
        $quantity              = '';
        $brand                 = '';
        $gtin                  = '';
        $selected_short        = '';
        $selected_long         = '';
        $featured_product      = '';
        $category_shop         = '';
        
        // Check if you want generate file on root
        if (Configuration::get('GENERATE_FILE_IN_ROOT') == 1) {
            $generate_file_in_root = "checked";
        }
        
        // googleshopping optional tags
        if (Configuration::get('GTIN') == 1) {
            $gtin = "checked";
        }
        if (Configuration::get('MPN') == 1) {
            $mpn = "checked";
        }
        if (Configuration::get('QUANTITY') == 1) {
            $quantity = "checked";
        }
        if (Configuration::get('BRAND') == 1) {
            $brand = "checked";
        }
        if (Configuration::get('FEATURED_PRODUCT') == 1) {
            $featured_product = "checked";
        }
        if (Configuration::get('CATEGORY_SHOP') == 1) {
            $category_shop = "checked";
        }
        
        (intval(Configuration::get('DESCRIPTION')) === intval(1)) ? $selected_short = "selected" : $selected_long = "selected";
        
        $form = '
		<form action="' . $_SERVER['REQUEST_URI'] . '" method="post">


		<fieldset style="float: right; width: 255px">
					<legend>' . $this->l('About') . '</legend>
					<p style="font-size: 1.5em; font-weight: bold; padding-bottom: 0">' . $this->displayName . ' ' . $this->version . '</p>
					<p style="clear: both">
					' . $this->description . '
					</p>
		</fieldset>

		<fieldset class="space width3">
		<legend>' . $this->l('Parameters') . '</legend>';
        
        $form .= '
			<label>' . $this->l('Description Type') . ' </label>
			<div class="margin-form">
				<select name="description">
					<option value="1" ' . $selected_short . '>' . $this->l('Short Description') . '</option>
					<option value="2" ' . $selected_long . '>' . $this->l('Long Description') . '</option>
				</select>
			</div>';
        
        // Récupération des langues actives pour la boutique
        $languages = Language::getLanguages();
        foreach ($languages as $i => $lang) {
            $form .= '<label title="product_type_' . $lang['iso_code'] . '">' . $this->l('Google Category') . ' ' . strtoupper($lang['iso_code']) . '</label>
			<div class="margin-form">
				<input type="text" name="product_type_' . $lang['iso_code'] . '" value="' . Configuration::get('GS_PRODUCT_TYPE_' . $lang['iso_code']) . '" size="40">
				<br />(<a href="http://www.google.com/support/merchants/bin/answer.py?answer=160081&query=product_type" target="_blank">' . $this->l('See Google Category') . '</a>)
			</div>';
        }

        
        $form .= '<label title="[shipping]">' . $this->l('Shipping') . ' </label>
			<div class="margin-form">
				<input type="text" name="shipping" value="' . Configuration::get('GS_SHIPPING') . '">
			</div>

			<label title="[country]">' . $this->l('Shipping Country') . ' </label>
			<div class="margin-form">
				<input type="text" name="country" value="' . ((Configuration::get('GS_COUNTRY') != '') ? (Configuration::get('GS_COUNTRY')) : 'EN') . '">
			</div>
			
			<label title="[image]">' . $this->l('Image Type') . ' </label>
			<div class="margin-form">
				<input type="text" name="image" value="' . ((Configuration::get('GS_IMAGE') != '') ? (Configuration::get('GS_IMAGE')) : 'large_default') . '">
			</div>

			<hr>

			<table>
				<tr>
					<td><label>' . $this->l('Generate the files to the root of the site') . '</label></td>
					<td><input type="checkbox" name="generate_root" ' . $generate_file_in_root . '></td>
				</tr>
				<tr>
					<td><label>' . $this->l('Categories breadcrumb shop') . '</label></td>
					<td><input type="checkbox" name="category_shop" ' . $category_shop . '></td>
				</tr>
				<tr>
					<td><label>' . $this->l('Manufacturers References') . '</label></td>
					<td><input type="checkbox" name="mpn" ' . $mpn . ' title="' . $this->l('Recomended') . '"></td>
				</tr>
				<tr>
					<td><label>' . $this->l('Number of products') . '</label></td>
					<td><input type="checkbox" name="quantity" ' . $quantity . ' title="' . $this->l('Recomended') . '"></td>
				</tr>
				<tr>
					<td><label title="[brand]">' . $this->l('Brand') . '</label></td>
					<td><input type="checkbox" name="brand" ' . $brand . ' title="' . $this->l('Recomended') . '"></td>
				</tr>
				<tr>
					<td><label>' . $this->l('Code EAN13') . '</label></td>
					<td><input type="checkbox" name="gtin" ' . $gtin . ' title="' . $this->l('Recomended') . '"></td>
				</tr>
				<tr>
					<td><label>' . $this->l('Featured Products') . '</label></td>
					<td><input type="checkbox" name="featured_product" ' . $featured_product . '></td>
				</tr>
			</table>
			<br>
			<center><input name="generate" type="submit" value="' . $this->l('Generate') . '"></center>
		</fieldset>
		</form>
		';
        return $form;
    }
    
    public function getName()
    {
        $output = $this->name;
        return $output;
    }
    
    public function uninstall()
    {
        Configuration::deleteByName('GS_PRODUCT_TYPE');
        Configuration::deleteByName('GS_SHIPPING');
        Configuration::deleteByName('GS_COUNTRY');
        return parent::uninstall();
    }
    
    public function generateFileList()
    {
        // Get all shop languages
        $languages = Language::getLanguages();
        foreach ($languages as $i => $lang) {
            $this->generateFile($lang);
        }
    }
    
private function rip_tags($string) { 
    
    // ----- remove HTML TAGs ----- 
    $string = preg_replace ('/<[^>]*>/', ' ', $string); 
    
    // ----- remove control characters ----- 
    $string = str_replace("\r", '', $string);    // --- replace with empty space
    $string = str_replace("\n", ' ', $string);   // --- replace with space
    $string = str_replace("\t", ' ', $string);   // --- replace with space
    
    // ----- remove multiple spaces ----- 
    $string = trim(preg_replace('/ {2,}/', ' ', $string));
    
    return $string; 

}
    
    private function generateFile($lang)
    {
        $path_parts = pathinfo(__FILE__);
        
        if (Configuration::get('GENERATE_FILE_IN_ROOT')):
            $generate_file_path = '../googleshopping-' . $lang['iso_code'] . '.xml';
        else:
            $generate_file_path = $path_parts["dirname"] . '/file_exports/googleshopping-' . $lang['iso_code'] . '.xml';
        endif;
        
        //Google Shopping XML
        $xml = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";
        $xml .= '<feed xmlns="http://www.w3.org/2005/Atom" xmlns:g="http://base.google.com/ns/1.0" encoding="UTF-8" >' . "\n";
        $xml .= '<title>' . Configuration::get('PS_SHOP_NAME') . '</title>' . "\n";
        $xml .= '<link href="'.$this->uri.'" rel="alternate" type="text/html"/>' . "\n";
        $xml .= '<modified>' . date('Y-m-d') . 'T01:01:01Z</modified><author><name>' . Configuration::get('PS_SHOP_NAME') . '</name></author>' . "\n";
        
        $googleshoppingfile = fopen($generate_file_path, 'w');
        
        fwrite($googleshoppingfile, $xml);
        
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'product p' . ' LEFT JOIN ' . _DB_PREFIX_ . 'product_lang pl ON p.id_product = pl.id_product' . ' WHERE p.active = 1 AND pl.id_lang=' . $lang['id_lang'];
        
        $products = Db::getInstance()->ExecuteS($sql);
                
        $title_limit       = 70;
        $description_limit = 10000;
        
        $languages     = Language::getLanguages();
        $tailleTabLang = sizeof($languages);
        
        foreach ($products as $product) {
            $xml_googleshopping = '';
            $cat_link_rew       = Category::getLinkRewrite($product['id_category_default'], intval($lang));
            
            //continue if product not have price
            $price = Product::getPriceStatic($product['id_product'], true, NULL, 2);
            if (empty($price)) {
                continue;
            }
            $product_link = $this->context->link->getProductLink((int) ($product['id_product']), $product['link_rewrite'], $cat_link_rew, $product['ean13'], (int) ($product['id_lang']), 1, 0, true);
            
            $title_crop = $product['name'];
            if (strlen($product['name']) > $title_limit) {
                $title_crop = substr($title_crop, 0, ($title_limit - 1));
                $title_crop = substr($title_crop, 0, strrpos($title_crop, " "));
            }
            
            if (intval(Configuration::get('DESCRIPTION')) === intval(2)) {
                $description_crop = $product['description'];
            } else {
                $description_crop = $product['description_short'];
            }
            $description_crop =$this->rip_tags($description_crop);
            
            if (strlen($description_crop) > $description_limit) {
                $description_crop = substr($description_crop, 0, ($description_limit - 1));
                $description_crop = substr($description_crop, 0, strrpos($description_crop, " "));
            }
            $xml_googleshopping .= '<entry>' . "\n";
            $xml_googleshopping .= '<g:id>' . $product['id_product'] . '-' . $lang['iso_code'] . '</g:id>' . "\n";
            $xml_googleshopping .= '<title>' . htmlspecialchars(ucfirst(mb_strtolower($title_crop, 'UTF-8'))) . '</title>' . "\n";
            $xml_googleshopping .= '<link>' . $product_link . '</link>' . "\n";
            $xml_googleshopping .= '<g:price>' . $price . '</g:price>' . "\n";
            $xml_googleshopping .= '<g:description>' . htmlspecialchars($description_crop, null, 'UTF-8', false) . '</g:description>' . "\n";
            $xml_googleshopping .= '<g:condition>new</g:condition>' . "\n"; // condition = new, used, refurbished
            
            if (Configuration::get('MPN') && $product['supplier_reference'] != '') {
                $xml_googleshopping .= '<g:mpn>' . $product['supplier_reference'] . '</g:mpn>';
            }
            
            // Pour chaque image
            $images       = Image::getImages($lang['id_lang'], $product['id_product']);
            $indexTabLang = 0;
            
            if ($tailleTabLang > 1) {
                while (sizeof($images) < 1 && $indexTabLang < $tailleTabLang) {
                    if ($languages[$indexTabLang]['id_lang'] != $lang['id_lang']) {
                        $images = Image::getImages($languages[$indexTabLang]['id_lang'], $product['id_product']);
                    }
                    $indexTabLang++;
                }
            }
            
            $nbimages   = 0;
            $image_type = Configuration::get('GS_IMAGE');
            if ($image_type == '')
                $image_type = 'large_default';
            
            /* create image links */
            foreach ($images as $im) {
                $image = $this->context->link->getImageLink($product['link_rewrite'], $product['id_product'] . '-' . $im['id_image'], $image_type);
                $xml_googleshopping .= '<g:image_link>' . $image . '</g:image_link>' . "\n";
                //max images by product
                if (++$nbimages == 10)
                    break;
            }
            
            if (Configuration::get('QUANTITY') == 1) {
            	$quantity = StockAvailable::getQuantityAvailableByProduct($product['id_product'], 0);
                if ($quantity>0)
                {
               		$xml_googleshopping .= '<g:quantity>'.$quantity.'</g:quantity>'."\n";
                	$xml_googleshopping .= '<g:availability>in stock</g:availability>'."\n";
                }
                else{
                	$xml_googleshopping .= '<g:quantity>0</g:quantity>'."\n";
                	$xml_googleshopping .= '<g:availability>out of stock</g:availability>'."\n";
                }
            }
            
            // Brand
            if (Configuration::get('BRAND') && $product['id_manufacturer'] != '0') {
                $xml_googleshopping .= '<g:brand>' . htmlspecialchars(Manufacturer::getNameById(intval($product['id_manufacturer'])), null, 'UTF-8', false) . '</g:brand>' . "\n";
            }
            
            // Category google
            if (Configuration::get('GS_PRODUCT_TYPE_' . $lang['iso_code'])) {
                $product_type = str_replace('>', '&gt;', Configuration::get('GS_PRODUCT_TYPE_' . $lang['iso_code']));
                $product_type = str_replace('&', '&amp;', $product_type);
                $xml_googleshopping .= '<g:google_product_category>' . $product_type . '</g:google_product_category>' . "\n";
            }

            // Category shop
            if (Configuration::get('CATEGORY_SHOP')){
                $categories = $this->getBreadcrumbCategory($product['id_category_default'], $product['id_lang']);
                $categories = str_replace('>', '&gt;', $categories);
                $categories = str_replace('&', '&amp;', $categories);
                $xml_googleshopping .= '<g:product_type>' . $categories . '</g:product_type>' . "\n";
            }
            
            //Shipping
            $xml_googleshopping .= '<g:shipping>' . "\n";
            $xml_googleshopping .= '<g:country>' . Configuration::get('GS_COUNTRY') . '</g:country>' . "\n";
            $xml_googleshopping .= '<g:service>Standard</g:service>' . "\n";
            $xml_googleshopping .= '<g:price>' . Configuration::get('GS_SHIPPING') . '</g:price>' . "\n";
            $xml_googleshopping .= '</g:shipping>' . "\n";
            
            
            //weight
            if ($product['weight'] != '0') {
                $xml_googleshopping .= '<g:shipping_weight>' . $product['weight'] . ' kilograms</g:shipping_weight>' . "\n";
            }
            
            //featured product
            if (Configuration::get('FEATURED_PRODUCT') == 1 && $product['on_sale'] != '0') {
                $xml_googleshopping .= '<g:featured_product>true</g:featured_product>' . "\n";
            }
            
            
            if (Configuration::get('GTIN') && $product['ean13'] != '') {
                $xml_googleshopping .= '<g:gtin>' . $product['ean13'] . '</g:gtin>' . "\n";
                
            }
            $xml_googleshopping .= '</entry>' . "\n";
            
            // Ecriture du produit dans l'XML googleshopping
            fwrite($googleshoppingfile, $xml_googleshopping);
        }
        
        $xml = '</feed>';
        fwrite($googleshoppingfile, $xml);
        fclose($googleshoppingfile);
        
        @chmod($generate_file_path, 0777);
        return true;
    }

    private function getBreadcrumbCategory($id_category, $id_lang = null)
    	{
    		$context = Context::getContext()->cloneContext();
    		$context->shop = clone($context->shop);

    		if (is_null($id_lang))
    			$id_lang = $context->language->id;

    		$categories = '';
    		$id_current = $id_category;
    		if (count(Category::getCategoriesWithoutParent()) > 1 && Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') && count(Shop::getShops(true, null, true)) != 1)
    			$context->shop->id_category = Category::getTopCategory()->id;
    		elseif (!$context->shop->id)
    			$context->shop = new Shop(Configuration::get('PS_SHOP_DEFAULT'));
    		$id_shop = $context->shop->id;
    		while (true)
    		{
    			$sql = '
    			SELECT c.*, cl.*
    			FROM `'._DB_PREFIX_.'category` c
    			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
    				ON (c.`id_category` = cl.`id_category`
    				AND `id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').')';
    			if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP)
    				$sql .= '
    			LEFT JOIN `'._DB_PREFIX_.'category_shop` cs
    				ON (c.`id_category` = cs.`id_category` AND cs.`id_shop` = '.(int)$id_shop.')';
    			$sql .= '
    			WHERE c.`id_category` = '.(int)$id_current;
    			if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP)
    				$sql .= '
    				AND cs.`id_shop` = '.(int)$context->shop->id;
    			$root_category = Category::getRootCategory();
    			if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP &&
    				(!Tools::isSubmit('id_category') ||
    					(int)Tools::getValue('id_category') == (int)$root_category->id ||
    					(int)$root_category->id == (int)$context->shop->id_category))
    				$sql .= '
    					AND c.`id_parent` != 0';

    			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

                if (!$result || ($result[0]['id_category'] == $context->shop->id_category))
             		return $categories;

    			if (isset($result[0]))
    				$categories = $result[0]['name'].' > '.$categories;

    			$id_current = $result[0]['id_parent'];
    		}
    	}
}
?>
