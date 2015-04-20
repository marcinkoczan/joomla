<?php
/********************************************************************
Product		: Flexicontact
Date		: 23 March 2015
Copyright	: Les Arbres Design 2010-2015
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class FlexicontactViewLog_Detail extends JViewLegacy
{
function display($tpl = null)
{
	JToolBarHelper::title(LAFC_COMPONENT_NAME.': '.JText::_('COM_FLEXICONTACT_LOG'), 'flexicontact.png');
	JToolBarHelper::cancel('log_list');

	Flexicontact_Utility::viewStart();

?>
	<form action="index.php" method="post" name="adminForm" id="adminForm" >
	<input type="hidden" name="option" value="com_flexicontact" />
	<input type="hidden" name="task" value="cancel" />
	</form>
<?php

	$list_prompt = (isset($this->config_data->list_prompt)) ? $this->config_data->list_prompt : JText::_('COM_FLEXICONTACT_LIST').' '.JText::_('COM_FLEXICONTACT_DATA');

	echo '<table class="fc_table">';
	echo "\n".'<tr><td class="prompt"><strong>'.JText::_('COM_FLEXICONTACT_DATE_TIME').'</strong></td><td>'.$this->log_data->datetime.'</td></tr>';
	echo "\n".'<tr><td class="prompt"><strong>'.JText::_('COM_FLEXICONTACT_NAME').'</strong></td><td>'.$this->log_data->name.'</td></tr>';
	echo "\n".'<tr><td class="prompt"><strong>'.JText::_('COM_FLEXICONTACT_EMAIL').'</strong></td><td>'.$this->log_data->email.'</td></tr>';
	echo "\n".'<tr><td class="prompt"><strong>'.JText::_('COM_FLEXICONTACT_ADMIN_SUBJECT').'</strong></td><td>'.$this->log_data->subject.'</td></tr>';
	
// the main message

	echo "\n".'<tr><td class="prompt" valign="top"><strong>'.JText::_('COM_FLEXICONTACT_MESSAGE').'</strong></td>';
	$message = nl2br($this->log_data->message);
	if (substr($message, 0, 6) == '<br />')
    	$message = substr($message, 6);
	echo "\n".'<td style="white-space: normal;">'.$message.'</td></tr>';
	
	if ($this->log_data->list_choice)	
		echo "\n".'<tr><td class="prompt"><strong>'.$list_prompt.'</strong></td><td>'.$this->log_data->list_choice.'</td></tr>';
	if ($this->log_data->field1)
		echo "\n".'<tr><td class="prompt"><strong>'.$this->config_data->field_prompt1.'</strong></td><td>'.$this->log_data->field1.'</td></tr>';
	if ($this->log_data->field2)
		echo "\n".'<tr><td class="prompt"><strong>'.$this->config_data->field_prompt2.'</strong></td><td>'.$this->log_data->field2.'</td></tr>';
	if ($this->log_data->field3)
		echo "\n".'<tr><td class="prompt"><strong>'.$this->config_data->field_prompt3.'</strong></td><td>'.$this->log_data->field3.'</td></tr>';
	if ($this->log_data->field4)
		echo "\n".'<tr><td class="prompt"><strong>'.$this->config_data->field_prompt4.'</strong></td><td>'.$this->log_data->field4.'</td></tr>';
	if ($this->log_data->field5)
		echo "\n".'<tr><td class="prompt"><strong>'.$this->config_data->field_prompt5.'</strong></td><td>'.$this->log_data->field5.'</td></tr>';

// admin_email was only added to the log at version 6.01 so older logs have it blank - better not to show it

	if ($this->log_data->admin_email != '')
		echo "\n".'<tr><td class="prompt"><strong>'.JText::_('COM_FLEXICONTACT_V_EMAIL_TO').'</strong></td><td>'.$this->log_data->admin_email.'</td></tr>';
		
	echo "\n".'<tr><td class="prompt"><strong>'.JText::_('COM_FLEXICONTACT_IP_ADDRESS').'</strong></td><td>'.$this->log_data->ip.'</td></tr>';
	echo "\n".'<tr><td class="prompt"><strong>'.JText::_('COM_FLEXICONTACT_BROWSER').'</strong></td><td>'.$this->log_data->browser_string.'</td></tr>';
	echo "\n".'<tr><td class="prompt"><strong>'.JText::_('COM_FLEXICONTACT_STATUS').'</strong></td><td>'.$this->_status($this->log_data->status_main).'</td></tr>';
	echo "\n".'<tr><td class="prompt"><strong>'.JText::_('COM_FLEXICONTACT_STATUS_COPY').'</strong></td><td>'.$this->_status($this->log_data->status_copy).'</td></tr>';
	echo '</table>';
	Flexicontact_Utility::viewEnd();
}

function _status($status)
{
	if ($status == '0')		// '0' status means no mail was sent
		return '';
	if ($status == '1')		// '1' means email was sent ok
		return '<img src="'.LAFC_ADMIN_ASSETS_URL.'tick.png" border="0" alt="" />';
	return $status;			// anything else was an error
}


}