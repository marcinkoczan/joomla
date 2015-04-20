<?php
/********************************************************************
Product 	: FlexiContact
Date		: 23 January 2014
Copyright	: Les Arbres Design 2014
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

if (class_exists("LAFC_model"))
	return;

class LAFC_model extends JModelLegacy
{

//-------------------------------------------------------------------------------
// Execute a SQL query and return true if it worked, false if it failed
//
function ladb_execute($query)
{
	if (version_compare(JVERSION,"3.0.0","<"))	// if < 3.0
		{
		$this->_db->setQuery($query);
		$this->_db->execute();
		if ($this->_db->getErrorNum())
			{
			$this->ladb_error_text = $this->_db->stderr();
			$this->ladb_error_code = $this->_db->getErrorNum();
			return false;
			}
		return true;
		}
		
// for Joomla 3.x+ use try/catch error handling

	try
		{
		$this->_db->setQuery($query);
		$this->_db->execute();
		}
	catch (RuntimeException $e)
		{
	    $this->ladb_error_text = $e->getMessage();
	    $this->ladb_error_code = $e->getCode();
		return false;
		}
	return true;
}

//-------------------------------------------------------------------------------
// Get a single value from the database as an object and return it, or false if it failed
//
function ladb_loadResult($query)
{
	if (version_compare(JVERSION,"3.0.0","<"))	// if < 3.0
		{
		$this->_db->setQuery($query);
		$result = $this->_db->loadResult();
		if ($this->_db->getErrorNum())
			{
			$this->ladb_error_text = $this->_db->stderr();
			$this->ladb_error_code = $this->_db->getErrorNum();
			return false;
			}
		return $result;
		}

// for Joomla 3.x+ use try/catch error handling

	try
		{
		$this->_db->setQuery($query);
		$result = $this->_db->loadResult();
		}
	catch (RuntimeException $e)
		{
	    $this->ladb_error_text = $e->getMessage();
	    $this->ladb_error_code = $e->getCode();
		return false;
		}
	return $result;
}

//-------------------------------------------------------------------------------
// Get a row from the database as an object and return it, or false if it failed
//
function ladb_loadObject($query)
{
	if (version_compare(JVERSION,"3.0.0","<"))	// if < 3.0
		{
		$this->_db->setQuery($query);
		$result = $this->_db->loadObject();
		if ($this->_db->getErrorNum())
			{
			$this->ladb_error_text = $this->_db->stderr();
			$this->ladb_error_code = $this->_db->getErrorNum();
			return false;
			}
		return $result;
		}

// for Joomla 3.x+ use try/catch error handling

	try
		{
		$this->_db->setQuery($query);
		$result = $this->_db->loadObject();
		}
	catch (RuntimeException $e)
		{
	    $this->ladb_error_text = $e->getMessage();
	    $this->ladb_error_code = $e->getCode();
		return false;
		}
	return $result;
}

//-------------------------------------------------------------------------------
// Get an array of rows from the database and return it, or false if it failed
//
function ladb_loadObjectList($query, $limitstart = 0, $limit = 0)
{
	if (version_compare(JVERSION,"3.0.0","<"))	// if < 3.0
		{
		$this->_db->setQuery($query, $limitstart, $limit);
		$result = $this->_db->loadObjectList();
		if ($this->_db->getErrorNum())
			{
			$this->ladb_error_text = $this->_db->stderr();
			$this->ladb_error_code = $this->_db->getErrorNum();
			return false;
			}
		return $result;
		}

// for Joomla 3.x+ use try/catch error handling

	try
		{
		$this->_db->setQuery($query, $limitstart, $limit);
		$result = $this->_db->loadObjectList();
		}
	catch (RuntimeException $e)
		{
	    $this->ladb_error_text = $e->getMessage();
	    $this->ladb_error_code = $e->getCode();
		return false;
		}
	return $result;
}

//-------------------------------------------------------------------------------
// set the database date language
//
function setDbLanguage()
{
	$langObj = JFactory::getLanguage();
	$lang = $langObj->get('tag');
	$lang[2] = '_';
	$this->ladb_execute("SET lc_time_names = '$lang';");
}

//-------------------------------------------------------------------------------
// get the current database time
// can be called from anywhere, not just models
//
static function getDatabaseDateTime()
{
	$db	= JFactory::getDBO();
	$db->setQuery('Select NOW()');
	return $db->loadResult();
}


}




