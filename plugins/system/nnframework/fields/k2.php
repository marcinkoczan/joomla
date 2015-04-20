<?php
/**
 * Element: K2
 *
 * @package         NoNumber Framework
 * @version         15.3.6
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once JPATH_PLUGINS . '/system/nnframework/helpers/groupfield.php';

class JFormFieldNN_K2 extends nnFormGroupField
{
	public $type = 'K2';

	protected function getInput()
	{
		if ($error = $this->missingFilesOrTables(array('categories', 'items', 'tags')))
		{
			return $error;
		}

		return $this->getSelectList();
	}

	function getCategories()
	{
		$query = $this->db->getQuery(true)
			->select('COUNT(*)')
			->from('#__k2_categories AS c')
			->where('c.published > -1');
		$this->db->setQuery($query);
		$total = $this->db->loadResult();

		if ($total > $this->max_list_count)
		{
			return -1;
		}

		$query->clear('select')
			->select('c.id, c.parent AS parent_id, c.name AS title, c.published');
		if (!$this->get('getcategories', 1))
		{
			$query->where('c.parent = 0');
		}
		$query->order('c.ordering, c.name');
		$this->db->setQuery($query);
		$items = $this->db->loadObjectList();

		return $this->getOptionsTreeByList($items);
	}

	function getTags()
	{
		$query = $this->db->getQuery(true)
			->select('t.name as id, t.name as name')
			->from('#__k2_tags AS t')
			->where('t.published = 1')
			->order('t.name');
		$this->db->setQuery($query);
		$list = $this->db->loadObjectList();

		return $this->getOptionsByList($list);
	}

	function getItems()
	{
		$query = $this->db->getQuery(true)
			->select('COUNT(*)')
			->from('#__k2_items AS i')
			->where('i.published > -1');
		$this->db->setQuery($query);
		$total = $this->db->loadResult();

		if ($total > $this->max_list_count)
		{
			return -1;
		}

		$query->clear('select')
			->select('i.id, i.title as name, c.name as cat, i.published')
			->join('LEFT', '#__k2_categories AS c ON c.id = i.catid')
			->order('i.title, i.ordering, i.id');
		$this->db->setQuery($query);
		$list = $this->db->loadObjectList();

		return $this->getOptionsByList($list, array('cat', 'id'));
	}
}
