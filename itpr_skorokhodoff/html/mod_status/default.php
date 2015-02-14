<?php
// No direct access
defined( '_JEXEC' ) or die;
?>
<div class="module<?php echo $moduleclass_sfx; ?>">
<p>Узнать статус доставки вашего груза по номеру накладной (состоит из 6 цифр и указан в заказе).</p>

<form method="post" action="/status">
<input name="view" value="name4" type="hidden">
<input name="option" value="com_status" type="hidden">
<input name="id" type="text" >
<button class="roboto">узнать</button>
</form>

</div>