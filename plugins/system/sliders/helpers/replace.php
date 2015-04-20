<?php
/**
 * Plugin Helper File: Replace
 *
 * @package         Sliders
 * @version         4.1.2
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

class plgSystemSlidersHelperReplace
{
	var $helpers = array();
	var $params = null;

	public function __construct()
	{
		require_once __DIR__ . '/helpers.php';
		$this->helpers = plgSystemSlidersHelpers::getInstance();
		$this->params = $this->helpers->getParams();

		$this->params->tag_delimiter = ($this->params->tag_delimiter == 'space') ? '(?:\s|&nbsp;|&\#160;)+' : '=';

		$this->params->tag_open = trim($this->params->tag_open);
		$this->params->tag_close = trim($this->params->tag_close);

		$bts = '((?:<[a-zA-Z][^>]*>\s*){0,3})'; // break tags start
		$bte = '((?:\s*<(?:/[a-zA-Z]|br|BR)[^>]*>){0,3})'; // break tags end

		$this->params->regex = '#'
			. $bts
			. '\{(' . $this->params->tag_open . 's?'
			. '((?:-[a-zA-Z0-9-_]+)?)'
			. $this->params->tag_delimiter
			. '((?:[^\}]*?\{[^\}]*?\})*[^\}]*?)|/' . $this->params->tag_close
			. '(?:-[a-z0-9-_]*)?)\}'
			. $bte
			. '#s';
		$this->params->regex_end = '#'
			. $bts
			. '\{/' . $this->params->tag_close
			. '(?:-[a-z0-9-_]+)?\}'
			. $bte
			. '#s';
		$this->params->regex_link = '#'
			. '\{' . $this->params->tag_link
			. '(?:-[a-z0-9-_]+)?' . $this->params->tag_delimiter
			. '([^\}]*)\}'
			. '(.*?)'
			. '\{/' . $this->params->tag_link . '\}'
			. '#s';

		$this->ids = array();
		$this->matches = array();
		$this->allitems = array();
		$this->setcount = 0;

		$this->setMainClass();
	}

	private function setMainClass()
	{
		$this->mainclass = array();
		$this->mainclass[] = 'nn_sliders';
		$this->mainclass[] = 'accordion panel-group';

		if ($this->params->load_stylesheet == 2)
		{
			$this->mainclass[] = 'oldschool';
		}
	}

	public function replaceTags(&$string, $area = 'article')
	{
		if (!is_string($string) || $string == '')
		{
			return;
		}

		// allow in component?
		if (((($area == 'article' && JFactory::getApplication()->input->get('option') == 'com_content') || $area == 'component'))
			&& in_array(JFactory::getApplication()->input->get('option'), $this->params->disabled_components)
		)
		{
			if (!$this->params->disable_components_remove)
			{
				$this->helpers->get('protect')->protectTags($string);

				return;
			}

			$this->helpers->get('protect')->protect($string);

			$this->handlePrintPage($string);

			nnProtect::unprotect($string);

			return;
		}

		if (
			strpos($string, '{' . $this->params->tag_open) === false
			&& strpos($string, '{' . $this->params->tag_link) === false
		)
		{
			// Links with #slider-name or &slider=slider-name
			$this->replaceLinks($string);

			return;
		}

		$this->helpers->get('protect')->protect($string);

		list($pre_string, $string, $post_string) = nnText::getContentContainingSearches(
			$string,
			array(
				'{' . $this->params->tag_open,
				'{' . $this->params->tag_link
			),
			array(
				'{/' . $this->params->tag_close,
				'{/' . $this->params->tag_link . '}'
			)
		);

		if (JFactory::getApplication()->input->getInt('print', 0))
		{
			// Replace syntax with general html on print pages
			$this->handlePrintPage($string);

			$string = $pre_string . $string . $post_string;

			nnProtect::unprotect($string);

			return;
		}

		$sets = $this->getSets($string);
		$this->initSets($sets);

		// Tag syntax: {slider ...}
		$this->replaceSyntax($string, $sets);

		// Closing tag: {/slider}
		$this->replaceClosingTag($string);

		// Links with #slider-name or &slider=slider-name
		$this->replaceLinks($string);

		// Link tag {sliderlink ...}
		$this->replaceLinkTag($string);

		$string = $pre_string . $string . $post_string;

		nnProtect::unprotect($string);
	}

	private function handlePrintPage(&$string)
	{
		if (preg_match_all($this->params->regex, $string, $matches, PREG_SET_ORDER) > 0)
		{
			foreach ($matches as $match)
			{
				$title = nnText::cleanTitle($match['4']);
				if (strpos($title, '|') !== false)
				{
					list($title, $extra) = explode('|', $title, 2);
				}
				$title = trim(preg_replace('#<\?h[0-9](\s[^>]* )?>#', '', $title));
				$replace = '<' . $this->params->title_tag . ' class="nn_sliders-title">' . $title . '</' . $this->params->title_tag . '>';
				$string = str_replace($match['0'], $replace, $string);
			}
		}
		if (preg_match_all($this->params->regex_end, $string, $matches, PREG_SET_ORDER) > 0)
		{
			foreach ($matches as $match)
			{
				$string = str_replace($match['0'], '', $string);
			}
		}
		if (preg_match_all($this->params->regex_link, $string, $matches, PREG_SET_ORDER) > 0)
		{
			foreach ($matches as $match)
			{
				$href = nnText::getURI($match['1']);
				$link = '<a href="' . $href . '">' . $match['2'] . '</a>';
				$string = str_replace($match['0'], $link, $string);
			}
		}
	}

	private function getSets(&$string)
	{
		if (!preg_match_all($this->params->regex, $string, $matches, PREG_SET_ORDER))
		{
			return array();
		}


		$sets = array();
		$setids = array();

		foreach ($matches as $match)
		{
			if ($match['2']['0'] == '/')
			{
				array_pop($setids);
				continue;
			}

			end($setids);

			$item = new stdClass;
			$item->orig = $match['0'];
			$item->setid = trim(str_replace('-', '_', $match['3']));

			if (empty($setids) || current($setids) != $item->setid)
			{
				$this->setcount++;
				$setids[$this->setcount . '.'] = $item->setid;
			}

			$item->set = str_replace('__', '_', array_search($item->setid, array_reverse($setids)) . $item->setid);
			$item->title = nnText::cleanTitle($match['4']);
			list($item->pre, $item->post) = nnTags::setSurroundingTags($match['1'], $match['5']);

			if (!isset($sets[$item->set]))
			{
				$sets[$item->set] = array();
			}

			$sets[$item->set][] = $item;
		}

		return $sets;
	}


	private function initSets(&$sets)
	{
		$urlitem = JFactory::getApplication()->input->get('slider');
		$itemcount = 0;

		foreach ($sets as $set_id => $items)
		{
			$active = 0;
			foreach ($items as $i => $item)
			{
				// Fix some different syntaxes
				$tag = str_replace(
					array(
						'|alias:',
						'title-close=',
						'title-closed=',
						'title-open=',
						'title-opened=',
					),
					array(
						'|alias=',
						'title-inactive=',
						'title-inactive=',
						'title-active=',
						'title-active=',
					),
					$item->title
				);
				$tag = preg_replace('#^title-(in)?active=#', '', $tag);

				// Get the values from the tag
				$tag = nnTags::getTagValues($tag);

				$item->title = $tag->title;

				$item->title_full = $item->title;

				if (isset($tag->{'title-active'}) || isset($tag->{'title-inactive'}))
				{
					$title_inactive = isset($tag->{'title-inactive'}) ? $tag->{'title-inactive'} : $item->title;
					$title_active = isset($tag->{'title-active'}) ? $tag->{'title-active'} : $item->title;

					// Set main title to the title-active, otherwise to title-inactive
					$item->title = $title_active ?: ($title_inactive ?: $item->title);

					// place the title-active and title-inactive in css controlled spans
					$item->title_full = '<span class="nn_sliders-title-inactive">' . $title_inactive . '</span>'
						. '<span class="nn_sliders-title-active">' . $title_active . '</span>';
				}

				$item->haslink = preg_match('#<a [^>]*>.*?</a>#usi', $item->title);

				$item->title = nnText::cleanTitle($item->title, 1);
				$item->title = $item->title ?: nnText::getAttribute('title', $item->title_full);
				$item->title = $item->title ?: nnText::getAttribute('alt', $item->title_full);
				$item->title = str_replace(array('&nbsp;', '&#160;'), ' ', $item->title);
				$item->title = preg_replace('#\s+#', ' ', $item->title);

				$item->alias = nnText::createAlias(isset($tag->alias) ? $tag->alias : $item->title);
				$item->alias = $item->alias ?: 'slider';

				$item->id = $this->createId($item->alias);
				$item->set = (int) $set_id;
				$item->count = $i + 1;
				$item->active = 0;

				foreach ($tag->params as $j => $val)
				{
					if (!$val)
					{
						continue;
					}

					if (in_array($val, array('active', 'opened', 'open')))
					{
						$active = $i;
						unset($tag->params[$j]);
						continue;
					}

					if (in_array($val, array('inactive', 'closed', 'close')))
					{
						$item->active = 0;
						if ($active == $i)
						{
							$active = '';
						}
						unset($tag->params[$j]);
					}
				}
				$item->class = trim(implode(' ', $tag->params));

				$item->matches = nnText::createUrlMatches(array($item->id, $item->title));
				$item->matches[] = ++$itemcount . '';
				$item->matches[] = $item->set . '.' . ($i + 1);
				$item->matches[] = $item->set . '-' . ($i + 1);

				$item->matches = array_unique($item->matches);
				$item->matches = array_diff($item->matches, $this->matches);
				$this->matches = array_merge($this->matches, $item->matches);

				if ($urlitem && in_array($urlitem, $item->matches))
				{
					$item->active = 1;
					$active = $i;
				}

				if ($active == $i && $item->haslink)
				{
					$active++;
				}

				$sets[$set_id][$i] = $item;
				$this->allitems[] = $item;
			}

			if ($active !== '' && isset($sets[$set_id][$active]))
			{
				$sets[$set_id][$active]->active = 1;
			}
		}
	}

	private function replaceSyntax(&$string, $sets)
	{
		if (!preg_match($this->params->regex_end, $string))
		{
			return;
		}

		foreach ($sets as $items)
		{
			$this->replaceSyntaxItemList($string, $items);
		}
	}

	private function replaceSyntaxItemList(&$string, $items)
	{
		$first = key($items);
		end($items);

		foreach ($items as $i => &$item)
		{
			$this->replaceSyntaxItem($string, $item, $items, ($i == $first));
		}
	}

	private function replaceSyntaxItem(&$string, $item, $items, $first = 0)
	{
		$s = '#' . preg_quote($item->orig, '#') . '#';
		if (@preg_match($s . 'u', $string))
		{
			$s .= 'u';
		}

		if (!preg_match($s, $string, $match))
		{
			return;
		}

		$html = array();
		$html[] = $item->post;
		$html[] = $item->pre;

		$html[] = $this->getPreHtml($items, $first);

		$class = array();
		$class[] = 'accordion-body nn_sliders-body';
		$class[] = 'collapse';
		if ($item->active)
		{
			$class[] = 'in';
		}

		$html[] = '<div class="' . trim('accordion-group panel nn_sliders-group ' . ($item->active ? 'active ' : '') . $item->class) . '">';
		$html[] = '<a id="nn_sliders-scrollto_' . $item->id . '" class="anchor nn_sliders-scroll"></a>';
		$html[] = '<div class="accordion-heading panel-heading">';

		$html[] = $this->getSliderTitle($item, $items);

		$html[] = '</div>';
		$html[] = '<div class="' . trim(implode(' ', $class)) . '" id="' . $item->id . '">';
		$html[] = '<div class="accordion-inner panel-body">';
		$html[] = '<' . $this->params->title_tag . ' class="nn_sliders-title">' . preg_replace('#<\?h[0-9](\s[^>]* )?>#', '', $item->title_full) . '</' . $this->params->title_tag . '>';

		$html = implode("\n", $html);

		$string = nnText::strReplaceOnce($match['0'], $html, $string);
	}

	private function getPreHtml($items, $first = 0)
	{
		if (!$first)
		{
			return '</div></div></div>';
		}

		return '<div class="' . trim(implode(' ', $this->mainclass)) . '" id="set-nn_sliders-' . $items['0']->set . '">'
		. '<a id="nn_sliders-scrollto_' . $items['0']->set . '" class="anchor nn_sliders-scroll"></a>';
	}

	private function getSliderTitle($item, $items)
	{
		if ($item->haslink)
		{
			return $item->title_full;
		}

		$class = 'accordion-toggle nn_sliders-toggle';

		$html = array();
		$html[] = '<a href="' . nnText::getURI($item->id) . '" class="' . $class . '"'
			. ' data-toggle="collapse" data-id="' . $item->id . '" data-parent="#set-nn_sliders-' . $items['0']->set . '"'
			. '><span class="nn_sliders-toggle-inner">';
		$html[] = $item->title_full;
		$html[] = '</span></a>';

		return implode("\n", $html);

	}

	private function replaceClosingTag(&$string)
	{
		if (!preg_match_all($this->params->regex_end, $string, $matches, PREG_SET_ORDER))
		{
			return;
		}

		foreach ($matches as $match)
		{
			$html = '</div></div></div></div>';

			list($pre, $post) = nnTags::setSurroundingTags($match['1'], $match['2']);
			$html = $pre . $html . $post;

			$string = nnText::strReplaceOnce($match['0'], $html, $string);
		}
	}

	private function replaceLinks(&$string)
	{
		// Links with #slider-name
		$this->replaceAnchorLinks($string);
		// Links with &slider=slider-name
		$this->replaceUrlLinks($string);
	}

	private function replaceAnchorLinks(&$string)
	{
		if (!preg_match_all(
			'#(<a\s[^>]*href="([^"]*)?\#([^"]*)"[^>]*>)(.*?)</a>#si',
			$string,
			$matches,
			PREG_SET_ORDER
		)
		)
		{
			return;
		}

		$this->replaceLinksMatches($string, $matches);
	}

	private function replaceUrlLinks(&$string)
	{
		if (!preg_match_all(
			'#(<a\s[^>]*href="([^"]*)(?:\?|&(?:amp;)?)slider=([^"\#&]*)(?:\#[^"]*)?"[^>]*>)(.*?)</a>#si',
			$string,
			$matches,
			PREG_SET_ORDER
		)
		)
		{
			return;
		}

		$this->replaceLinksMatches($string, $matches);
	}

	private function replaceLinksMatches(&$string, $matches)
	{
		$uri = JURI::getInstance();
		$current_urls = array();
		$current_urls[] = $uri->toString(array('path'));
		$current_urls[] = $uri->toString(array('scheme', 'host', 'path'));
		$current_urls[] = $uri->toString(array('scheme', 'host', 'port', 'path'));

		foreach ($matches as $match)
		{
			$link = $match['1'];

			if (
				strpos($link, 'data-toggle=') !== false
				|| strpos($link, 'onclick=') !== false
				|| strpos($link, 'nn_tabs-toggle-sm') !== false
			)
			{
				continue;
			}

			$url = $match['2'];
			if (strpos($url, 'index.php/') === 0)
			{
				$url = '/' . $url;
			}

			if (strpos($url, 'index.php') === 0)
			{
				$url = JRoute::_($url);
			}

			if ($url != '' && !in_array($url, $current_urls))
			{
				continue;
			}

			$id = $match['3'];

			if (!$this->stringHasItem($string, $id))
			{
				// This is a link to a normal anchor or other element on the page
				// Remove the prepending obsolete url and leave the hash
				// $string = str_replace('href="' . $match['2'] . '#' . $id . '"', 'href="#' . $id . '"', $string);

				continue;
			}

			$attribs = $this->getLinkAttributes($id);

			// Combine attributes with original
			$attribs = nnText::combineAttributes($link, $attribs);

			$html = '<a ' . $attribs . '"><span class="nn_sliders-link-inner">' . $match['4'] . '</span></a>';

			$string = str_replace($match['0'], $html, $string);
		}
	}

	private function replaceLinkTag(&$string)
	{
		if (!preg_match_all($this->params->regex_link, $string, $matches, PREG_SET_ORDER))
		{
			return;
		}

		foreach ($matches as $match)
		{
			$this->replaceLinkTagMatch($string, $match);
		}
	}

	private function replaceLinkTagMatch(&$string, $match)
	{
		$id = nnText::createAlias($match['1']);

		if (!$this->stringHasItem($string, $id))
		{
			$html = '<a href="' . nnText::getURI($id) . '">' . $match['2'] . '</a>';

			$string = nnText::strReplaceOnce($match['0'], $html, $string);

			return;
		}

		$html = '<a ' . $this->getLinkAttributes($id) . '>'
			. '<span class="nn_sliders-link-inner">' . $match['2'] . '</span>'
			. '</a>';

		$string = nnText::strReplaceOnce($match['0'], $html, $string);
	}

	private function getLinkAttributes($id)
	{
		$onclick = 'nnSliders.show(this.rel);return false;';

		return 'href="' . nnText::getURI($id) . '"'
		. ' class="nn_sliders-link nn_sliders-link-' . $id . '"'
		. ' rel="' . $id . '"'
		. ' onclick="' . $onclick . '"';
	}

	private function stringHasItem(&$string, $id)
	{
		return (strpos($string, 'data-toggle="collapse" data-id="' . $id . '"') !== false);
	}

	private function createId($alias)
	{
		$id = $alias;

		$i = 1;
		while (in_array($id, $this->ids))
		{
			$id = $alias . '-' . ++$i;
		}

		$this->ids[] = $id;

		return $id;
	}
}
