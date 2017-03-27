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
$estilo = '';
$doc = &JFactory::getDocument();
if($mostrarTaboa) {
	$estilo .= <<<REMATE
#courseManagerModuList$idModulo table{margin:$alinhaTaboa;border:0;border-collapse:separate !important;border-spacing:1px !important;background-color:$corTaboaFondo;$amploTaboa}
#courseManagerModuList$idModulo table tr td.titulo_curso{color:$corCabecalhoTexto;background-color:$corCabecalhoFondo;padding-left:14px;text-align:left;$amploCelaCurso}
#courseManagerModuList$idModulo table tr td.titulo_categoria{color:$corCabecalhoTexto;background-color:$corCabecalhoFondo;text-align:center;$amploCelaCategoria}
#courseManagerModuList$idModulo table tr td.titulo_icone{color:$corCabecalhoTexto;background-color:$corCabecalhoFondo;text-align:center;$amploCelaIcone}
#courseManagerModuList$idModulo table tr td.cela_curso{background-color:$corCelasFondo;padding:2px 2px 2px 8px;text-align:left;}
#courseManagerModuList$idModulo table tr td.cela_curso div{display: inline-block;white-space: nowrap;overflow: hidden;height:18px;text-align:left;$amploCelaCurso}
#courseManagerModuList$idModulo table tr td.cela_categoria{background-color:$corCelasFondo;padding:2px 2px 2px 2px;text-align:center;}
#courseManagerModuList$idModulo table tr td.cela_categoria div{display: inline-block;white-space: nowrap;overflow: hidden;height:18px;text-align:center;$amploCelaCategoria}
#courseManagerModuList$idModulo table tr td.cela_icone{background-color:$corCelasFondo;padding:2px 2px 2px 2px;text-align:center;}
#courseManagerModuList$idModulo table tr td.cela_icone div{display: inline-block;height:18px;white-space: nowrap;overflow: hidden;text-align:center;$amploCelaIcone}
#courseManagerModuList$idModulo table a,a:visited{color:$corCelasTexto;text-decoration:none;}
#courseManagerModuList$idModulo table a:hover{color:$corCelasTextoPairado;text-decoration:none;}
REMATE;
}
$estilo .= <<<REMATE
#courseManagerModuListSlider$idModulo {float: left; background: transparent url(${urlBase}modules/mod_coursemanager_modulist/imagens/linha.png) repeat-x right top;height: 16px;width: $amploRolagem;margin: 0 10px 0 5px}
#courseManagerModuListSlider$idModulo .knob {background: transparent url(${urlBase}modules/mod_coursemanager_modulist/imagens/carraboujo.png) no-repeat right top;width: 16px;height: 16px;}
#courseManagerModuListNavegacom$idModulo {display: block;}
#courseManagerModuListPaginacom$idModulo {margin: 5px;}
REMATE;
$doc->addStyleDeclaration($estilo);
if($ajax) {
	$idioma         = &JFactory::getLanguage();
	$etiquetaIdioma = $idioma->getTag();
	$tradPagina     = JText::_('MOD_CML_PAGINA');
	$tradDe         = JText::_('MOD_CML_DE');
	$endereco       = $urlBase.'modules'.DS.'mod_coursemanager_modulist'.DS.'paginacom_ajax.php?ajax=true'.'&tipo='.$tipoElementos.'&elementos='.$elementos.'&elementos_pagina='.$elementosPagina.'&ordem='.$ordem.'&catid='.$catid.'&mostrar_categorias='.$mostrarCategoria.'&mostrar_icone='.$mostrarIcone.'&mostrar_taboa='.$mostrarTaboa.'&car_categoria='.$caracteresCategoria.'&car_curso='.$caracteresCurso.'&itemid='.$itemid.'&idioma='.$etiquetaIdioma;
	$script = <<<REMATE
	window.addEvent('domready', function(){
		var courseManagerModuListSlider$idModulo = $("courseManagerModuListSlider$idModulo");
		new Slider(courseManagerModuListSlider$idModulo, courseManagerModuListSlider$idModulo.getElement('.knob'), {
			steps: $totalPaginas,
			snap: true,
			wheel: true,
			initialStep: 1,
			offset: 2,
			range: [1, $totalPaginas],
			onChange: function(valor){
				if (valor) $('courseManagerModuListPaginacom$idModulo').setHTML('$tradPagina '+valor+' $tradDe $totalPaginas');
			},
			onComplete: function(valor){
				if (valor) url = "$endereco"+"&pagina="+valor;
			pedidoAjax();
			}
		});
REMATE;
	if($fundido) {
		$script .= <<<REMATE
			function pedidoAjax() {
				$('courseManagerModuList$idModulo').effect('opacity',{duration:$tempoFundido, fps:50, onComplete: atualizaContedor}).start(1,0);
			};

			function atualizaContedor() {
				new Ajax(url, {update: $('courseManagerModuList$idModulo'), onComplete: fundidoContedor}).request();
			}

			function fundidoContedor() {
				$('courseManagerModuList$idModulo').effect('opacity',{duration:$tempoFundido, fps:50}).start(0,1);
			}
REMATE;
	}
	else {
		$script .= <<<REMATE
			var desliza$idModulo = new Fx.Slide('courseManagerModuList$idModulo', {mode: '$modoSlide', duration: $tempoFundido, transition: Fx.Transitions.$tipoSlide});
			function pedidoAjax() {
				desliza$idModulo.slideOut().chain(function(){atualizaContedor();});
			};

			function atualizaContedor() {
				new Ajax(url, {update: $('courseManagerModuList$idModulo'), onComplete: deslizaContedor}).request();
			}

			function deslizaContedor() {
				desliza$idModulo.slideIn();
			}
REMATE;
	}
	$script .= <<<REMATE
	});
REMATE;
	$doc->addScriptDeclaration($script);
}
?>
<?php if($ajax) :?>
<div id="courseManagerModuListNavegacom<?php echo $idModulo;?>">
<div id="courseManagerModuListSlider<?php echo $idModulo;?>" class="courseManagerModuListSlider<?php echo $idModulo;?>">
  <div class="knob"></div>
</div>
<div id="courseManagerModuListPaginacom<?php echo $idModulo;?>"><?php echo $tradPagina.' 1 '.$tradDe.' '.$totalPaginas;?></div>
</div>
<?php endif;?>
<?php if($descricom) 
	echo $descricom;?>
<div id="courseManagerModuList<?php echo $idModulo;?>">
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
<?php foreach($listagem as $item) :?>
	<tr>
		<td class="cela_curso"><div><a href="<?php echo $item->link;?>" title="<?php echo $item->text;?>"><?php echo((strlen($item->text) >= $caracteresCurso) && $caracteresCurso ? mb_substr($item->text, 0, $caracteresCurso, 'UTF-8').'...' : $item->text);?></a></div></td>
		<?php if($mostrarCategoria) :?>
		<td class="cela_categoria"><div><a href="<?php echo $item->link_category;?>" title="<?php echo $item->category;?>"><?php echo((strlen($item->category) >= $caracteresCategoria) && $caracteresCategoria ? mb_substr($item->category, 0, $caracteresCategoria, 'UTF-8').'...' : $item->category);?></a></div></td>
		<?php endif;?>
		<?php if($mostrarIcone) :?>
		<td class="cela_icone"><div><a href="<?php echo $item->link;?>"><img src="<?php echo $urlBase.'modules'.DS.'mod_coursemanager_modulist'.DS?>imagens/info.png" width="15" height="15" alt="Info" title="<?php echo JText::_('MOD_CML_VERMAIS');?>"></a></div></td>
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
<?php foreach($listagem as $item) :?>
		<li><a href="<?php echo $item->link;?>" title="<?php echo $item->text;?>"><?php echo((strlen($item->text) >= $caracteresCurso) && $caracteresCurso ? mb_substr($item->text, 0, $caracteresCurso, 'UTF-8').'...' : $item->text);?></a>
		<?php if($mostrarCategoria) :?>
		(<a href="<?php echo $item->link_category;?>" title="<?php echo $item->category;?>"><?php echo((strlen($item->category) >= $caracteresCategoria) && $caracteresCategoria ? mb_substr($item->category, 0, $caracteresCategoria, 'UTF-8').'...' : $item->category);?></a>)
		<?php endif;?>
		</li>
<?php endforeach;?>
</ul>
<?php endif;?>
</div>
