<?php
/**
* @Copyright ((c) bigemot.ru
* @ http://bigemot.ru/
* @license    GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class qfCheck
{
	
	function qfCheck() 
{
	$this->post=JRequest::get('post');
}
	
	function getSelect($pat, $post, $c) 
	{
		static $i=0;$add='';$k=0;$value='';$svz=0;
		
		preg_match('/([^"]+)(?=" class="inp_sel")/', $pat, $m);
		$title=$m[0];
		
		$opts=explode('</div><div>',$pat);
		
		foreach($opts as $opt) {
			$calc='';
			$vals=explode('input',$opt);
			
//			if($c){
				foreach($vals as $val){
					if(strpos($val,'opt_modifer'))$calc.=$val{strpos($val,'value="')+7};
				}
				
				preg_match('/([^"]+)(?=" class="opt_price)/', $opt, $o);
				$calc.=trim($o[0]);
//			}
		
			if($k.'_'.$calc==$post['sel'][$i]){
				preg_match('/([^"]+)(?=" class="inp_opt")/', $opt, $m);
				$value=$m[0];
				if($c) {
					$add=$calc;
					if(!$this->isCluster)$GLOBALS['qfSum'].=';'.$calc;
				}
				preg_match('/([^"]+)(?=" class="inp_svz")/', $opt, $m);
				if($m)$svz=(int)$m[0];
//				if($svz){echo $svz;die;}
			}
			
			$k++;
		}
		
		$i++;
		
		$html = $this->getTr($title,$value,$add,$c);
		if($svz)$html.=$this->getChildForm($svz);
		
		return $html;
	}
	
	function getChildForm($id) 
	{
		$row=$this->getClonerQuery($id);
		
		if(!$row->settlement) return '';
		
		return $this->getFilds($row->cod,$row->calc, $id);
	}
	
	function getFilds($settlement,$c, $reqid, $tmpl='default') 
	{
		if(!isset($this->isCluster))$this->isCluster=false;
		
		$row=$this->getClonerQuery($reqid);
		$html = '';
		if($row->mailare1) $html.=$this->getTr('',$row->mailare1,'',$c);
		
		$arr=explode('</tr><tr>',$settlement);
		
		foreach($arr as $ar) {
			if(strpos($ar,'this)">select<')!== false) $html.=$this->getSelect($ar,$this->post,$c);
			elseif(strpos($ar,'r_td">checkbox')!== false) $html.=$this->getCheckbox($ar,$this->post,$c);
			elseif(strpos($ar,'r_td">email')!== false) $html.=$this->getEmail($ar,$this->post,$c);
			elseif(strpos($ar,'radio</a></td>')!== false) $html.=$this->getRadio($ar,$this->post,$c,$reqid);
			elseif(strpos($ar,'r_td">text<')!== false) $html.=$this->getText($ar,$this->post,$c);
			elseif(strpos($ar,'r_td">textarea<')!== false) $html.=$this->getTextarea($ar,$this->post,$c);
			elseif(strpos($ar,'r_td">calctext<')!== false) $html.=$this->getCalctext($ar,$this->post,$c);
			elseif(strpos($ar,'r_td">file<')!== false) $html.=$this->getFile($ar,$this->post,$c);
			elseif(strpos($ar,'>cloner</td>')!== false) $html.=$this->getCloner($ar,$this->post,$c);
		}
		if($row->mailare2) $html.=$this->getTr('',$row->mailare2,'',$c);
		return $html;
	}
	
	function getClonerQuery($id) 
	{
		if(!isset($this->ClonerQuery[$id])){
			$db		=& JFactory::getDBO();
			$db->setQuery('SELECT * FROM #__quickform WHERE published=1 AND id = '.(int)$id);
			$row = $db->loadObject();
			!isset($this->curr)?$this->curr=$row->cur:'';
			$row->cod=QuickFormHelper::coder($row->settlement,'d');
			
			$params=json_decode($row->params, TRUE);
			if(!isset($params['tmpl'])||!$row->tmpl=$params['tmpl']) $row->tmpl='default';
			if(!isset($params['mailare1'])||!$row->mailare1=$params['mailare1']) $row->mailare1='';
			if(!isset($params['mailare2'])||!$row->mailare2=$params['mailare2']) $row->mailare2='';
			
			$this->ClonerQuery[$id]=$row;
		}
		return $this->ClonerQuery[$id];
	}
	
	function getCloner($pat, $post, $c) 
	{
		preg_match('/([^"]+)(?=" class="inp_clone")/', $pat, $m);
		
		if($id=(int)$m[0])
		{
			static $a=-1;$a++;
						
			preg_match('/([^"]+)(?=" class="inp_len")/', $pat, $m);
			$len=(int)$m[0]?(int)$m[0]:1;
			preg_match('/([^"]+)(?=" class="inp_max")/', $pat, $m);
			$max=(int)$m[0];
			
			$row=$this->getClonerQuery($id);
			$this->isCluster=$len>1?true:false;
			if(!isset($this->clonArr))$this->clonArr=explode(',',$this->post['clonStr']);
			if(!isset($this->arrId[$a]))$this->arrId[$a]=array($row->cod,$row->calc, $id, $row->tmpl, $row->title);
			
			if(isset($this->arrId[$a-1]))$c=$this->arrId[$a-1][1];
			include JPATH_COMPONENT.'/helpers/mailtmpl/'.$this->arrId[$a][3].'.php';
			
			// реализуем все вложенные в клонер структуры
			$clon=$clonHtmlStart;
			while($this->clonArr[$a]){
				$this->clonArr[$a]--;
				
				if($this->isCluster)	$clon.=$this->getCluster($a,$max);
				else $clon.=$this->getFilds($this->arrId[$a][0],$this->arrId[$a][1],$this->arrId[$a][2]);
			}
			$clon.=$clonHtmlEnd;
			array_splice($this->clonArr, $a, 1);
			array_splice($this->arrId, $a, 1);
			$a--;
			
			return $clon;
		}
	}
	
	function getCluster($a,$max) 
	{
		$this->getFilds($this->arrId[$a][0],$this->arrId[$a][1],$this->arrId[$a][2]);
		$clon2 = $this->cloncells;
		$clonlLen=sizeof($clon2);
		$clusterName=$this->arrId[$a][4];
		$c=$this->arrId[$a-1][1];$curr=$this->curr; 
		include JPATH_COMPONENT.'/helpers/mailtmpl/'.$this->arrId[$a][3].'.php';
		$Html=$clusterHtmlStart;
		
		foreach($clon2 as $el){
			$Html.=$clusterTitle.$el['label'].'</th>';
		}
		if($this->arrId[$a][1])$Html.=$clusterTitleSum.'';
		$Html.='</tr>';
		$ma=$max*$clonlLen;
		$sum='';$all=0;$ii=0;
		
		for($i=0;$i<=(int)$this->clonArr[$a]&&($i<$ma||!$max);$i++){
			$Html.=$clusterRow.'<tr>'.$clusterName;
			if($i)$this->getFilds($this->arrId[$a][0],$this->arrId[$a][1],$this->arrId[$a][2]);
			
			for($ii=0;$ii<$clonlLen;$ii++){
				$Html.=$clusterCell.'<td>'.$this->cloncells[$ii+$i*$clonlLen]['val'].'</td>';
				$sum+=$this->cloncells[$ii+$i*$clonlLen]['add'];
			}
			
			if($this->arrId[$a][1]){
				$sum=eval("return ".$sum.";");
				$all+=$sum;
				$Html.=$clusterCell.'<td>'.$sum.'</td>';
			}
			$Html.='</tr>';
			$sum='';
		}
		
		$Html.=$clusterHtmlEnd;
		$this->clonArr[$a]=0;
		$this->isCluster=FALSE;
		$GLOBALS['qfSum'].=';+'.$all;
		
		return $Html;
	}
	
	function getCheckbox($pat, $post, $c) 
	{
		$add='';static $i=0;
		
		preg_match('/([^"]+)(?=" class="inp_ch")/', $pat, $m);
		
		$calc='';
		$vals=explode('input',$pat);
		
//		if($c){
			foreach($vals as $val){
				if(strpos($val,'opt_modifer'))$calc.=$val{strpos($val,'value="')+7};
			}
			
			preg_match('/([^"]+)(?=" class="opt_price)/', $pat, $o);
			$calc.=trim($o[0]);
//		}
		
		if($i.'_'.$calc==$post['chbx'][$i]){
			$value=JText::_('JYES');
			if($c) {
				$add=$calc;
				if(!$this->isCluster)$GLOBALS['qfSum'].=';'.$calc;
			}
		}
		else $value=JText::_('JNO');
		
		$i++;
		return $this->getTr($m[0],$value,$add,$c);
	}
	
	function getEmail($pat,$post,$c) 
	{
		static $i=0;
		
		preg_match('/([^"]+)(?=" class="inp_sel")/', $pat, $m);
		$html= $this->getTr($m[0],$post['email'][$i],'',$c);
		$i++;
		return $html;
	}
	
	function getRadio($pat, $post, $c, $reqid) 
	{
		static $a=array();$add='';$k=0;$value='';
		if(!isset($a[$reqid]))$a[$reqid]=1;
		
		preg_match('/([^"]+)(?=" class="inp_sel")/', $pat, $m);
		$title=$m[0];
		
		$opts=explode('</div><div>',$pat);
		$name='r'.$reqid.'_'.$a[$reqid]; $a[$reqid]++;
		
		foreach($opts as $opt) {
			$calc='';
			$vals=explode('input',$opt);
			
//			if($c){
				foreach($vals as $val){
					if(strpos($val,'opt_modifer'))$calc.=$val{strpos($val,'value="')+7};
				}
				
				preg_match('/([^"]+)(?=" class="opt_price)/', $opt, $o);
				$calc.=trim($o[0]);
//			}
		
			if($k.'_'.$calc==$post[$name]){
				preg_match('/([^"]+)(?=" class="inp_opt")/', $opt, $m);
				$value=$m[0];
				if($c) {
					$add=$calc;
					if(!$this->isCluster)$GLOBALS['qfSum'].=';'.$calc;
				}
				preg_match('/([^"]+)(?=" class="inp_svz")/', $opt, $m);
				if($m)$svz=(int)$m[0];
			}
			$k++;
		}
		$html = $this->getTr($title,$value,$add,$c);
		if($svz)$html.=$this->getChildForm($svz);
		
		return $html;
	}
	
	function getText($pat,$post,$c) 
	{
		static $i=0;
		
		preg_match('/([^"]+)(?=" class="inp_sel")/', $pat, $m);
		$html= $this->getTr($m[0],$post['qftext'][$i],'',$c);
		$i++;
		return $html;
	}
	
	function getCalctext($pat,$post,$c) 
	{
		static $i=0;$add='';
		
		preg_match('/([^"]+)(?=" class="inp_ch")/', $pat, $m);
		
		$calc='';
		$vals=explode('input',$pat);
		
//		if($c){
			foreach($vals as $val){
				if(strpos($val,'opt_modifer'))$calc.=$val{strpos($val,'value="')+7};
			}
			
			preg_match('/([^"]+)(?=" class="opt_price2)/', $pat, $o);
			$calc.=trim($o[0]);
//		}
		
		if($calc==$post['qfchtext'][$i]){
			if($c) {
				$add=$calc{0}.'('.trim($o[0]).'*'.$post['qfctext'][$i].')';
				if(!$this->isCluster)$GLOBALS['qfSum'].=';'.$calc{0}.trim($o[0])*$post['qfctext'][$i];
			}
		}

		$html= $this->getTr($m[0],$post['qfctext'][$i],$add,$c);
		$i++;
		return $html;
	}
	
	function getTextarea($pat,$post,$c) 
	{
		static $i=0;
		
		preg_match('/([^"]+)(?=" class="inp_sel")/', $pat, $m);
		$html= $this->getTr($m[0],$post['qftextarea'][$i],'',$c);
		$i++;
		return $html;
	}
	
	function getFile($pat,$post,$c) 
	{
		static $i=0;
		
		preg_match('/([^"]+)(?=" class="inp_sel")/', $pat, $m);
		$html= $this->getTr($m[0],$_FILES['qffile']['name'][$i],'',$c);
		$i++;
		return $html;
	}
	
	function getip()
	{
		if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"),"unknown"))
			$ip = getenv("HTTP_CLIENT_IP");
	
		elseif (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
			$ip = getenv("HTTP_X_FORWARDED_FOR");
	
		elseif (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
			$ip = getenv("REMOTE_ADDR");
	
		elseif (!empty($_SERVER['REMOTE_ADDR']) && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
			$ip = $_SERVER['REMOTE_ADDR'];
		
		else
			$ip = "unknown";
		
		return($ip);
	}
	
	static function clasterreplace($str,$c ){
		$str2='';
		$arr=explode('_cLrow_',$str);
		array_splice($arr, 0, 1);
		foreach($arr as $ar){
			$ar2=explode('_cLcell_',$ar);
//			if(!$c)array_splice($ar2, -1, 1);
			array_splice($ar2, 0, 1);
			$arr3=array();
			foreach($ar2 as $ar22){
				$arr3[]='"'.str_replace(array('<td>','</td>','</tr>'),'',$ar22).'"';
			}
			$str2.=implode(',',$arr3)."<br/>";
		}
		
		return str_replace(',""<br/>','<br/>',$str2);
	}
	
	function getTr($title,$value,$add,$c='') 
	{
//		$add=strlen($add)>1?$add:'';
		static $i=0;
//		!isset($this->len)?$this->len=-1:$this->len++;
		if($this->isCluster){
			$this->cloncells[$i]['label']=$title;
			$this->cloncells[$i]['val']=$value;
			$this->cloncells[$i]['add']=$add;
			$i++;
		}
		else{
			
		$i=0;$this->cloncells=array();
		$c=($c)?'<td style="padding:0 10px; width:5%;">'.$add.'</td>':'';
		
		return '<tr><td style="padding: 0 10px;width:180px;">'.$title.'</td><td style="padding: 0 10px;">'.$value.'</td>'.$c.'</tr>';}
	}

}

