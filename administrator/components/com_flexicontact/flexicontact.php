<?php
/********************************************************************
Product		: Flexicontact
Date		: 23 March 2015
Copyright	: Les Arbres Design 2010-2015
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

// Pull in the helper file

require_once JPATH_COMPONENT.'/helpers/flexicontact_helper.php';

// load our css

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::base().'components/com_flexicontact/assets/com_flexicontact.css');

// Load MooTools

JHtml::_('behavior.framework');

// create an instance of the controller and tell it to execute $task

require_once( JPATH_COMPONENT.'/controller.php' );
$controller	= new FlexicontactController( );

$jinput = JFactory::getApplication()->input;
$task = $jinput->get('task', 'config', 'STRING');

$controller->execute($task);

$controller->redirect();

