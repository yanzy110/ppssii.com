<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}


$post=dhtmlspecialchars($_GET);

//$uname=$post['uname'];
//$upasswd=$post['upasswd'];
//
//$userName = $uname . '@tarena.net';
//$userPwd = $upasswd;
//$bind = @(int) ldap_bind($ldapConnect, $userName, $userPwd);

//if($uname=='aa10'){//╡Бйтсц
//	$bind=1;
//}else{
//	$bind=0;
//}
//debug($_G);
include_once template("ihome/index");