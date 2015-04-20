<?php
/********************************************************************
Product		: Flexicontact
Date		: 23 March 2015
Copyright	: Les Arbres Design 2010-2015
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class FlexicontactViewConfig_Fields extends JViewLegacy
{
function display($tpl = null)
{
	JToolBarHelper::title(LAFC_COMPONENT_NAME.': '.JText::_('COM_FLEXICONTACT_CONFIG_FIELDS_NAME'), 'flexicontact.png');
	JToolBarHelper::apply();
	JToolBarHelper::save();
	JToolBarHelper::cancel();

// setup the three field options

	$options = array();
	$options['disabled']  = JText::_('COM_FLEXICONTACT_V_DISABLED');
	$options['optional']  = JText::_('COM_FLEXICONTACT_V_OPTIONAL');
	$options['mandatory'] = JText::_('COM_FLEXICONTACT_V_MANDATORY');

	Flexicontact_Utility::viewStart();

?>
	<form action="index.php" method="post" name="adminForm" id="adminForm" >
	<input type="hidden" name="option" value="<?php echo LAFC_COMPONENT; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="config_fields" />
<?php

	echo '<table class="fc_table">';
	
// subject field	

	echo "\n<tr>";
		echo '<td class="prompt">'.JText::_('COM_FLEXICONTACT_V_SHOW_SUBJECT').'</td>';
		echo '<td>'.Flexicontact_Utility::make_radio('show_subject',$this->config_data->show_subject).'</td>';
		echo '<td class="prompt">'.JText::_('COM_FLEXICONTACT_V_DEFAULT_SUBJECT').'</td>';
		echo '<td><input type="text" size="40" name="default_subject" value="'.$this->config_data->default_subject.'" /> '.
			Flexicontact_Utility::make_info(JText::_('COM_FLEXICONTACT_V_DEFAULT_SUBJECT_DESC')).'</td>';
	echo "\n</tr>";
	
// main message field

	echo "\n<tr>";
		echo '<td class="prompt">'.JText::_('COM_FLEXICONTACT_MESSAGE').'</td>';
		echo '<td valign="top">'.Flexicontact_Utility::make_list('area_opt',$this->config_data->area_opt, $options, 0, 'style="margin-bottom:0"').'</td>';
		echo '<td class="prompt">'.JText::_('COM_FLEXICONTACT_V_PROMPT').'</td>';
		echo '<td><input type="text" size="40" name="area_prompt" value="'.$this->config_data->area_prompt.'" /> '.
			Flexicontact_Utility::make_info(JText::_('COM_FLEXICONTACT_V_TEXT_AREA_DESC')).'</td>';
	echo "\n</tr>";
	echo "\n<tr>";
		echo '<td colspan="3"><td>'.JText::_('COM_FLEXICONTACT_V_WIDTH');
		echo ' <input type="text" size="5" name="area_width" value="'.$this->config_data->area_width.'" />';
		echo '&nbsp;&nbsp;'.JText::_('COM_FLEXICONTACT_V_HEIGHT');
		echo ' <input type="text" size="5" name="area_height" value="'.$this->config_data->area_height.'" /></td>';
	echo "\n</tr>";
	
// list field

	echo "\n<tr>";
		echo '<td class="prompt">'.JText::_('COM_FLEXICONTACT_LIST').'</td>';
		echo '<td valign="top">'.Flexicontact_Utility::make_list('list_opt',$this->config_data->list_opt, $options, 0, 'style="margin-bottom:0"').'</td>';
		echo '<td class="prompt">'.JText::_('COM_FLEXICONTACT_V_PROMPT').'</td>';
		echo '<td valign="top"><input type="text" size="40" name="list_prompt" value="'.$this->config_data->list_prompt.'" /> '.
			Flexicontact_Utility::make_info(JText::_('COM_FLEXICONTACT_V_LIST_ITEMS_DESC')).'</td>';
	echo "\n</tr>";
	echo "\n<tr>";
		echo '<td colspan="2"><td class="prompt">'.JText::_('COM_FLEXICONTACT_V_LIST_ITEMS').'</td>';
		echo '<td><textarea rows="3" cols="45" name="list_list">'.$this->config_data->list_list.'</textarea></td>';
	echo "\n</tr>";

// the five optional text fields

	for ($i = 1; $i <= 5; $i++)
		{
		echo "\n<tr>";
			echo '<td  class="prompt">'.JText::_('COM_FLEXICONTACT_FIELD').' '.$i.'</td>';
			$option_name = 'field_opt'.$i;
			echo '<td>'.Flexicontact_Utility::make_list($option_name, $this->config_data->$option_name, $options, 0, 'style="margin-bottom:0"').'</td>';
			echo '<td class="prompt">'.JText::_('COM_FLEXICONTACT_V_PROMPT').'</td>';
			$promptname = 'field_prompt'.$i;
			echo '<td colspan="3"><input type="text" size="40" name="'.$promptname.'" value="'.$this->config_data->$promptname.'" /> '.
				Flexicontact_Utility::make_info(JText::_('COM_FLEXICONTACT_V_TEXT_FIELD_DESC')).'</td>';
		echo "\n</tr>";
		}
	echo '</table></form>';
	Flexicontact_Utility::viewEnd();
}

}