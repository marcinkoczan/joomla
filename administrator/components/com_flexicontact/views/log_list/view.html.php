<?php
/********************************************************************
Product		: Flexicontact
Date		: 23 March 2015
Copyright	: Les Arbres Design 2010-2015
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class FlexicontactViewLog_List extends JViewLegacy
{
function display($tpl = null)
{
	JToolBarHelper::title(LAFC_COMPONENT_NAME.': '.JText::_('COM_FLEXICONTACT_LOG'), 'flexicontact.png');
	JToolBarHelper::deleteList('','delete_log');

	Flexicontact_Utility::viewStart();
	
	if (!$this->logging)
		echo '<span class="flexicontact_error">'.JText::_('COM_FLEXICONTACT_LOGGING').' '.JText::_('COM_FLEXICONTACT_V_DISABLED').'</span>';

// get the order states				

	$app = JFactory::getApplication();
	$filter_order = $app->getUserStateFromRequest(LAFC_COMPONENT.'.filter_order', 'filter_order', 'date_time');
	$filter_order_Dir = $app->getUserStateFromRequest(LAFC_COMPONENT.'.filter_order_Dir', 'filter_order_Dir', 'desc');
	$lists['order_Dir'] = $filter_order_Dir;
	$lists['order'] = $filter_order;

// get the current filters	
		
	$filter_date = $app->getUserStateFromRequest(LAFC_COMPONENT.'.filter_date','filter_date',LAFC_LOG_LAST_28_DAYS,'int');

// make the filter lists

	$date_filters = array(
		LAFC_LOG_ALL           => JText::_('COM_FLEXICONTACT_LOG_ALL'),
		LAFC_LOG_LAST_7_DAYS  => JText::_('COM_FLEXICONTACT_LOG_LAST_7_DAYS'),
		LAFC_LOG_LAST_28_DAYS  => JText::_('COM_FLEXICONTACT_LOG_LAST_28_DAYS'),
		LAFC_LOG_LAST_12_MONTHS => JText::_('COM_FLEXICONTACT_LOG_LAST_12_MONTHS')
		);

	$lists['date_filters']    = Flexicontact_Utility::make_list('filter_date', $filter_date, $date_filters, 0, 'onchange="submitform( );"');					

	$numrows = count($this->log_list);
	$check_all = 'onclick="Joomla.checkAll(this);"';

// Show the list

	?>
	<form action="index.php" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="option" value="<?php echo LAFC_COMPONENT ?>" />
	<input type="hidden" name="task" value="log_list" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="view" value="log_list" />
	<input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />

	<table>
	<tr>
		<td width="100%"></td>
		<td align="right" nowrap="nowrap">
			<?php
			echo $lists['date_filters'];
			echo '&nbsp;';
			echo '<button  class="fc_button" onclick="'."
					this.form.getElementById('filter_date').value='".LAFC_LOG_LAST_28_DAYS."';
					this.form.submit();".'">'.JText::_('COM_FLEXICONTACT_RESET').'</button>';
			?>
		</td>
	</tr>
	</table>

	<table class="adminlist table table-striped">
	<thead>
	<tr>
		<th width="20"><input type="checkbox" name="toggle" value="" <?php echo $check_all; ?> /></th>
		<th class="title" nowrap="nowrap">
			<?php echo JHTML::_('grid.sort', 'COM_FLEXICONTACT_DATE_TIME', 'datetime', $lists['order_Dir'], $lists['order']); ?></th>
		<th class="title" nowrap="nowrap">
			<?php echo JHTML::_('grid.sort', 'COM_FLEXICONTACT_NAME', 'name', $lists['order_Dir'], $lists['order']); ?></th>
		<th class="title" nowrap="nowrap">
			<?php echo JHTML::_('grid.sort', 'COM_FLEXICONTACT_EMAIL', 'email', $lists['order_Dir'], $lists['order']); ?></th>
		<th class="title" nowrap="nowrap">
			<?php echo JHTML::_('grid.sort', 'COM_FLEXICONTACT_ADMIN_SUBJECT', 'subject', $lists['order_Dir'], $lists['order']); ?></th>
		<th class="title" nowrap="nowrap"><?php echo JText::_('COM_FLEXICONTACT_MESSAGE'); ?></th>
		<th class="title" nowrap="nowrap"><?php echo JText::_('COM_FLEXICONTACT_STATUS'); ?></th>
	</tr>
	</thead>

	<tfoot>
	<tr>
		<td colspan="15">
			<?php echo $this->pagination->getListFooter(); ?>
		</td>
	</tr>
	</tfoot>
	
	<tbody>
	<?php
	$k = 0;

	for ($i=0; $i < $numrows; $i++) 
		{
		$row = $this->log_list[$i];
		$link = JRoute::_(LAFC_COMPONENT_LINK.'&task=log_detail&id='.$row->id);
		$checked = JHTML::_('grid.id', $i, $row->id);
		$date = JHTML::link($link, $row->datetime);
		$name = preg_replace('/[^(a-zA-Z \x27)]*/','', $row->name);				// remove all except a-z, A-Z, and '
		$subject = preg_replace('/[^(a-zA-Z1-9 \x27)]*/','', $row->subject);
		$message = preg_replace('/[^(a-zA-Z1-9 \x27)]*/','', $row->short_message);
		$status_main = $this->_status($row->status_main);
		$status_copy = $this->_status($row->status_copy);

		echo "<tr class='row$k'>
				<td align='center'>$checked</td>
				<td>$date</td>
				<td>$name</td>
				<td>$row->email</td>
				<td>$subject</td>
				<td>$message</td>
				<td>$status_main $status_copy</td>
				</tr>\n";
		$k = 1 - $k;
		}
	echo '</tbody></table></form>';

	Flexicontact_Utility::viewEnd();
}

function _status($status)
{
	if ($status == '0')		// '0' status means no mail was sent
		return ' ';
	if ($status == '1')		// '1' means email was sent ok
		return '<img src="'.LAFC_ADMIN_ASSETS_URL.'tick.png" border="0" alt="" />';
	return '<img src="'.LAFC_ADMIN_ASSETS_URL.'x.png" border="0" alt="" />';	// anything else was an error
}

}