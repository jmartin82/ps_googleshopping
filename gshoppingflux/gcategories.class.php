<?php 
// <!--
// Licensed to the Apache Software Foundation (ASF) under one
// or more contributor license agreements.  See the NOTICE file
// distributed with this work for additional information
// regarding copyright ownership.  The ASF licenses this file
// to you under the Apache License, Version 2.0 (the
// "License"); you may not use this file except in compliance
// with the License.  You may obtain a copy of the License at

//   http://www.apache.org/licenses/LICENSE-2.0

// Unless required by applicable law or agreed to in writing,
// software distributed under the License is distributed on an
// "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
// KIND, either express or implied.  See the License for the
// specific language governing permissions and limitations
// under the License.
// //-->

class GCategories
{
	
	public static function gets($id_lang, $id_gcategory = null, $id_shop)
	{
		
		$sql = 'SELECT g.*, gl.gcategory, s.name as shop_name, cl.name as cat_name '
			 . 'FROM '._DB_PREFIX_.'gshoppingflux g '
			 . 'LEFT JOIN '._DB_PREFIX_.'category c ON (c.id_category=g.id_gcategory AND c.id_shop_default=g.id_shop) '
			 . 'LEFT JOIN '._DB_PREFIX_.'category_shop cs ON (cs.id_category=g.id_gcategory AND cs.id_shop=g.id_shop) '
			 . 'LEFT JOIN '._DB_PREFIX_.'gshoppingflux_lang gl ON (gl.id_gcategory=g.id_gcategory AND gl.id_lang='.(int)$id_lang.' AND gl.id_shop=g.id_shop) '
			 . 'LEFT JOIN '._DB_PREFIX_.'shop s ON (s.id_shop=g.id_shop) '
			 . 'LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category=g.id_gcategory AND cl.id_lang='.(int)$id_lang.' AND cl.id_shop=g.id_shop) '
			 . 'WHERE '.((!is_null($id_gcategory)) ? ' g.id_gcategory="'.(int)$id_gcategory.'" AND ' : '')
			 . 'g.id_shop IN (0, '.(int)$id_shop.');';
			 
		$ret = Db::getInstance()->executeS($sql);
		foreach($ret as $k => $v){
			$ret[$k]['breadcrumb'] = self::getBreadcrumbCategory($v['id_gcategory'], (int)$id_lang);
			if(empty($ret[$k]['breadcrumb']))$ret[$k]['breadcrumb']=$v['cat_name'];
		}
		
		return $ret;
	}

	public static function get($id_gcategory, $id_lang, $id_shop)
	{
		return self::gets($id_lang, $id_gcategory, $id_shop);
	}

	public static function getCategLang($id_gcategory, $id_shop)
	{
		$ret = Db::getInstance()->executeS('
			SELECT g.*, gl.gcategory, gl.id_lang, cl.name as gcat_name
			FROM '._DB_PREFIX_.'gshoppingflux g
			LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category = g.id_gcategory AND cl.id_shop='.(int)$id_shop.')
			LEFT JOIN '._DB_PREFIX_.'gshoppingflux_lang gl ON (gl.id_gcategory = g.id_gcategory AND gl.id_shop='.(int)$id_shop.')
			WHERE 1	'.((!is_null($id_gcategory)) ? ' AND g.id_gcategory = "'.(int)$id_gcategory.'"' : '').'
			AND g.id_shop IN (0, '.(int)$id_shop.')
		');
		
		$gcateg = array();

		foreach ($ret as $l => $line)
		{
			$gcateg[$line['id_lang']] = Tools::safeOutput($line['gcategory']);
		}
		
		$ret[0]['breadcrumb'] = self::getBreadcrumbCategory((int)$id_gcategory);
		if(empty($ret[0]['breadcrumb']) || $ret[0]['breadcrumb']==' > ')$ret[0]['breadcrumb']=$ret[0]['gcat_name'];

		return array('breadcrumb' => $ret[0]['breadcrumb'], 'gcategory' => $gcateg, 'export' => $ret[0]['export'], 'condition' => $ret[0]['condition'], 'availability' => $ret[0]['availability'], 'gender' => $ret[0]['gender'], 'age_group' => $ret[0]['age_group']);
		
	}

	public static function add($id_category, $gcateg, $export, $condition, $availability, $gender, $age_group, $id_shop)
	{
		if(empty($id_category))
			return false;
		if(!is_array($gcateg))
			return false;
		
		Db::getInstance()->insert(
			'gshoppingflux',
			array(
				'id_gcategory'=>(int)$id_category,
				'export' => (int)$export,
				'condition' => $condition,
				'availability' => $availability,
				'gender' => $gender,
				'age_group' => $age_group,
				'id_shop' => (int)$id_shop
			)
		);

		foreach ($gcateg as $id_lang=>$categ)
		Db::getInstance()->insert(
			'gshoppingflux_lang',
			array(
				'id_gcategory'=>(int)$id_category,
				'id_lang'=>(int)$id_lang,
				'id_shop'=>(int)$id_shop,
				'gcategory'=>pSQL($categ)
			)
		);
	}

	public static function update($id_category, $gcateg, $export, $condition, $availability, $gender, $age_group, $id_shop)
	{
		if(empty($id_category))
			return false;
		if(!is_array($gcateg))
			return false;		
		
		Db::getInstance()->update(
			'gshoppingflux',
			array(
				'export' => (int)$export,
				'condition' => $condition,
				'availability' => $availability,
				'gender' => $gender,
				'age_group' => $age_group,
			),
			'id_gcategory = '.(int)$id_category.' AND id_shop='.(int)$id_shop
		);

		foreach ($gcateg as $id_lang => $categ)
			Db::getInstance()->update(
				'gshoppingflux_lang',
				array(
					'gcategory'=>pSQL($categ),
				),
				'id_gcategory = '.(int)$id_category.' AND id_lang = '.(int)$id_lang.' AND id_shop='.(int)$id_shop
			);
	}

	public static function updateStatus($id_category, $id_shop, $export)
	{		
		Db::getInstance()->update(
			'gshoppingflux',
			array(
				'export' => (int)$export,
			),
			'id_gcategory = '.(int)$id_category.' AND id_shop='.(int)$id_shop
		);
	}

	public static function remove($id_gcategory, $id_shop)
	{
		Db::getInstance()->delete('gshoppingflux', 'id_gcategory = '.(int)$id_gcategory.' AND id_shop = '.(int)$id_shop);
		Db::getInstance()->delete('gshoppingflux_lang', 'id_gcategory = '.(int)$id_gcategory);
	}

	public static function getRoot($id_shop)
	{
		$sql = 'SELECT s.* FROM '._DB_PREFIX_.'shop s '
			 . 'WHERE s.id_shop = '.(int)$id_shop.' '
			 . 'ORDER BY s.id_shop ASC LIMIT 1;';		
		$ret = Db::getInstance()->executeS($sql);		
		return ($ret[0]['id_category']);
	}
	
	public static function getBreadcrumbCategory($id_category, $id_lang = null, $id_shop = null)
    {
        $context       = Context::getContext()->cloneContext();
		$context->shop = clone ($context->shop);

        if (is_null($id_lang))
            $id_lang = $context->language->id;
			
		if (is_null($id_shop))
			$id_shop = $context->shop->id;
			
        $categories = '';
        $id_current = $id_category;
		$id_root = GCategories::getRoot($id_shop);
		
        while (true) {
            $sql = 'SELECT c.id_parent, cl.* '
     			 . 'FROM `' . _DB_PREFIX_ . 'category` c '
    			 . 'LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl '
     			 . 'ON (cl.`id_category` = c.`id_category` '
        		 . 'AND `id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('cl') . ') '
                 . 'LEFT JOIN `' . _DB_PREFIX_ . 'gshoppingflux` gc ON (gc.`id_gcategory` = c.`id_category`) '
            	 . 'WHERE gc.`id_gcategory` = ' . (int) $id_current.' AND c.id_parent != 0';
            if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP)
            	$sql .= ' AND gc.`id_shop` = ' . (int)$id_shop;
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            
            if (isset($result[0]))
                $categories = $result[0]['name'] . ' > ' . $categories;
				
			$id_current = $result[0]['id_parent'];
			
			if (!$result || ($result[0]['id_category'] == $id_root)){
				$categories = substr($categories, 0, -3);
                return $categories;
			}
        }
		
    }

}
