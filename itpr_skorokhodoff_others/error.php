<?php
/**
 * @package     Joomla.Site
 * @subpackage  Template.system
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
if (!isset($this->error))
{
	$this->error = JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
	$this->debug = false;
}
//get language and direction
$doc = JFactory::getDocument();
$this->language = $doc->language;
$this->direction = $doc->direction;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php echo $this->title; ?> <?php echo htmlspecialchars($this->error->getMessage()); ?></title>
	<?php
		$debug = JFactory::getConfig()->get('debug_lang');
		if (JDEBUG || $debug)
		{
	?>
		<link rel="stylesheet" href="<?php echo $this->baseurl ?>/media/cms/css/debug.css" type="text/css" />
	<?php
		}
	?>

<!-- template code start  -->

<link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:300,400,700&subset=latin,cyrillic-ext' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="<?php echo $this->baseurl ?>/templates/itpr_skorokhodoff/css/default.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/css/template.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->baseurl ?>/templates/itpr_skorokhodoff/css/error_default.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/css/error.css" />
<!--[if lt IE 10]>
<link rel="stylesheet" type="text/css" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/css/ie9.css" />	
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
			<div id="phone">
				<?php
				// Display position-0 modules
				echo $doc->getBuffer('modules', 'phone', array('style' => 'none'));
				?>
			</div>
			<div id="office"><a href="/">личный кабинет</a></div>
		</div>
	</div>
	<div id="top" class="roboto">
		<?php
		// Display position-0 modules
		echo $doc->getBuffer('modules', 'topmenu', array('style' => 'none'));
		?>
	</div>
	<div id="content">
		<div id="shadow"></div>
		<div class="section-body">
			<div id="mainbody">
				<?php
				// Display position-0 modules
				echo $doc->getBuffer('modules', 'status', array('style' => 'xhtml'));
				?>
				
				<div class="content">
					<!-- Begin Content -->
					<h1 class="page-header"><?php echo JText::_('JERROR_LAYOUT_PAGE_NOT_FOUND'); ?></h1>
					<div class="well">
						<div class="row-fluid">
							<div class="span6">
								<p><strong><?php echo JText::_('JERROR_LAYOUT_ERROR_HAS_OCCURRED_WHILE_PROCESSING_YOUR_REQUEST'); ?></strong></p>
								<p><?php echo JText::_('JERROR_LAYOUT_NOT_ABLE_TO_VISIT'); ?></p>
								<ul>
									<li><?php echo JText::_('JERROR_LAYOUT_AN_OUT_OF_DATE_BOOKMARK_FAVOURITE'); ?></li>
									<li><?php echo JText::_('JERROR_LAYOUT_MIS_TYPED_ADDRESS'); ?></li>
									<li><?php echo JText::_('JERROR_LAYOUT_SEARCH_ENGINE_OUT_OF_DATE_LISTING'); ?></li>
									<li><?php echo JText::_('JERROR_LAYOUT_YOU_HAVE_NO_ACCESS_TO_THIS_PAGE'); ?></li>
								</ul>
							</div>
							<div class="span6">
								<?php if (JModuleHelper::getModule('search')) : ?>
									<p><strong><?php echo JText::_('JERROR_LAYOUT_SEARCH'); ?></strong></p>
									<p><?php echo JText::_('JERROR_LAYOUT_SEARCH_PAGE'); ?></p>
									<?php echo $doc->getBuffer('module', 'search'); ?>
								<?php endif; ?>
								<p><?php echo JText::_('JERROR_LAYOUT_GO_TO_THE_HOME_PAGE'); ?></p>
								<p><a href="<?php echo $this->baseurl; ?>/" class="btn"><i class="icon-home"></i> <?php echo JText::_('JERROR_LAYOUT_HOME_PAGE'); ?></a></p>
							</div>
						</div>
						<hr />
						<p><?php echo JText::_('JERROR_LAYOUT_PLEASE_CONTACT_THE_SYSTEM_ADMINISTRATOR'); ?></p>
						<blockquote>
							<span class="label label-inverse"><?php echo $this->error->getCode(); ?></span> <?php echo $this->error->getMessage();?>
						</blockquote>
					</div>
					<!-- End Content -->
				</div>
					
				<div class="clear footer"></div>
			</div>
		</div>
	</div>
</div>

<div id="footer">
	<div class="section-body">
		<div id="copyright">
			<?php
			// Display position-0 modules
			echo $doc->getBuffer('modules', 'copyright', array('style' => 'none'));
			?>
		</div>
		<div id="icons-sns"><img src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/images/icons-sns.png" width="150" height="40" /></div>
	</div>	
</div>


</body>
</html>