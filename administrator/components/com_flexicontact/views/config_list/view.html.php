<?php
/********************************************************************
Product	   : Flexicontact
Date       : 23 March 2015
Copyright  : Les Arbres Design 2009-2015
Contact	   : http://www.lesarbresdesign.info
Licence	   : GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted access');

class FlexicontactViewConfig_List extends JViewLegacy
{
function display($tpl = null)
{
	JToolBarHelper::title(LAFC_COMPONENT_NAME.': '.JText::_('COM_FLEXICONTACT_CONFIGURATION'), 'flexicontact.png');
	Flexicontact_Utility::viewStart();

// Set up the configuration links

	$config_table = array(
		array(
			'link' => LAFC_COMPONENT_LINK.'&task=config&view=config_general',
			'icon' => 'config_general.gif',
			'name' => 'COM_FLEXICONTACT_CONFIG_GENERAL_NAME',
			'desc' => 'COM_FLEXICONTACT_CONFIG_GENERAL_DESC'),
		array(
			'link' => LAFC_COMPONENT_LINK.'&task=config&view=config_template&param1=admin_template',
			'icon' => 'config_email_a.gif',
			'name' => 'COM_FLEXICONTACT_CONFIG_ADMIN_EMAIL_NAME',
			'desc' => 'COM_FLEXICONTACT_CONFIG_ADMIN_EMAIL_DESC'),
		array(
			'link' => LAFC_COMPONENT_LINK.'&task=config&view=config_template&param1=user_template',
			'icon' => 'config_email_u.gif',
			'name' => 'COM_FLEXICONTACT_CONFIG_USER_EMAIL_NAME',
			'desc' => 'COM_FLEXICONTACT_CONFIG_USER_EMAIL_DESC'),
		array(
			'link' => LAFC_COMPONENT_LINK.'&task=config&view=config_confirm',
			'icon' => 'config_text.gif',
			'name' => 'COM_FLEXICONTACT_CONFIG_CONFIRM_NAME',
			'desc' => 'COM_FLEXICONTACT_CONFIG_CONFIRM_DESC'),
		array(
			'link' => LAFC_COMPONENT_LINK.'&task=config&view=config_fields',
			'icon' => 'config_fields.gif',
			'name' => 'COM_FLEXICONTACT_CONFIG_FIELDS_NAME',
			'desc' => 'COM_FLEXICONTACT_CONFIG_FIELDS_DESC'),
		array(
			'link' => LAFC_COMPONENT_LINK.'&task=config&view=config_text&param1=page_text',
			'icon' => 'config_text_top.gif',
			'name' => 'COM_FLEXICONTACT_V_TOP_TEXT',
			'desc' => 'COM_FLEXICONTACT_CONFIG_TOP_BOTTOM_TEXT_DESC'),
		array(
			'link' => LAFC_COMPONENT_LINK.'&task=config&view=config_text&param1=bottom_text',
			'icon' => 'config_text_bottom.gif',
			'name' => 'COM_FLEXICONTACT_V_BOTTOM_TEXT',
			'desc' => 'COM_FLEXICONTACT_CONFIG_TOP_BOTTOM_TEXT_DESC'),
		array(
			'link' => LAFC_COMPONENT_LINK.'&task=config&view=config_css',
			'icon' => 'config_css.gif',
			'name' => 'COM_FLEXICONTACT_CONFIG_CSS_NAME',
			'desc' => 'COM_FLEXICONTACT_CONFIG_CSS_DESC')
		);

	// show the list
	?>
	<form action="index.php" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="option" value="<?php echo LAFC_COMPONENT ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="config_list" />
	<table class="adminlist table table-striped">
	<thead>
		<tr>
			<th width = "5%"></th>
			<th width = "20%" nowrap="nowrap"><?php echo  JText::_('COM_FLEXICONTACT_CONFIG_NAME'); ?></th>
			<th width = "75%" nowrap="nowrap"><?php echo  JText::_('COM_FLEXICONTACT_CONFIG_DESC'); ?></th>
		</tr>
	</thead>

	<?php
	$k = 0;
	foreach ($config_table as $config)
		{
		$link = JRoute::_($config['link']);
		$icon = '<img src="'.LAFC_ADMIN_ASSETS_URL.$config['icon'].'" alt="" />';
		echo "<tr class='row$k'>
				<td>$icon</td>
				<td>".JHTML::link($link, JText::_($config['name']))."</td>
				<td>".JText::_($config['desc'])."</td>
			</tr>";
		$k = 1 - $k;
		}
	echo '</table></form>';
	Flexicontact_Utility::viewEnd();
	}
}