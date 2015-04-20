<?php
/********************************************************************
Product		: Flexicontact
Date		: 23 March 2015
Copyright	: Les Arbres Design 2010-2015
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class FlexicontactViewConfig_Text extends JViewLegacy
{
function display($tpl = null)
{
	$text_name = $this->param1;			// param1 is the text name, 'page_text' or 'bottom_text'
	
	if ($text_name == 'page_text')
		JToolBarHelper::title(LAFC_COMPONENT_NAME.': '.JText::_('COM_FLEXICONTACT_V_TOP_TEXT'), 'flexicontact.png');
	else
		JToolBarHelper::title(LAFC_COMPONENT_NAME.': '.JText::_('COM_FLEXICONTACT_V_BOTTOM_TEXT'), 'flexicontact.png');
	JToolBarHelper::apply();
	JToolBarHelper::save();
	JToolBarHelper::cancel();

	Flexicontact_Utility::viewStart();

// setup the wysiwg editor

	$editor = JFactory::getEditor();
	
?>
	<form action="index.php" method="post" name="adminForm" id="adminForm" >
	<input type="hidden" name="option" value="<?php echo LAFC_COMPONENT; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="config_text" />
	<input type="hidden" name="param1" value="<?php echo $text_name; ?>" />
<?php 

	echo $editor->display($text_name, htmlspecialchars($this->config_data->$text_name, ENT_QUOTES),'700','350','60','20',array('pagebreak', 'readmore'));
	
	echo '</form>';

	Flexicontact_Utility::viewEnd();

}

}