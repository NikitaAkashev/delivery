<?php
/**
* @Copyright ((c) bigemot.ru
* @ http://bigemot.ru/
* @license    GNU/GPL
*/

defined('_JEXEC') or die;

// идентификаторы структуры вложенности клона
//  clon 
$clonHtmlStart='';
$clonHtmlEnd='';
//  cluster
$clusterHtmlStart ='<tr><td colspan="'.(($c)?'3':'2') .'" style="padding-left:10px">_claster';
$clusterHtmlEnd ='claster_</td></tr>';
$clusterRow ='_cLrow_';
$clusterCell ='_cLcell_';
$clusterTitle ='_clasterTitle_';
$clusterTitleSum ='';

// дополнительное поле строки кластера с его заголовком
//$clusterName=$clusterCell.'<td>List:  '.$clusterName.'</td>';

$clusterName='';
if(isset($groups))
{
	// calculator sum row
	if($res)$html .='<br />'.($res).' '.$row->cur;
	
	// mail start
	$mailHtml ='<br />'.$row->title.'<br /><br />'.$html;
}



