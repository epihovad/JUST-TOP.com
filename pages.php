<?
require('inc/common.php');

$link = clean($_GET['link']);
$page = getRow("SELECT * FROM {$prx}pages WHERE link = '{$link}' AND status = 1");
if(!$page) { header("HTTP/1.0 404 Not Found"); $code = '404'; require('errors.php'); exit; }

$mainID = $page['id'];
$const['Rcol'] = true;

$title = $title.' :: '.$page['name'];
foreach(array('title','keywords','description') as $val){
	if($page[$val]) $$val = $page[$val];
}

ob_start();

?>
<div class="pd25">
	<h1><?=$page['h1']?$page['h1']:$page['name']?></h1>
	<div class="content"><?=$page['text']?></div>
	<a href="" class="back" rel="nofollow" title="вернуться назад"></a>
</div>
<?

$content = ob_get_clean();
require('tpl/tpl.php');
