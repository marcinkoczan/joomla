<?php
/********************************************************************
Product		: Flexicontact
Date		: 3 January 2014
Copyright	: Les Arbres Design 2009-2014
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

require_once JPATH_COMPONENT_ADMINISTRATOR .'/helpers/flexicontact_helper.php';

if (file_exists(JPATH_ROOT.'/LA.php'))
	require_once JPATH_ROOT.'/LA.php';

$jinput = JFactory::getApplication()->input;
$task = $jinput->get('task', '', 'STRING');

if ($task == 'image')
	{
	require_once(LAFC_HELPER_PATH.'/flexi_captcha.php');
	Flexi_captcha::show_image();
	return;
	}

// load our css

$document = JFactory::getDocument();
$document->addStyleSheet('components/com_flexicontact/assets/com_flexicontact.css');

jimport('joomla.application.component.controller');

require_once( JPATH_COMPONENT.'/controller.php' );
$controller = new FlexicontactController();

$controller->execute($task);

$controller->redirect();
