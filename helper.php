<?php
/*
 *      Course Manager List module
 *      @package Course Manager List module
 *      @author José António Cidre Bardelás
 *      @copyright Copyright (C) 2011 José António Cidre Bardelás and Joomla Empresa. All rights reserved
 *      @license GNU/GPL v3 or later
 *      
 *      Contact us at info@joomlaempresa.com (http://www.joomlaempresa.es)
 *      
 *      This file is part of Course Manager List module.
 *      
 *          Course Manager List module is free software: you can redistribute it and/or modify
 *          it under the terms of the GNU General Public License as published by
 *          the Free Software Foundation, either version 3 of the License, or
 *          (at your option) any later version.
 *      
 *          Course Manager List module is distributed in the hope that it will be useful,
 *          but WITHOUT ANY WARRANTY; without even the implied warranty of
 *          MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *          GNU General Public License for more details.
 *      
 *          You should have received a copy of the GNU General Public License
 *          along with Course Manager List module.  If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') or die('Acesso restrito');
if(file_exists(JPATH_SITE.DS.'components'.DS.'com_courseman'.DS.'helpers'.DS.'route.php')) 
	require_once(JPATH_SITE.DS.'components'.DS.'com_courseman'.DS.'helpers'.DS.'route.php');

class modCourseManagerModuList {

	function getList(&$params) {
		$db = &JFactory::getDBO();
		$elementos = (int) $params->get('elementos', 5);
		$elementos_pagina = (int) $params->get('elementos_pagina', 5);
		$catid = trim($params->get('catid'));
		$itemid = (int) $params->get('itemid', 1);
		if($catid) {
			$ids = explode(',', $catid);
			JArrayHelper::toInteger($ids);
			$catCondition = ' AND (cr.catid='.implode(' OR cr.catid=', $ids).')';
		}
		$user = &JFactory::getUser();
		$usuarioConvidado = $user->guest;
		$nullDate = $db->getNullDate();
		$date = &JFactory::getDate();
		$now = $date->toMySQL();
		// Ordering
		if($params->get('tipo_elementos') == 'recentes') {
			switch($params->get('ordering')) {
				case 'm_dsc':
				$ordering = 'a.modified DESC, a.created DESC';
				break;
				case 'c_dsc':
				default:
				$ordering = 'a.created DESC';
				break;
			}
		}
		elseif($params->get('tipo_elementos') == 'populares') {
			$ordering = 'a.hits DESC, a.created DESC';
		}
		else 
			$ordering = 'a.created DESC';
		$query = 'SELECT *'.' FROM #__courseman_categories'.' WHERE published = 1'.($usuarioConvidado ? ' AND access = 0' : '').' ORDER BY id';
		$db->setQuery($query);
		$rows_categorias = $db->loadObjectList();
		$categorias = array();
		$titulos_categorias = array();
		foreach($rows_categorias as $row) {
			$categorias[$row->id] = $row->alias;
			$titulos_categorias[$row->id] = $row->title;
		}
		$query = 'SELECT a.*, cr.*'.' FROM #__courseman_courses AS a'.' INNER JOIN #__courseman_cats_course_relations AS cr ON cr.courseid = a.id'.' INNER JOIN #__courseman_categories AS c ON c.id = cr.catid'.' WHERE a.state = 1'.' AND c.published = 1 '.($usuarioConvidado ? ' AND c.access = 0' : '').($catid ? $catCondition : '').' AND ( a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).' )'.' AND ( a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).' )'.' ORDER BY '.$ordering;
		$db->setQuery($query);
		$db->query();
		$totalResultados = $db->getNumRows();
		if($params->get('tipo_elementos') == 'aleatorios') 
			$db->setQuery($query);
		else 
			$db->setQuery($query, 0, $elementos_pagina);
		$rows = $db->loadObjectList();
		$i = 0;
		$lists = array();
		foreach($rows as $row) {
			$lists[$i]->link = JRoute::_('index.php?option=com_courseman'.'&cid='.$row->catid.'&view=courses&id='.$row->id.'&Itemid='.$itemid);
			$lists[$i]->text = htmlspecialchars($row->title);
			$lists[$i]->link_category = JRoute::_('index.php?option=com_courseman'.'&view=courses&cid='.$row->catid.'&Itemid='.$itemid);
			$lists[$i]->category = htmlspecialchars($titulos_categorias[$row->catid]);
			$i++;
		}
		if($params->get('tipo_elementos') == 'aleatorios') {
			shuffle($lists);
			$lists = array_slice($lists, 0, $elementos);
		}
		return array($lists, $totalResultados);
	}
}
