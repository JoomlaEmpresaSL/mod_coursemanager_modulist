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
define('_JEXEC', 1);
// JPATH_BASE should point to Joomla root directory
// if you app is placed into a subfolder in Joomla root, the path will look like dirname(__FILE__) . '/..'
define('JPATH_BASE', realpath(dirname(__FILE__).'/../..'));
define('DS', DIRECTORY_SEPARATOR);
require_once(JPATH_BASE.DS.'includes'.DS.'defines.php');
require_once(JPATH_BASE.DS.'includes'.DS.'framework.php');
$mainframe = JFactory::getApplication('site');
if(file_exists(JPATH_SITE.DS.'components'.DS.'com_courseman'.DS.'helpers'.DS.'route.php')) 
	require_once(JPATH_SITE.DS.'components'.DS.'com_courseman'.DS.'helpers'.DS.'route.php');
jimport('joomla.environment.request');
$ajax = JRequest::getBool('ajax');
if(!$ajax) 
	die;
$elementos           = JRequest::getInt('elementos');
$elementos_pagina    = JRequest::getInt('elementos_pagina');
$elemento_comeco     = ((JRequest::getInt('pagina') ? JRequest::getInt('pagina') : 1) - 1) * $elementos_pagina;
$elementos_pagina    = ($elemento_comeco + $elementos_pagina) <= $elementos ? $elementos_pagina : ($elementos - $elemento_comeco);
$tipoElementos       = JRequest::getString('tipo');
$ordem               = JRequest::getString('ordem');
$catid               = JRequest::getString('catid');
$mostrarTaboa        = JRequest::getInt('mostrar_taboa');
$mostrarCategoria    = JRequest::getInt('mostrar_categorias');
$mostrarIcone        = JRequest::getInt('mostrar_icone');
$caracteresCurso     = JRequest::getInt('car_curso');
$caracteresCategoria = JRequest::getInt('car_categoria');
$itemid              = JRequest::getInt('itemid');
$lang                = &JFactory::getLanguage();
$extension           = 'mod_coursemanager_modulist';
$base_dir            = JPATH_BASE;
$language_tag        = JRequest::getString('idioma');
$lang->load($extension, $base_dir, $language_tag, true);
if($catid) {
	$ids = explode(',', $catid);
	JArrayHelper::toInteger($ids);
	$catCondition = ' AND (cr.catid='.implode(' OR cr.catid=', $ids).')';
}
if($tipoElementos == 'recentes') {
	switch($ordem) {
		case 'm_dsc':
		$ordering = 'a.modified DESC, a.created DESC';
		break;
		case 'c_dsc':
		default:
		$ordering = 'a.created DESC';
		break;
	}
}
elseif($tipoElementos == 'populares') {
	$ordering = 'a.hits DESC, a.created DESC';
}
else 
	$ordering        = 'a.created DESC';
$user             = &JFactory::getUser();
$usuarioConvidado = $user->guest;
$db               = &JFactory::getDBO();
$nullDate         = $db->getNullDate();
$date             = &JFactory::getDate();
$now              = $date->toMySQL();
$query            = 'SELECT *'.' FROM #__courseman_categories'.' WHERE published = 1'.($usuarioConvidado ? ' AND access = 0' : '').' ORDER BY id';
$db->setQuery($query);
$rows_categorias    = $db->loadObjectList();
$categorias         = array();
$titulos_categorias = array();
foreach($rows_categorias as $row) {
	$categorias[$row->id] = $row->alias;
	$titulos_categorias[$row->id] = $row->title;
}
$query = 'SELECT a.*, cr.*'.' FROM #__courseman_courses AS a'.' INNER JOIN #__courseman_cats_course_relations AS cr ON cr.courseid = a.id'.' INNER JOIN #__courseman_categories AS c ON c.id = cr.catid'.' WHERE a.state = 1'.' AND c.published = 1 '.($usuarioConvidado ? ' AND c.access = 0' : '').($catid ? $catCondition : '').' AND ( a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).' )'.' AND ( a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).' )'.' ORDER BY '.$ordering;
$db->setQuery($query, $elemento_comeco, $elementos_pagina);
$rows  = $db->loadObjectList();
$i     = 0;
$lists = array();
foreach($rows as $row) {
	$lists[$i]->link          = str_replace('modules/mod_coursemanager_modulist/', '', JRoute::_('index.php?option=com_courseman'.'&cid='.$row->catid.'&id='.$row->id.'&Itemid='.$itemid));
	$lists[$i]->text          = htmlspecialchars($row->title);
	$lists[$i]->link_category = str_replace('modules/mod_coursemanager_modulist/', '', JRoute::_('index.php?option=com_courseman'.'&cid='.$row->catid.'&Itemid='.$itemid));
	$lists[$i]->category      = htmlspecialchars($titulos_categorias[$row->catid]);
	$i++;
}
?>
<?php if($mostrarTaboa) :?>
<table><tbody>
	<tr>
		<td class="titulo_curso"><b><?php echo JText::_('MOD_CML_CURSO');?></b></td>
		<?php if($mostrarCategoria) :?>
		<td class="titulo_categoria"><b><?php echo JText::_('MOD_CML_CATEGORIA');?></b></td>
		<?php endif;?>
		<?php if($mostrarIcone) :?>
		<td class="titulo_icone"><b><?php echo JText::_('MOD_CML_INFO');?></b></td>
		<?php endif;?>
	</td></tr>
<?php foreach($lists as $item) :?>
	<tr>
		<td class="cela_curso"><div><a href="<?php echo $item->link;?>" title="<?php echo $item->text;?>"><?php echo((strlen($item->text) >= $caracteresCurso) && $caracteresCurso ? mb_substr($item->text, 0, $caracteresCurso, 'UTF-8').'...' : $item->text);?></a></div></td>
		<?php if($mostrarCategoria) :?>
		<td class="cela_categoria"><div><a href="<?php echo $item->link_category;?>" title="<?php echo $item->category;?>"><?php echo((strlen($item->category) >= $caracteresCategoria) && $caracteresCategoria ? mb_substr($item->category, 0, $caracteresCategoria, 'UTF-8').'...' : $item->category);?></a></div></td>
		<?php endif;?>
		<?php if($mostrarIcone) :?>
		<td class="cela_icone"><div><a href="<?php echo $item->link;?>"><img src="<?php echo JURI::base();?>imagens/info.png" width="15" height="15" alt="Info" title="<?php echo JText::_('MOD_CML_VERMAIS');?>"></a></div></td>
		<?php endif;?>
	</td></tr>
<?php endforeach;?>
</table>
<?php else :?>
	<b><?php echo JText::_('MOD_CML_CURSO');?></b>
	<?php if($mostrarCategoria) :?>
	(<b><?php echo JText::_('MOD_CML_CATEGORIA');?></b>)
	<?php endif;?>
	<ul>
<?php foreach($lists as $item) :?>
		<li><a href="<?php echo $item->link;?>" title="<?php echo $item->text;?>"><?php echo((strlen($item->text) >= $caracteresCurso) && $caracteresCurso ? mb_substr($item->text, 0, $caracteresCurso, 'UTF-8').'...' : $item->text);?></a>
		<?php if($mostrarCategoria) :?>
		(<a href="<?php echo $item->link_category;?>" title="<?php echo $item->category;?>"><?php echo((strlen($item->category) >= $caracteresCategoria) && $caracteresCategoria ? mb_substr($item->category, 0, $caracteresCategoria, 'UTF-8').'...' : $item->category);?></a>)
		<?php endif;?>
		</li>
<?php endforeach;?>
</ul>
<?php endif;?>
