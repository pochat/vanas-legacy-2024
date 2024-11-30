<?php

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('imagelist');

class JFormFieldImageradio extends JFormFieldImageList
{

	public $type = 'Imageradio';

	protected function getInput()
	{
		$path = (string) $this->element['directory'];
    $files = $this->getOptions();
		$options = array ();

    $imageurl = JURI::root().$path.'/';

		if ( is_array($files) )
		{
			foreach ($files as $file)
			{
			 if($file->value == '' || $file->value == -1) continue;
			 $s = "";
			 if($this->value == $file->value) $s = " checked='checked' ";
			  $options[] = '<input type="radio" '.$s.' class="inputbox" value="'.$file->value.'" id="'.$this->id.$file->value.'" name="'.$this->name.'">	
                    <label style="float:left; clear:none; min-width:0px;" for="'.$this->id.$file->value.'">
                      <img src="'.str_replace('\\','/',$imageurl.$file->text).'">
                    </label>';
			}
		}
		$s = "";
		if($this->value == -1) $s = " checked='checked' ";
		$options[] = '<input type="radio" '.$s.'  class="inputbox" value="-1" id="'.$this->id.'none" name="'.$this->name.'">	
                    <label style="float:left; clear:none; min-width:0px;" for="'.$this->id.'none">
                      - '.JText::_('None').' -
                    </label>';
		return implode(' ', $options);
		
		return JHTML::_('select.radiolist',  $options, ''.$this->name.'', 'class="inputbox"', 'value', 'text', $this->value, $this->id);
	}
}
