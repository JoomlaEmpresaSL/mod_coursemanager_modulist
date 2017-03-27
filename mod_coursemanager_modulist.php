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
jimport('joomla.application.component.helper');
if(!JComponentHelper::isEnabled('com_courseman', true)) 
	return false;
require_once(dirname(__FILE__).DS.'helper.php');
list($listagem, $totalResultados) = modCourseManagerModuList::getList($params);
$mostrarTaboa = (int) $params->get('mostrar_taboa', 1);
$margemSupTaboa = $params->get('margem_sup_taboa', 5).'px';
$margemInfTaboa = $params->get('margem_inf_taboa', 5).'px';
switch($params->get('alinha_taboa', 'center')) {
	case 'esquerda':
	$alinhaTaboa = $margemSupTaboa.' auto '.$margemInfTaboa.' 0';
	break;
	case 'direita':
	$alinhaTaboa = $margemSupTaboa.' 0 '.$margemInfTaboa.' auto';
	break;
	case 'center':
	default:
	$alinhaTaboa = $margemSupTaboa.' auto '.$margemInfTaboa.' auto';
	break;
}
$corTaboaFondo = $params->get('cor_taboa_fondo', '#FFFFFF');
$corCabecalhoFondo = $params->get('cor_cabecalho_fondo', '#444444');
$corCabecalhoTexto = $params->get('cor_cabecalho_texto', '#FFFFFF');
$corCelasFondo = $params->get('cor_celas_fondo', '#C1D2E6');
$corCelasTexto = $params->get('cor_celas_texto', '#000000');
$corCelasTextoPairado = $params->get('cor_celas_texto_pairado', '#FFFFFF');
$amploRolagem = (int) $params->get('amplo_rolagem', '300').'px';
$tempoFundido = (int) $params->get('tempo_fundido', '500');
$elementos = (int) $params->get('elementos', 5);
$elementosPagina = (int) $params->get('elementos_pagina', 5);
$tipoElementos = $params->get('tipo_elementos', 'recentes');
$ordem = $params->get('ordering', 'c_dsc');
$mostrarCategoria = (int) $params->get('mostrar_categoria', 1);
$mostrarIcone = (int) $params->get('mostrar_icone', 1);
$amploTaboa = (int) $params->get('amplo_taboa') ? 'min-width: '.(int) $params->get('amplo_taboa').'px;' : '';
$amploCelaCurso = (int) $params->get('amplo_cela_curso') ? 'width: '.(int) $params->get('amplo_cela_curso').'px;' : '';
$amploCelaCategoria = (int) $params->get('amplo_cela_categoria') ? 'width: '.(int) $params->get('amplo_cela_categoria').'px;' : '';
$amploCelaIcone = (int) $params->get('amplo_cela_icone') ? 'width: '.(int) $params->get('amplo_cela_icone').'px;' : '';
$caracteresCurso = (int) $params->get('caracteres_curso');
$caracteresCategoria = (int) $params->get('caracteres_categoria');
$fundido = (int) $params->get('efecto_transicom', 0);
$modoSlide = $params->get('modo_slide', 'horizontal');
$tipoSlide = $params->get('tipo_slide', 'Expo.easeOut');
$itemid = (int) $params->get('itemid', '');
$urlBase = JURI::base();
$idModulo = $module->id;
$catid = trim($params->get('catid'));
$descricom = $params->get('texto_descricom') ? strip_tags($params->get('texto_descricom'), '<b><i><p><a>') : '';
$totalResultados = $elementos < $totalResultados ? $elementos : $totalResultados;
$totalPaginas = ceil($totalResultados / $elementosPagina);
$ajax = ((int) $params->get('elementos', 5) > (int) $params->get('elementos_pagina', 5)) && $params->get('tipo_elementos') != 'aleatorios' && ($totalPaginas > 1) ? true : false;
require(JModuleHelper::getLayoutPath('mod_coursemanager_modulist'));
