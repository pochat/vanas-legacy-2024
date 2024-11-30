<?php

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.application.component.view');
jimport('joomla.application.component.model');

class JFormFieldPluginChooser extends JFormField
{

	protected $type = 'PluginChooser';

	function getAreas()
	{
		// Load the Category data
			$areas = array();

			JPluginHelper::importPlugin('search');
			$dispatcher = JDispatcher::getInstance();
			$searchareas = $dispatcher->trigger('onContentSearchAreas');

			foreach ($searchareas as $area) {
				if (is_array($area)) {
					$areas = array_merge($areas, $area);
				}
			}
		return $areas;
	}

	protected function getInput()
	{
		// Initialize variables.
		$html = array();
    $areas = $this->getAreas();

    $db =& JFactory::getDBO();
    
    $db->setQuery('SET @rank=0;');
    $db->query();
    $db->setQuery('UPDATE #__extensions SET ordering = (@rank:=@rank+1) WHERE type = "plugin" AND folder = "search" ORDER BY ordering ASC');
    $db->query();

    $db->setQuery("SELECT extension_id, name FROM #__extensions WHERE type = 'plugin' AND folder = 'search' AND enabled =1 ORDER BY ordering");
    $plgs = $db->loadRowList();
    $lng = JFactory::getLanguage();
    $new = false;
		if($this->value == 1) $new=true; // check if it is a new module 
    
		$html[] = '<fieldset class="only">';
		$i=0;
    
    foreach ($plgs as $plg)
    {
      $lng->load($plg[1]);
			$checked = is_array($this->value['enabled']) && in_array($plg[0], $this->value['enabled']) ? 'checked="checked"' : '';
			$value = is_array($this->value['name']) ? htmlspecialchars(html_entity_decode($this->value['name'][$plg[0]], ENT_QUOTES), ENT_QUOTES) : '';
      if ($new){
        $checked = 'checked="checked"';
      }
      if($value == ''){
        $value = JText::_($plg[1]);
      }
      
		  $html[] = '<input type="checkbox" name="'.$this->name.'[enabled][]" value="'.$plg[0].'" id="'.$this->id.'_'.$plg[0].'" '.$checked.' />';
			$html[] = '<label for="'.$this->id.'_'.$plg[0].'"  style="clear:none;" >'.JText::_($plg[1]).'</label>';
		  $html[] = '<input type="text" name="'.$this->name.'[name]['.$plg[0].']" value="'.$value.'" id="'.$this->id.'_'.$plg[0].'"  />';
			$html[] = '<div style="clear:both;"></div>';
      $i++;
		}
		$html[] = '</fieldset>';
		return implode($html);
	}


}

