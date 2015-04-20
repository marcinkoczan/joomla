<?php
/********************************************************************
Product		: Flexicontact
Date		: 23 March 2015
Copyright	: Les Arbres Design 2010-2015
Contact		: http://extensions.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class FlexicontactViewConfig_Images extends JViewLegacy
{
function display($tpl = null)
{
	JToolBarHelper::title(LAFC_COMPONENT_NAME.': '.JText::_('COM_FLEXICONTACT_CAPTCHA_IMAGES'), 'flexicontact.png');
	JToolBarHelper::deleteList('','delete_image');
	JToolBarHelper::cancel();
	Flexicontact_Utility::viewStart();
	
// Get the list of themes

	$app = JFactory::getApplication();
	$filter_theme = $app->getUserStateFromRequest(LAFC_COMPONENT.'.filter_theme','filter_theme', THEME_ALL,'word');
	
	$theme_list = Flexicontact_Utility::make_theme_list();
	$theme_list_html = Flexicontact_Utility::make_list('filter_theme', $filter_theme, $theme_list, 0, 'onchange="submitform( );"');
	
// load the front end Flexicontact language file for the current language

	$lang = JFactory::getLanguage();
	$language = $lang->get('tag');
	if (file_exists(JPATH_SITE.'/language/'.$language.'/'.$language.'.com_flexicontact_captcha.ini'))
		$lang->load('com_flexicontact_captcha', JPATH_SITE);	// 3rd parameter could specify language
	else
		$lang->load('com_flexicontact', JPATH_SITE);

// get an array of filenames
	
    $imageFiles = array();					// create array
    $handle = opendir(LAFC_SITE_IMAGES_PATH);
	if (!$handle)
		{
		echo JText::_('COM_FLEXICONTACT_NO_IMAGES_DIRECTORY');
		Flexicontact_Utility::viewEnd();
		return;
		}
		
	while (($filename = readdir($handle)) != false)
		{
			if ($filename == '.' or $filename == '..')
				continue;
			$imageInfo = @getimagesize(LAFC_SITE_IMAGES_PATH.'/'.$filename);
			if ($imageInfo === false)
				continue;					// not an image
			if ($imageInfo[3] > 3)			// only support gif, jpg or png
				continue;
			if ($imageInfo[0] > 150)		// if X size > 150 pixels ..
				continue;					// .. it's too big so skip it
				
			// Do we display it?
			$prefix = substr($filename, 0, 2);
			
			switch ($filter_theme)
				{
				case THEME_ALL:
					$imageFiles[] = $filename;
					break;
				case THEME_STANDARD:
					if (substr($prefix, 0, 1) == '0')
						$imageFiles[] = $filename;
					break;
				case THEME_TOYS:
					if ($prefix == 'A_' or $prefix == 'B_')
						$imageFiles[] = $filename;
					break;
				case THEME_NEON:
					if ($prefix == 'C_')
						$imageFiles[] = $filename;
					break;
				case THEME_WHITE:
					if ($prefix == 'D_')
						$imageFiles[] = $filename;
					break;
				case THEME_BLACK:
					if ($prefix == 'E_')
						$imageFiles[] = $filename;
					break;
					
				}
   		}
    closedir($handle);
    if (empty($imageFiles) and $filter_theme == THEME_ALL)
		{
		echo JText::_('COM_FLEXICONTACT_NO_IMAGES');
		Flexicontact_Utility::viewEnd();
		return;
		}
    $image_count = count($imageFiles);
	sort($imageFiles);

// start the form

	echo '<form action="index.php" method="post" name="adminForm" id="adminForm" >';
	echo '<input type="hidden" name="option" value="com_flexicontact" />';
	echo '<input type="hidden" name="task" value="config" />';
	echo '<input type="hidden" name="view" value="config_images" />';
	echo '<input type="hidden" name="boxchecked" value="0" />';
	echo '<input type="hidden" name="hidemainmenu" value="0" />';
	
// filters

	echo "\n".'<div>';
	echo $image_count.' '.JText::_('COM_FLEXICONTACT_IMAGES').' ';

	$check_all = 'onclick="Joomla.checkAll(this);"';

	echo '<input type="checkbox" name="toggle" value="" '.$check_all.' /> ';
	echo $theme_list_html;
	echo "\n</div>";

// draw the images
	
	$i = 0;
	foreach ($imageFiles as $filename)
		{
		$imageInfo = getimagesize(LAFC_SITE_IMAGES_PATH.'/'.$filename);
		if ($imageInfo !== false)
			{
			$imageX = $imageInfo[0];
			$imageY = $imageInfo[1];
			}
		
		$text_name = 'COM_FLEXICONTACT_IMAGE_'.strtoupper($filename);
		$description = JText::_($text_name);	// resolved by front end language file
		if ($text_name == $description)			// highlight if not resolved
			$description = '<span style="color: red">'.self::max_length($description, 17).'</span>';
		
		echo "\n".'<div style="display:inline-block;width:190px;border:1px solid gray;margin:1px;padding:5px;">';
		echo "\n".'  <img src="'.JURI::root().'components/com_flexicontact/images/'.$filename.'" alt="" style="float:left; border:none; margin:0 5px 0 0;"/>';
		echo "\n".'<b>'.utf8_encode($filename).'</b><br />';
		echo $description.'<br />';
		echo $imageX.'x'.$imageY.'<br />';
		echo "\n".JHTML::_('grid.id',   $i++, $filename);
		echo '</div>';
		}

	echo '</form>';
	Flexicontact_Utility::viewEnd();
}

//-------------------------------------------------------------------------------
// Forcibly enable long strings to be wrapped by the browser
//
function max_length($text, $max_chars)
{
	if (strlen($text) <= $max_chars)
		return $text;
	return substr($text,0,$max_chars).' '.self::max_length(substr($text,$max_chars), $max_chars);
}

}