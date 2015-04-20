<?php
/********************************************************************
Product		: FlexicontactPlus
Date		: 23 March 2015
Copyright	: Les Arbres Design 2010-2015
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class FlexicontactController extends JControllerLegacy
{
function __construct()
{
	parent::__construct();						// automatically maps public functions
	$this->registerTask('help', 'display');
	$this->registerTask('save', 'apply');
	$this->registerTask('save_css', 'apply_css');
}

function display($cachable = false, $urlparams = false)
{
	Flexicontact_Utility::addSubMenu('help');
	$view = $this->getView('help', 'html');
	$view->display();
}

function config_images()
{
	Flexicontact_Utility::addSubMenu('images');
	$view = $this->getView('config_images', 'html');
	$view->display();
}

function log_list()
{
	Flexicontact_Utility::addSubMenu('log');
	$view = $this->getView('log_list', 'html');
	
	$config_model = $this->getModel('config');
	$config_data = $config_model->getData();

	$logging = (isset($config_data->logging)) ? $config_data->logging : 0;
	$view->logging = $logging;
	
	$log_model = $this->getModel('log');
	$log_list = $log_model->getList();
	$view->log_list = $log_list;
	
	$pagination = $log_model->getPagination();
	$view->pagination =	$pagination;
	
	$view->display();
}

function log_detail()
{
	Flexicontact_Utility::addSubMenu('log');
	$view = $this->getView('log_detail', 'html');

	$jinput = JFactory::getApplication()->input;
	$id = $jinput->get('id', '', 'INT');
	$log_model = $this->getModel('log');
	$log_data = $log_model->getOne($id);
	$view->log_data = $log_data;

	$config_model = $this->getModel('config');
	$config_data = $config_model->getData();
	$view->config_data = $config_data;

	$view->display();
}

function delete_log()
{
	$log_model = $this->getModel('log');
	$jinput = JFactory::getApplication()->input;
	$cids = $jinput->get('cid', array(0), 'ARRAY');
	foreach ($cids as $id)
		$log_model->delete($id);
	$this->setRedirect(LAFC_COMPONENT_LINK."&task=log_list");
}

function config()
{
	Flexicontact_Utility::addSubMenu('config');
	$jinput = JFactory::getApplication()->input;
	$view_name = $jinput->get('view', 'config_list', 'STRING');
	$view = $this->getView($view_name, 'html');
	$jinput = JFactory::getApplication()->input;
	$param1 = $jinput->get('param1', '', 'STRING');
	switch ($view_name)
		{
		case 'config_general':		// these options need the config data, the rest don't
		case 'config_template':
		case 'config_fields':
		case 'config_confirm':
		case 'config_text':
			$config_model = $this->getModel('config');
			$config_model->getData();
			$config_data = $config_model->data;
			$view->config_data = $config_data;
			$view->param1 = $param1;
			break;
		}
	$view->display();
}

function cancel()
{
	$this->setRedirect(LAFC_COMPONENT_LINK."&task=config");
}

function delete_image()
{
	$jinput = JFactory::getApplication()->input;
	$cids = $jinput->get('cid', array(0), 'ARRAY');
	foreach ($cids as $file_name)
		@unlink(LAFC_SITE_IMAGES_PATH.'/'.$file_name);
	$this->setRedirect(LAFC_COMPONENT_LINK."&task=config&view=config_images");
}

function apply()									// save changes to component config
{
	$jinput = JFactory::getApplication()->input;
	$task = $jinput->get('task', '', 'STRING');		// 'save' or 'apply'
	$view = $jinput->get('view', '', 'STRING');		// could be one of several
	$param1 = $jinput->get('param1', '', 'STRING');	// 'user_template', 'admin_template', 'page_text', 'bottom_text', etc
	$config_model = $this->getModel('config');
	$config_model->store($view, $param1);			// save config items
	
	if ($view == 'config_general')
		{
		$log_model = $this->getModel('log');
		$log_model->create();						// create log table if not exists
		}

	if ($task == 'apply')
		$this->setRedirect(LAFC_COMPONENT_LINK."&task=config&view=$view&param1=$param1",JText::_('COM_FLEXICONTACT_SAVED'));
	else
		$this->setRedirect(LAFC_COMPONENT_LINK."&task=config",JText::_('COM_FLEXICONTACT_SAVED'));
}   

function apply_css()								// save changes to front end css
{
	$jinput = JFactory::getApplication()->input;
	$task = $jinput->get('task', '', 'STRING');		// 'save_css' or 'apply_css'
	$css_contents = $_POST['css_contents'];
	if (strlen($css_contents) == 0)
		$this->setRedirect(LAFC_COMPONENT_LINK."&task=config");
	$css_path = JPATH_COMPONENT_SITE.'/assets/com_flexicontact.css';
	$length_written = file_put_contents ($css_path, $css_contents);
	if ($length_written == 0)
		$msg = JText::_('COM_FLEXICONTACT_NOT_SAVED');
	else
		$msg = JText::_('COM_FLEXICONTACT_SAVED');
	if ($task == 'apply_css')
		$this->setRedirect(LAFC_COMPONENT_LINK."&task=config&view=config_css",$msg);
	else
		$this->setRedirect(LAFC_COMPONENT_LINK."&task=config",$msg);
}   


}
