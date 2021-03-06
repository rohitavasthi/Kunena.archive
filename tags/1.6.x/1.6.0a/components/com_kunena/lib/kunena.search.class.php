<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 * Based on FireBoard Component
 * @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.bestofjoomla.com
 *
 * Based on Joomlaboard Component
 * @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @author TSMF & Jan de Graaff
 **/
// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

DEFINE ( 'KUNENA_URL_LIST_SEPARATOR', ' ' );

class CKunenaSearch {
	/** search results **/
	var $arr_kunena_results = array ();
	/** function **/
	var $func;
	/** search words **/
	var $searchword;
	/** search strings **/
	var $arr_kunena_searchstrings;
	/** search username **/
	var $str_kunena_username;
	/** error number **/
	var $int_kunena_errornr;
	/** error msg **/
	var $str_kunena_errormsg;
	/** params **/
	var $params = array ();
	/** total **/
	var $total = 0;
	/** limitstart **/
	var $limitstart;
	/** limit **/
	var $limit;
	/** defaults **/
	var $defaults = array ('titleonly' => 0, 'searchuser' => '', 'starteronly' => 0, 'replyless' => 0, 'replylimit' => 0, 'searchdate' => '365', 'beforeafter' => 'after', 'sortby' => 'lastpost', 'order' => 'dec', 'catids' => '0', 'show' => '0' );
	/**
	 * Search constructor
	 * @param limitstart First shown item
	 * @param limit Limit
	 */
	function CKunenaSearch() {
		$this->my = JFactory::getUser ();
		$this->app = JFactory::getApplication ();
		$this->doc = JFactory::getDocument ();
		$this->db = JFactory::getDBO ();
		$this->config = KunenaFactory::getConfig ();
		$this->session = KunenaFactory::getSession ();

		// TODO: started_by
		// TODO: active_in


		// Default values for checkboxes depends on function
		$this->func = JString::strtolower ( JRequest::getCmd ( 'func' ) );
		if ($this->func == 'search') {
			$this->defaults ['exactname'] = $this->defaults ['childforums'] = 1;
		} else {
			$this->defaults ['exactname'] = $this->defaults ['childforums'] = 0;
		}

		$q = JRequest::getVar ( 'q', '' ); // Search words
		// Backwards compability for old templates
		if (empty ( $q ) && isset ( $_REQUEST ['searchword'] )) {
			$q = JRequest::getVar ( 'searchword', '' );
		}
		$q = JString::trim ( $q );
		$this->params ['titleonly'] = JRequest::getInt ( 'titleonly', $this->defaults ['titleonly'] );
		$this->params ['searchuser'] = JRequest::getVar ( 'searchuser', $this->defaults ['searchuser'] );
		$this->params ['starteronly'] = JRequest::getInt ( 'starteronly', $this->defaults ['starteronly'] );
		$this->params ['exactname'] = JRequest::getInt ( 'exactname', $this->defaults ['exactname'] );
		$this->params ['replyless'] = JRequest::getInt ( 'replyless', $this->defaults ['replyless'] );
		$this->params ['replylimit'] = JRequest::getInt ( 'replylimit', $this->defaults ['replylimit'] );
		$this->params ['searchdate'] = JRequest::getVar ( 'searchdate', $this->defaults ['searchdate'] );
		$this->params ['beforeafter'] = JRequest::getVar ( 'beforeafter', $this->defaults ['beforeafter'] );
		$this->params ['sortby'] = JRequest::getVar ( 'sortby', $this->defaults ['sortby'] );
		$this->params ['order'] = JRequest::getVar ( 'order', $this->defaults ['order'] );
		$this->params ['childforums'] = JRequest::getInt ( 'childforums', $this->defaults ['childforums'] );
		$this->params ['catids'] = strtr ( JRequest::getVar ( 'catids', '0', 'get' ), KUNENA_URL_LIST_SEPARATOR, ',' );
		$this->params ['show'] = JRequest::getInt ( 'show', $this->defaults ['show'] );
		$this->limitstart = JRequest::getInt ( 'limitstart', 0 );
		$this->limit = JRequest::getInt ( 'limit', $this->config->messages_per_page_search );
		extract ( $this->params );

		if ($this->limit < 1 || $this->limit > 40)
			$this->limit = $this->limit = $this->config->messages_per_page_search;

		if (isset ( $_POST ['q'] ) || isset ( $_POST ['searchword'] )) {
			$this->params ['catids'] = implode ( ',', JRequest::getVar ( 'catids', array (0), 'post', 'array' ) );
			$url = CKunenaLink::GetSearchURL ( $this->func, $q, $this->limitstart, $this->limit, $this->getUrlParams () );
			header ( "HTTP/1.1 303 See Other" );
			header ( "Location: " . htmlspecialchars_decode ( $url ) );
			$this->app->close ();
		}

		if ($q == JText::_('COM_KUNENA_GEN_SEARCH_BOX'))
			$q = '';
		$this->searchword = $q;
		$arr_searchwords = explode ( ' ', $q );
		$do_search = FALSE;
		$this->arr_kunena_searchstrings = array ();
		foreach ( $arr_searchwords as $q ) {
			$q = JString::trim ( $q );
			if (JString::strlen ( $q ) > 2)
				$do_search = TRUE;
			$this->arr_kunena_searchstrings [] = $q;
		}
		if (JString::strlen ( $this->params ['searchuser'] ) > 0)
			$do_search = TRUE;
		$arr_searchwords = $this->arr_kunena_searchstrings;
		$this->str_kunena_username = $this->params ['searchuser'];

		if ($do_search != TRUE) {
			$this->int_kunena_errornr = 1;
			$this->str_kunena_errormsg = JText::_('COM_KUNENA_SEARCH_ERR_SHORTKEYWORD');
			return;
		}

		$search_forums = $this->get_search_forums ( $this->params ['catids'], $this->params ['childforums'] );
		/* if there are no forums to search in, set error and return */
		if (empty ( $search_forums )) {
			$this->int_kunena_errornr = 2;
			$this->str_kunena_errormsg = JText::_('COM_KUNENA_SEARCH_NOFORUM');
			return;
		}

		for($x = 0; $x < count ( $arr_searchwords ); $x ++) {
			$searchword = $arr_searchwords [$x];
			$searchword = $this->db->getEscaped ( JString::trim ( $searchword ) );
			if (empty ( $searchword ))
				continue;
			$matches = array ();
			$not = '';
			$operator = ' OR ';

			if (strstr ( $searchword, '-' ) == $searchword) {
				$not = 'NOT';
				$operator = 'AND';
				$searchword = JString::substr ( $searchword, 1 );
			}

			if ($this->params ['titleonly'] == '0') {
				$querystrings [] = "(t.message {$not} LIKE '%{$searchword}%' {$operator} m.subject {$not} LIKE '%{$searchword}%')";
			} else {
				$querystrings [] = "(m.subject {$not} LIKE '%{$searchword}%')";
			}
		}

		//User searching
		if (JString::strlen ( $this->params ['searchuser'] ) > 0) {
			if ($this->params ['exactname'] == '1') {
				$querystrings [] = "m.name LIKE '" . $this->db->getEscaped ( $this->params ['searchuser'] ) . "'";
			} else {
				$querystrings [] = "m.name LIKE '%" . $this->db->getEscaped ( $this->params ['searchuser'] ) . "%'";
			}
		}

		$time = 0;
		switch ($this->params ['searchdate']) {
			case 'lastvisit' :
				$this->db->setQuery ( "SELECT lasttime FROM #__kunena_sessions WHERE userid='{$this->my->id}'" );
				$time = $this->db->loadResult ();
				break;
			case 'all' :
				break;
			case '1' :
			case '7' :
			case '14' :
			case '30' :
			case '90' :
			case '180' :
			case '365' :
				$time = time () - 86400 * intval ( $this->params ['searchdate'] ); //24*3600
				break;
			default :
				$time = time () - 86400 * 365;
				$searchdate = '365';
		}

		if ($time) {
			if ($this->params ['beforeafter'] == 'after') {
				$querystrings [] = "m.time > '{$time}'";
			} else {
				$querystrings [] = "m.time <= '{$time}'";
			}
		}

		/* build query */
		$querystrings [] = "m.moved='0'";

		//Search also unapproved, trash
		$this->show = array();
		if ( CKunenaTools::isModerator($this->my->id) && $this->params['show']>0 ) {
			$search_forums_array = explode(',', $search_forums);
			$search_forums = array();
			foreach ($search_forums_array as $currforum) {
				if (CKunenaTools::isModerator($this->my->id, $currforum)) $search_forums[] = $currforum;
			}
			if (empty($search_forums)) return;
			$search_forums = implode ( ',', $search_forums );
			$querystrings [] = "m.hold='".(int)$this->params['show'] ."'";
		} else {
			$querystrings [] = "m.hold='0'";
		}
		$querystrings [] = "m.catid IN ({$search_forums})";

		$where = implode ( ' AND ', $querystrings );

		$groupby = array ();
		if ($this->params ['order'] == 'dec')
			$order1 = 'DESC';
		else
			$order1 = 'ASC';
		switch ($this->params ['sortby']) {
			case 'title' :
				$orderby = "m.subject {$order1}, m.time {$order1}";
				break;
			case 'views' :
				$orderby = "m.hits {$order1}, m.time {$order1}";
				break;
			/*
        case 'threadstart':
		$orderby = "m.time {$order1}, m.ordering {$order1}, m.hits {$order1}";
        break;
*/
			case 'forum' :
				$orderby = "m.catid {$order1}, m.time {$order1}, m.ordering {$order1}";
				break;
			/*
        case 'replycount':
        case 'postusername':
*/
			case 'lastpost' :
			default :
				$orderby = "m.time {$order1}, m.ordering {$order1}, m.catid {$order1}";
		}

		if (count ( $groupby ) > 0)
			$groupby = ' GROUP BY ' . implode ( ',', $groupby );
		else
			$groupby = '';

		/* get total */
		$this->db->setQuery ( "SELECT COUNT(*) FROM #__kunena_messages AS m JOIN #__kunena_messages_text AS t ON m.id=t.mesid WHERE {$where} {$groupby}" );
		$this->total = $this->db->loadResult ();
		KunenaError::checkDatabaseError();

		/* if there are no forums to search in, set error and return */
		if ($this->total == 0) {
			$this->int_kunena_errornr = 3;
			$this->str_kunena_errormsg = JText::_('COM_KUNENA_SEARCH_ERR_NOPOSTS');
			return;
		}
		if ($this->total < $this->limitstart)
			$this->limitstart = ( int ) ($this->total / $this->limit);

		/* get results */
		$sql = "SELECT m.id, m.subject, m.catid, m.thread, m.name, m.time, t.mesid, t.message,
						c.name AS catname, c.class_sfx
        		FROM #__kunena_messages_text AS t JOIN #__kunena_messages AS m ON m.id=t.mesid
        		JOIN #__kunena_categories AS c ON m.catid = c.id
        		WHERE {$where} {$groupby} ORDER BY {$orderby}";
		$this->db->setQuery ( $sql, $this->limitstart, $this->limit );
		$rows = $this->db->loadObjectList ();
		KunenaError::checkDatabaseError();

		$this->str_kunena_errormsg = $sql . '<br />' . $this->db->getErrorMsg ();

		if (count ( $rows ) > 0)
			$this->arr_kunena_results = $rows;
		else
			$this->arr_kunena_results = array ();

		return;
	}
	/** get searchstrings (array) **/
	function get_searchstrings() {
		return $this->arr_kunena_searchstrings;
	}
	function get_searchusername() {
		return $this->str_kunena_username;
	}
	/** get results (array) **/
	function get_results() {
		return $this->arr_kunena_results;
	}
	function getUrlParams() {
		$url_params = '';
		foreach ( $this->params as $param => $value ) {
			if ($param == 'catids')
				$value = strtr ( $value, ',', KUNENA_URL_LIST_SEPARATOR );
			if ($value != $this->defaults [$param])
				$url_params .= "&amp;$param=" . urlencode ( $value );
		}
		return $url_params;
	}
	function get_search_forums($catids, $childforums = 1) {
		/* get allowed forums */
		$allowed_string = '';
		if ($this->session->allowed && $this->session->allowed != 'na') {
			$allowed_string = "id IN ({$this->session->allowed})";
		} else {
			$allowed_string = "published='1' AND pub_access='0'";
		}
		$this->db->setQuery ( "SELECT id, parent FROM #__kunena_categories WHERE {$allowed_string}" );
		$allowed_forums = $this->db->loadAssocList ( 'id' );
		if (KunenaError::checkDatabaseError()) return array();

		$allow_list = array ();
		foreach ( $allowed_forums as $forum ) {
			// Children list: parent => array(child1, child2, ...)
			$allow_list [$forum ['parent']] [] = $forum ['id'];
		}

		$catids = explode ( ',', $catids );
		$result = array ();
		if (count ( $catids ) > 0 && ! in_array ( 0, $catids )) {
			// Algorithm:
			// Start with selected categories and pop them from the catlist one by one
			// Every popped item in the catlist will be added into result list
			// For every category: push all its children into the catlist
			$cur = array_pop ( $catids );
			do {
				$result [$cur] = $cur;
				if (array_key_exists ( $cur, $allow_list ))
					foreach ( $allow_list [$cur] as $forum )
						if (! in_array ( $forum, $catids ))
							array_push ( $catids, $forum );
				$cur = array_pop ( $catids );
			} while ( $cur );
			$search_forums = implode ( ",", $result );
		} else {
			$search_forums = implode ( ",", array_keys ( $allowed_forums ) );
		}
		return $search_forums;
	}

	/**
	 * Display results
	 * @param string actionstring
	 */
	function show() {
		extract ( $this->params );
		$this->q = implode ( " ", $this->get_searchstrings () );
		$this->quser = $this->get_searchusername ();

		$this->selected = ' selected="selected"';
		$this->checked = ' checked="checked"';
		$this->advsearch_hide = 1;
		if ($this->int_kunena_errornr) {
			$this->q = $this->searchword;
			$this->advsearch_hide = 0;
		}

		$this->tabclass = array ("sectiontableentry1", "sectiontableentry2" );

		//category select list
		$options = array ();
		$options [] = JHTML::_ ( 'select.option', '0', JText::_('COM_KUNENA_SEARCH_SEARCHIN_ALLCATS') );
		$this->categorylist = CKunenaTools::forumSelectList ( 'searchlist', explode ( ',', $this->params ['catids'] ), $options, 'class="inputbox" size="8" multiple="multiple"', true );

		CKunenaTools::loadTemplate('/search/advsearch.php');

		$this->results = $this->get_results ();

		$this->pagination = $this->getPagination ( $this->func, $this->q, $this->getUrlParams (), floor ( $this->limitstart / $this->limit ) + 1, $this->limit, floor ( $this->total / $this->limit ) + 1, 7 );

		if (defined ( 'KUNENA_DEBUG' ))
			echo '<p style="background-color:#FFFFCC;border:1px solid red;">' . $this->str_kunena_errormsg . '</p>';

		$searchlist = $this->get_searchstrings ();
		foreach ( $this->results as $i => $result ) {
			// Clean up subject
			$ressubject = KunenaParser::parseText ($result->subject);
			// Strip smiles and bbcode out of search results; they look ugly
			$resmessage = KunenaParser::parseBBCode ($result->message);

			foreach ( $searchlist as $searchword ) {
				if (empty ( $searchword ))
					continue;
				$ressubject = preg_replace ( "/" . preg_quote ( $searchword, '/' ) . "/iu", '<span  class="searchword" >' . $searchword . '</span>', $ressubject );
				$resmessage = preg_replace ( "/" . preg_quote ( $searchword, '/' ) . "/iu", '<span  class="searchword" >' . $searchword . '</span>', $resmessage );
			}
			$this->results [$i]->subject = $ressubject;
			$this->results [$i]->message = $resmessage;
		}

		CKunenaTools::loadTemplate('/search/search.php');
	}

	function getPagination($function, $q, $urlparams, $page, $limit, $totalpages, $maxpages) {
		if ($page == 0)
			$page ++;
		$startpage = ($page - floor ( $maxpages / 2 ) < 1) ? 1 : $page - floor ( $maxpages / 2 );
		$endpage = $startpage + $maxpages;
		if ($endpage > $totalpages) {
			$startpage = ($totalpages - $maxpages) < 1 ? 1 : $totalpages - $maxpages;
			$endpage = $totalpages;
		}

		$output = '<ul class="kpagination">';
		$output .= '<li class="page">' . JText::_('COM_KUNENA_PAGE') . '</li>';

		if ($startpage > 1) {
			if ($endpage < $totalpages)
				$endpage --;
			$output .= '<li>' . CKunenaLink::GetSearchLink ( $function, $q, 0, $limit, 1, $urlparams, $rel = 'nofollow' ) . '</li>';

			if ($startpage > 2) {
				$output .= '<li class="more">...</li>';
			}
		}

		for($i = $startpage; $i <= $endpage && $i <= $totalpages; $i ++) {
			if ($page == $i) {
				$output .= '<li class="active">' . $i . '</li>';
			} else {
				$output .= '<li>' . CKunenaLink::GetSearchLink ( $function, $q, ($i - 1) * $limit, $limit, $i, $urlparams, $rel = 'nofollow' ) . '</li>';
			}
		}

		if ($endpage < $totalpages) {
			if ($endpage < $totalpages - 1) {
				$output .= '<li class="more">...</li>';
			}

			$output .= '<li>' . CKunenaLink::GetSearchLink ( $function, $q, ($totalpages - 1) * $limit, $limit, $totalpages, $urlparams, $rel = 'nofollow' ) . '</li>';
		}

		$output .= '</ul>';
		return $output;
	}
}

