<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_custom
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

  <link rel="stylesheet" href="http://xn--d1almaabkf8aag.xn--p1ai/media/ext_tss/assets/css/style.css" type="text/css" />
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js" type="text/javascript"></script>
  <script src="http://xn--d1almaabkf8aag.xn--p1ai/media/ext_tss/assets/js/script.js" type="text/javascript"></script>
  <script type="text/javascript">
var sliders_nfa = 0;
  </script>

<div class="custom<?php echo $moduleclass_sfx ?>" <?php if ($params->get('backgroundimage')) : ?> style="background-image:url(<?php echo $params->get('backgroundimage');?>)"<?php endif;?> >
	<?php echo $module->content;?>
</div>
