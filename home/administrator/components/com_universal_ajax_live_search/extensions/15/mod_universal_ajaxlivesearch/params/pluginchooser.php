<?php 
/*------------------------------------------------------------------------
# mod_universal_ajaxlivesearch - Universal AJAX Live Search 
# ------------------------------------------------------------------------
# author    Janos Biro 
# copyright Copyright (C) 2011 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
?>
<?php

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.application.component.view');
jimport('joomla.application.component.model');

class JElementPluginChooser extends JElement
{

	var $_name = 'PluginChooser';

	function fetchElement($name, $value, &$node, $control_name)
	{
    if(!is_array($value) && $value!=''){
      $value = array($value);
    }
		// Initialize variables.
		$html = array();
    $db =& JFactory::getDBO();
    
    $db->setQuery('SET @rank=0;');
    $db->query();
    $db->setQuery('UPDATE #__plugins SET ordering = (@rank:=@rank+1) WHERE folder = "search" ORDER BY ordering ASC');
    $db->query();

    $db->setQuery("SELECT id, name FROM #__plugins WHERE folder = 'search' AND published=1 ORDER BY ordering");
    $plgs = $db->loadRowList();
    $new = false;
		if($value[0] == 1) $new=true; // check if it is a new module 
    $val = ($node->attributes('value_field') ? $node->attributes('value_field') : $name);
		$i=0;
    
    foreach ($plgs as $plg)
    {
			$checked = is_array($value) && in_array($plg[0], $value) ? 'checked="checked"' : '';
      if ($new){
        $checked = 'checked="checked"';
      }
		  $html[] = '<input type="checkbox" name="'.$control_name.'['.$name.'][]" value="'.$plg[0].'" id="'.$name.'_'.$plg[0].'" '.$checked.' />';
			$html[] = '<label for="'.$name.'_'.$plg[0].'"  style="clear:none;" >'.JText::_($plg[1]).'</label>';
			$html[] = '<div style="clear:both;"></div>';
      $i++;
		}
		return implode($html);
	}
}

?>