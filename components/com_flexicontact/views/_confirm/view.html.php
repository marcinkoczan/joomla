<?php
/********************************************************************
Product		: Flexicontact
Date		: 12 November 2012
Copyright	: Les Arbres Design 2009-2012
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class FlexicontactView_Confirm extends JViewLegacy
{
function display($tpl = null)
	{
	echo "\n".'<div class="flexicontact">';

	echo $this->message;

	echo "\n</div>";							// end the <div class="flexicontact">
	return;
	}
}
?>
