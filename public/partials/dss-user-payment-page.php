<?php ?>

<div class="dashboard-menu-container">
	<div class="dss-account-menu-select-mob">My Payment<i class="fas fa-chevron-right"></i></div>
	<div class="dss-account-menu-select">My Payment<i class="fas fa-chevron-right"></i></div>
	<ul>
		<li><a href="/my-account/">Messages</a></li>
		<li><a href="/sms-service/">New SMS</a></li>
		<li><a href="/custom-service/">New CRS</a></li>
		<li><a href="/my-payment/">My Payment</a></li>
		<li><a href="/edit-profile/'">Edit Profile</a></li>
	</ul>
</div>
<div class="dss-notification-container"></div>
<?php
$sms_meta = Dropseal_Services_Public::get_payment_orders_by_user('sms', 'paypal_id');
$crs_meta = Dropseal_Services_Public::get_payment_orders_by_user('crs', 'paypal_id');

$order_ids = array_merge($sms_meta, $crs_meta);
rsort($order_ids);

if ( !$order_ids ) {
	echo '<div class="dss-empty-orders">
		<h3>You have no Orders yet.</h3>
	</div>';

	return;
}
?>

<div class="dss-mobile-payment-orders-container">
<?php foreach ( $order_ids as $order ) : ?>
	<div class="dss-mobile-payment-orders-wrap">
		<div class="dss-orders-payment-row">
			<div><?php echo get_post_meta( $order['meta_value'], '_txn_id', true ); ?></div>
			<div><?php echo get_post_meta( $order['meta_value'], '_mc_gross', true ); ?></div>
		</div>
		<div class="dss-orders-payment-row">
			<div><?php echo get_the_date( 'M j, Y, g:i A', $order['meta_value'] )?></div>
			<div><?php echo 'PayPal'; ?></div>
		</div>
	</div>
<?php endforeach; ?>
</div>

<table class="dss-payment-orders-table">
  	<thead>
    <tr>
		<th>Order</th>
		<th>Transaction ID</th>
		<th>Total</th>
		<th>Payment date</th>
		<th>Payment system</th>
    </tr>
  	</thead>
  	<tbody>
<?php foreach ( $order_ids as $order ) : ?>
    <tr>
		<td><?php echo $order['meta_value']; ?></td>			
		<td><?php echo get_post_meta( $order['meta_value'], '_txn_id', true ); ?></td>
		<td><?php echo get_post_meta( $order['meta_value'], '_mc_gross', true ); ?></td>
		<td><?php echo get_the_date( 'M j, Y, g:i A', $order['meta_value'] )?></td>
		<td><?php echo 'PayPal'; ?></td>
		<td>
		</td>
		<td>
		</td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>

<?php ?>