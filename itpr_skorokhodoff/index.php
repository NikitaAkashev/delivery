<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<jdoc:include type="head" />

<!-- template code start  -->

<link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:300,400,700&subset=latin,cyrillic-ext' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/css/default.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/css/template.css" />
<!--[if lt IE 10]>
<link rel="stylesheet" type="text/css" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/css/default_ie9.css" />	
<![endif]-->

<!-- template code end  -->
</head>
<body>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-52307010-1', 'auto');
  ga('require', 'linkid', 'linkid.js');
  ga('send', 'pageview');

</script>

<div id="container">
	<div id="header" class="roboto">
		<div class="section-body">
			<a id="logo" href="/"><img src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/images/logo.png" width="280" height="33" alt="Скороходофф" /></a>
			<div id="phone"><jdoc:include type="modules" name="phone" style="none" /></div>
			<div id="office"><a href="/">личный кабинет</a></div>
		</div>
	</div>
	<div id="top" class="roboto">
		<jdoc:include type="modules" name="topmenu" style="none" />
	</div>
	<div id="content">
		<div id="shadow"></div>
		<div class="section-body">
			<jdoc:include type="modules" name="calculator" style="xhtml" />
				
			<div id="mainbody">
				<jdoc:include type="modules" name="status" style="xhtml" />
				
				<div class="content">
					<jdoc:include type="message" />
					<jdoc:include type="component" />
				</div>
					
				<div class="clear footer"></div>
			</div>
		</div>
	</div>
</div>

<div id="footer">
	<div class="section-body">
		<div id="copyright"><jdoc:include type="modules" name="copyright" style="none" /></div>
		<div id="icons-sns"><jdoc:include type="modules" name="icons-sns" style="none" /></a>
		</div>
	</div>	
</div>


</body>
</html>