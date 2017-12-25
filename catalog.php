<?
require('inc/common.php');

$url = explode('/',$_SERVER['REQUEST_URI']);
$len = sizeof($url);
$links = array();
for($i=2; $i<$len-1; $i++)
	$links[] = $url[$i];

$id_parent = 0;
$rubric = array();
foreach($links as $link)
{
	$rubric = getRow("SELECT * FROM {$prx}catalog WHERE link='{$link}' AND id_parent='{$id_parent}' AND status=1");
	$id_parent = $rubric['id'];
}

if(!$links || !$rubric){ header("HTTP/1.0 404 Not Found"); $code = '404'; require('errors.php'); exit; }

//$ids_parent_rubric = getArrParents("SELECT id,id_parent FROM {$prx}catalog WHERE status=1 AND id='%s'",$rubric['id']);
//$ids_child_rubric = getIdChilds("SELECT * FROM {$prx}catalog WHERE status=1",$rubric['id']);

$good = array();
preg_match('/^(.*).htm/i',end($url),$m);
if($m[1]){
	if(!$good = getRow("SELECT * FROM {$prx}goods WHERE link='{$m[1]}' AND status=1")){ header("HTTP/1.0 404 Not Found"); $code = '404'; require('errors.php'); exit; }
}

//$navigate = get_navigate();

// -------------------- ТОВАР
if($good)
{
	require('goods.php');
	require('tpl/tpl.php');
	exit;
}

header("HTTP/1.0 404 Not Found");
$code = '404';
require('errors.php');
exit;