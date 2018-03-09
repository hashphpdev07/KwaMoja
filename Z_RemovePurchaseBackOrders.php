<?php
/* $Id: Z_RemovePurchaseBackOrders.php 7751 2017-04-13 16:34:26Z rchacon $*/

include ('includes/session.php');
$Title = _('Remove Purchase Order Back Orders');
include ('includes/header.php');

echo '<form method="post" action="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '">';
echo '<input type="hidden" name="FormID" value="', $_SESSION['FormID'], '" />';

echo '<br /><div class="page_help_text">', _('This will alter all purchase orders where the quantity required is more than the quantity delivered - where some has been delivered already. The quantity ordered will be reduced to the same as the quantity already delivered - removing all back orders'), '
	</div>
	<div class="centre">
		<input type="submit" name="RemovePOBackOrders" value="', _('Remove Purchase Back Orders'), '" />
	</div>
</form>';

if (isset($_POST['RemovePOBackOrders'])) {
	DB_query("UPDATE purchorderdetails
				SET quantityord=quantityrecd
				WHERE quantityrecd>0
					AND quantityord > quantityrecd
					AND deliverydate < CURRENT_DATE");
	prnMsg(_('Updated all purchase orders to remove back orders'), 'success');
}

include ('includes/footer.php');
?>