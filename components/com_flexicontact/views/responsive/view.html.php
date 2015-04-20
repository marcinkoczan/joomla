<?php
/********************************************************************
Product		: Flexicontact
Date		: 2 April 2015
Copyright	: Les Arbres Design 2009-2015
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class FlexicontactViewResponsive extends JViewLegacy
{

//---------------------------------------------------------------------------------------------------------
// display the contact form
//
function display($tpl = null)
{

// get the menu item parameters

	$app = JFactory::getApplication('site');
	$menu_params =  $app->getParams();

	if (isset($menu_params->pageclass_sfx))
		echo "\n".'<div class="fc_page'.$menu_params->pageclass_sfx.'">';
	else
		echo "\n".'<div class="fc_page">';

// get the page title
	
	$page_heading = '';
	if ($menu_params->get('show_page_title', '0'))
		$page_heading = $menu_params->get('page_title', '');				// Page Display Options, Joomla 1.5
	
	if ($menu_params->get('show_page_heading', '0'))						// Page Display Options, Joomla > 1.5
		$page_heading = $menu_params->get('page_heading', '');

// if there is a page heading in Page Display Options, draw it in H1

	if ($page_heading != '')
		echo "\n<h1>".$page_heading.'</h1>';
		
// if there is a page title in Basic Options, draw it in H2

	if ($menu_params->get('page_hdr', '') != '')							// Basic Options
		echo "\n<h2>".$menu_params->get('page_hdr', '').'</h2>';

// set meta data, if any

	if ($menu_params->get('menu-meta_description'))
		{
		$document = JFactory::getDocument();
		$document->setDescription($menu_params->get('menu-meta_description'));
		}

	if ($menu_params->get('menu-meta_keywords'))
		{
		$document = JFactory::getDocument();
		$document->setMetadata('keywords', $menu_params->get('menu-meta_keywords'));
		}
		
// draw top text, if any

	if (!empty($this->config_data->page_text))		// top text
		{
		JPluginHelper::importPlugin('content');
		$page_text = JHtml::_('content.prepare', $this->config_data->page_text);
		echo "\n".'<div>'.$page_text.'</div>';
		}

	if (!empty($errors))								// if validation failed
		{
		echo '<span class="fc_error">'.JText::_('COM_FLEXICONTACT_MESSAGE_NOT_SENT').'</span>';
		if (isset($errors['top']))
			echo '<br /><span class="fc_error">'.$errors['top'].'</span>';
		}
	
// start the form

	echo "\n".'<form name="fc_form" action="" method="post" class="fc_form">';
	echo JHTML::_('form.token');
	echo '<input type="hidden" name="option" value="'.LAFC_COMPONENT.'" />';
	echo '<input type="hidden" name="task" value="send" />';
	echo '<div class="fc_outer">';

// from name

	echo "\n".'<div class="fc_line"><label for="from_name" class="fc_left">'.JText::_('COM_FLEXICONTACT_FROM_NAME').'</label>';
	echo "\n".'<span><input type="text" class="fc_input" name="from_name" id="from_name" value="'.$this->escape($this->post_data->from_name).'" />'.
				self::get_error('from_name').'</span>';
	echo "\n".'</div>';

// from email address

	echo "\n".'<div class="fc_line"><label for="from_email" class="fc_left">'.JText::_('COM_FLEXICONTACT_FROM_ADDRESS').'</label>';
	echo "\n".'<span><input type="text" class="fc_input" name="from_email" id="from_email" value="'.$this->escape($this->post_data->from_email).'" />'.
				self::get_error('from_email').'</span>';
	echo "\n".'</div>';

// subject

	if ($this->config_data->show_subject)
		{
		echo "\n".'<div class="fc_line"><label for="subject" class="fc_left">'.JText::_('COM_FLEXICONTACT_SUBJECT').'</label>';
		echo "\n".'<span><input type="text" class="fc_input" name="subject" id="subject" value="'.$this->escape($this->post_data->subject).'" />'.
					self::get_error('subject').'</span>';
		echo "\n".'</div>';
		}

// the select list

	if ($this->config_data->list_opt != 'disabled')
		{
		$list_html = Flexicontact_Utility::make_list('list1',$this->post_data->list1, $this->config_data->list_array);
		echo "\n".'<div class="fc_line"><label for="list1" class="fc_left">'.$this->config_data->list_prompt.'</label>';
		echo "\n".'<span>'.$list_html.self::get_error('list').'</span>';
		echo "\n".'</div>';
		}

// the five optional fields

	for ($i=1; $i<=5; $i++)
		{
		$opt_name = 'field_opt'.$i;
		$prompt_name = 'field_prompt'.$i;
		$field_name = 'field'.$i;
		if ($this->config_data->$opt_name == 'disabled')
			continue;
		if ($this->config_data->$prompt_name == '')
			$this->config_data->$prompt_name = '&nbsp;';
		echo "\n".'<div class="fc_line"><label for="'.$field_name.'" class="fc_left">'.$this->config_data->$prompt_name.'</label>';
		echo "\n".'<span><input type="text" class="fc_input" name="'.$field_name.'" id="'.$field_name.'" value="'.$this->escape($this->post_data->$field_name).'" />'.
					self::get_error($field_name).'</span>';
		echo "\n".'</div>';
		}

// the main text area

	if ($this->config_data->area_opt != 'disabled')
		{
		if ($this->config_data->area_prompt == '')
			$this->config_data->area_prompt = '&nbsp;';
		$size = self::size($this->config_data->area_width, 'cols');
		echo "\n".'<div class="fc_line"><label for="area_data" class="fc_left fc_textarea">'.$this->config_data->area_prompt.'</label>';
		echo "\n".'<span><textarea class="fc_input" name="area_data" id="area_data" rows="'.$this->config_data->area_height.'" '.$size.'>'.$this->escape($this->post_data->area_data).'</textarea>'.
					self::get_error('area_data').'</span>';
		echo "\n".'</div>';
		}

// the "send me a copy" checkbox

	if ($this->config_data->show_copy == LAFC_COPYME_CHECKBOX)
		{
		if ($this->post_data->copy_me)
			$checked = 'checked = "checked"';
		else
			$checked = '';
		$checkbox = '<input type="checkbox" class="fc_input" name="copy_me" id="copy_me" value="1" '.$checked.'/>';
		echo "\n".'<div class="fc_line">';
		echo "\n".'<span class="fc_lcb">'.$checkbox.'</span>';
		echo "\n".'<label for="copy_me" class="fc_right">'.JText::_('COM_FLEXICONTACT_COPY_ME').'</label>';
		echo "\n".'</div>';
		}
	
// the agreement required checkbox

	$send_button_state = '';
	if ($this->config_data->agreement_prompt != '')
		{
		if ($this->post_data->agreement_check)
			$checked = 'checked = "checked"';
		else
			{
			$send_button_state = 'disabled="disabled"';
			$checked = '';
			}
		$onclick = ' onclick="if(this.checked==true){form.send_button.disabled=false;}else{form.send_button.disabled=true;}"';
		$checkbox = '<input type="checkbox" class="fc_input" name="agreement_check" id="agreement_check" value="1" '.$checked.$onclick.'/>';
		if (($this->config_data->agreement_name != '') and ($this->config_data->agreement_link != ''))
			{
			$popup = 'onclick="window.open('."'".$this->config_data->agreement_link."', 'fcagreement', 'width=640,height=480,scrollbars=1,location=0,menubar=0,resizable=1'); return false;".'"';
			$link_text = $this->config_data->agreement_prompt.' '.JHTML::link($this->config_data->agreement_link, $this->config_data->agreement_name, 'target="_blank" '.$popup);
			}
		else
			$link_text = $this->config_data->agreement_prompt;
		echo "\n".'<div class="fc_line">';
		echo "\n".'<span class="fc_lcb">'.$checkbox.'</span>';
		echo "\n".'<label for="agreement_check" class="fc_right">'.' '.$link_text.'</label>';
		echo "\n".'</div>';
		}

// the magic word

	if ($this->config_data->magic_word != '')
		{
		echo "\n".'<div class="fc_line"><label for="magic_word" class="fc_left">'.JText::_('COM_FLEXICONTACT_MAGIC_WORD').'</label>';
		echo "\n".'<span><input type="text" class="fc_input" name="magic_word" id="magic_word" value="'.$this->escape($this->post_data->magic_word).'" />'.
					self::get_error('magic_word').'</span>';
		echo "\n".'</div>';
		}

// the image captcha

	if ($this->config_data->num_images > 0)
		{
		require_once(LAFC_HELPER_PATH.'/flexi_captcha.php');
		echo Flexi_captcha::show_image_captcha($this->config_data, self::get_error('imageTest'));
		}

// the send button

	echo "\n".'<div class="fc_line">';
	echo "\n".'<input type="submit" class="button fc_button" name="send_button" '.$send_button_state.' value="'.JText::_('COM_FLEXICONTACT_SEND_BUTTON').'" />';
	echo "\n".'</div>';

	echo '</div>';					// class="fc_outer"

// bottom text

	if (!empty($this->config_data->bottom_text))
		{
		JPluginHelper::importPlugin('content');
		$bottom_text = JHtml::_('content.prepare', $this->config_data->bottom_text);
		echo "\n".'<div>'.$bottom_text.'</div>';
		}
		
	echo "\n</form>";
	echo "\n</div>";				// class="flexicontact"
}

//---------------------------------------------------------------------------------------------------------
// Get and format an error message
//
function get_error($field_name)
{
	if (isset($this->errors[$field_name]))
		return '<span class="fc_error">'.$this->errors[$field_name].'</span>';
	else
		return '';
}

//-------------------------------------------------------------------------------
// field widths can be:
//    0 or blank => nothing at all
// just a number => html attribute size="number" or cols="number"
//          99px => style="width:99px !important;"
//          99em => style="width:99em !important;"
//           99% => style="width:99% !important;"
//
static function size($width, $attribute='size')
{
	if (empty($width))
		return '';
	if (strpos($width, 'px'))
		return ' style="width:'.$width.' !important;"';
	if (strpos($width, 'em'))
		return ' style="width:'.$width.' !important;"';
	if (strpos($width, '%'))
		return ' style="width:'.$width.' !important;"';
	return ' '.$attribute.'="'.$width.'"';
}



	
}
?>
