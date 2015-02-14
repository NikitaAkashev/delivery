<?php
/**
* @Copyright ((c) bigemot.ru
* @ http://bigemot.ru/
* @license    GNU/GPL
*/

defined('_JEXEC') or die;

if(isset($groups))
{
	// calculator sum row
	if($res)$html .='<tr height="50"><td colspan="'.(($c)?'2':'2') .'" align="right" style="padding: 0 40px;"></td><td>'.($res).' '.$row->cur.'</td></tr>';
	
	// mail start
	$mailHtml ='<h2>'.$row->title.'</h2><br />'.'<table width="600" cellspacing="0" border="1">'.$html.'</table>';
}

// clon
// идентификаторы структуры вложенности
$clonHtmlStart='<tr><td colspan="'.(($c)?'3':'2') .'" style="padding-left:10px"><b>'.$row->title.'</b><table border="1" width="590" cellspacing="0">';
$clonHtmlEnd='</table></td></tr>';

// cluster
$clusterHtmlStart ='<tr><td colspan="'.(($c)?'3':'2') .'"><table width="100%" style="text-align: center;"><tr>';
$clusterHtmlEnd ='</table></td></tr>';

$clusterRow ='';
$clusterCell ='';
$clusterName='';

$clusterTitle ='<th style="background-color:#EFF1ED; border-right:1px solid;">';
$clusterTitleSum ='<th style="background-color:#EFF1ED; border-right:1px solid;"> '.(isset($curr)?$curr:'').' </th>';
