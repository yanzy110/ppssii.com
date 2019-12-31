<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 */


define('APPTYPEID', 101);
define('CURSCRIPT', 'ihome');

require './source/class/class_core.php';

$discuz = C::app();

$discuz->reject_robot();
$modarray = array('index', 'ihome');


$mod = getgpc('mod');
$mod = (empty($mod) || !in_array($mod, $modarray)) ? '' : $mod;

$discuz->init();

define('CURMODULE', $mod);


require DISCUZ_ROOT.'./source/module/ihome/index.php';