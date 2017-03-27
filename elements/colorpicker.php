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

class JElementColorpicker extends JElement {
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var $_name = 'Colorpicker';

	function fetchElement($name, $value, &$node, $control_name) {
		ob_start();
		$img = JUri::root()."modules/mod_coursemanager_modulist/elements/images/rainbow.png";
		$imgx = JUri::root()."modules/mod_coursemanager_modulist/elements/images/";
		static $embedded;
		if(!$embedded) {
			$css2 = JUri::root()."modules/mod_coursemanager_modulist/elements/mooRainbow.css";
			$jspath = JUri::root()."modules/mod_coursemanager_modulist/elements/mooRainbow.js";
			?>
<link href="<?php echo $css2;?>" type="text/css" rel="stylesheet" />
<script src="<?php echo $jspath;?>"></script>
            <?php
            $embedded = true;
			?>
<script>
            window.addEvent('domready',function(){
                Element.extend({
                    getSiblings: function() {
                        return this.getParent().getChildren().remove(this);
                    }
                });
            $$(".marcodavelha").each(function(item){
         item.color=new MooRainbow(item.id, {
                    startColor: [58, 142, 246],
                    wheel: true,
                    id:item.id+'x',
                    deslocamentoX: -60,
                    onChange: function(color) {
					item.setStyle('background-color', color.hex);
                    item.getSiblings()[0].value = color.hex;
                    },
                    onComplete: function(color) {
                    item.getSiblings()[0].value = color.hex;
                    },
                    imgPath: '<?php echo $imgx;?>'
                });
                });
            });
            </script>
            <?php
		}
		?>
            <label>
<input name="<?php echo $control_name;?>[<?php echo $name;?>]" type="text"
    class="inputbox" id="<?php echo $control_name.$name?>"
    value="<?php echo $value;?>" size="10" />
<img src="<?php echo $img;?>" id="img<?php echo $name;?>" alt="[M]"
    class="marcodavelha" width="16" height="16" title="<?php echo JText::_('MOD_CML_ESCOLHECOR')?>" style="background-color: <?php echo $value;?>; border: 1px solid #000;"/></label>
        <?php
        $content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}
