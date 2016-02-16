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
		$ret = Db::getInstance()->executeS('SELECT g.*, gl.gcategory, s.name as shop_name, cl.name as cat_name '
			 . 'FROM '._DB_PREFIX_.'gshoppingflux g '
			 . 'LEFT JOIN '._DB_PREFIX_.'category c ON (c.id_category=g.id_gcategory AND c.id_shop_default=g.id_shop) '
			 . 'LEFT JOIN '._DB_PREFIX_.'category_shop cs ON (cs.id_category=g.id_gcategory AND cs.id_shop=g.id_shop) '
			 . 'LEFT JOIN '._DB_PREFIX_.'gshoppingflux_lang gl ON (gl.id_gcategory=g.id_gcategory AND gl.id_lang='.(int)$id_lang.' AND gl.id_shop=g.id_shop) '
			 . 'LEFT JOIN '._DB_PREFIX_.'shop s ON (s.id_shop=g.id_shop) '
			 . 'LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category=g.id_gcategory AND cl.id_lang='.(int)$id_lang.' AND cl.id_shop=g.id_shop) '
			 . 'WHERE '.((!is_null($id_gcategory)) ? ' g.id_gcategory="'.(int)$id_gcategory.'" AND ' : '')
			 . 'g.id_shop IN (0, '.(int)$id_shop.');');
			 
		$shop = new Shop($id_shop);
		$root = Category::getRootCategory($id_lang, $shop);
		
		foreach($ret as $k => $v){
			$ret[$k]['breadcrumb'] = self::getPath($v['id_gcategory'], '', (int)$id_lang, (int)$id_shop, (int)$root->id_category);
			if(empty($ret[$k]['breadcrumb']))$ret[$k]['breadcrumb']=$v['cat_name'];
		}

		return $ret;
	}

	public static function get($id_gcategory, $id_lang, $id_shop)
	{
		return self::gets($id_lang, $id_gcategory, $id_shop);
	}

	public static function getCategLang($id_gcategory, $id_shop, $id_lang)
	{
		$ret = Db::getInstance()->executeS('
			SELECT g.*, gl.gcategory, gl.id_lang, cl.name as gcat_name
			FROM '._DB_PREFIX_.'gshoppingflux g
			LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category = g.id_gcategory AND cl.id_shop='.(int)$id_shop.')
			LEFT JOIN '._DB_PREFIX_.'gshoppingflux_lang gl ON (gl.id_gcategory = g.id_gcategory AND gl.id_shop='.(int)$id_shop.')
			WHERE 1	'.((!is_null($id_gcategory)) ? ' AND g.id_gcategory = "'.(int)$id_gcategory.'"' : '').'
			AND g.id_shop IN (0, '.(int)$id_shop.');'
		);

		$gcateg = array();

		foreach ($ret as $l => $line)
		{
			$gcateg[$line['id_lang']] = Tools::safeOutput($line['gcategory']);
		}
		
		$shop = new Shop($id_shop);
		$root = Category::getRootCategory($id_lang, $shop);
		$ret[0]['breadcrumb'] = self::getPath((int)$id_gcategory, '', $id_lang, $id_shop, $root->id_category);
		if (empty($ret[0]['breadcrumb']) || $ret[0]['breadcrumb'] == ' > ')
			$ret[0]['breadcrumb'] = $ret[0]['gcat_name'];

		return array(
			'breadcrumb' => $ret[0]['breadcrumb'],
			'gcategory' => $gcateg,
			'export' => $ret[0]['export'],
			'condition' => $ret[0]['condition'],
			'availability' => $ret[0]['availability'],
			'gender' => $ret[0]['gender'],
			'age_group' => $ret[0]['age_group'],
			'color' => $ret[0]['color'],
			'material' => $ret[0]['material'],
			'pattern' => $ret[0]['pattern'],
			'size' => $ret[0]['size']
		);
	}

	public static function add($id_category, $gcateg, $export, $condition, $availability, $gender, $age_group, $color, $material, $pattern, $size, $id_shop)
	{
		if(empty($id_category))
			return false;
		if(!is_array($gcateg))
			return false;

		Db::getInstance()->insert('gshoppingflux', array(
			'id_gcategory'=>(int)$id_category,
			'export' => (int)$export,
			'condition' => $condition,
			'availability' => $availability,
			'gender' => $gender,
			'age_group' => $age_group,
			'color' => $color,
			'material' => $material,
			'pattern' => $pattern,
			'size' => $size,
			'id_shop' => (int)$id_shop
			)
		);

		foreach ($gcateg as $id_lang=>$categ)
		Db::getInstance()->insert('gshoppingflux_lang', array(
			'id_gcategory' => (int)$id_category,
			'id_lang' => (int)$id_lang,
			'id_shop' => (int)$id_shop,
			'gcategory' => pSQL($categ)
			)
		);
	}

	public static function update($id_category, $gcateg, $export, $condition, $availability, $gender, $age_group, $color, $material, $pattern, $size, $id_shop)
	{
		if (empty($id_category))
			return false;
		if (!is_array($gcateg))
			return false;

		Db::getInstance()->update('gshoppingflux', array(
				'export' => (int)$export,
				'condition' => $condition,
				'availability' => $availability,
				'gender' => $gender,
				'age_group' => $age_group,
				'color' => $color,
				'material' => $material,
				'pattern' => $pattern,
				'size' => $size,
			),
			'id_gcategory = '.(int)$id_category.' AND id_shop='.(int)$id_shop
		);

		foreach ($gcateg as $id_lang => $categ)
			Db::getInstance()->update('gshoppingflux_lang', array(
				'gcategory'=>pSQL($categ),
				),
				'id_gcategory = '.(int)$id_category.' AND id_lang = '.(int)$id_lang.' AND id_shop='.(int)$id_shop
			);
	}

	public static function updateStatus($id_category, $id_shop, $export)
	{
		Db::getInstance()->update('gshoppingflux', array(
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

	public static function getPath($id_category, $path = '', $id_lang, $id_shop, $id_root)
	{
		$category = new Category((int)$id_category, (int)$id_lang, (int)$id_shop);

		if (!Validate::isLoadedObject($category) || $category->id_category == $id_root  || $category->active == 0 )
			return ($path);

		$pipe = ' > ';

		$category_name =  preg_replace('/^[0-9]+\./', '', $category->name);

		if ($path != $category_name)
			$path = $category_name.($path!='' ? $pipe.$path : '');

		return self::getPath((int)$category->id_parent, $path, (int)$id_lang, (int)$id_shop, (int)$id_root);
	}
}
