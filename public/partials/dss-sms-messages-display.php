<?php 
if ( isset($_GET['success-registration']) ) {
	$notice = '<h2>Congratulations, you are successfully registered!</h2>';
}
$user_id = get_current_user_id();
$sms_limit = get_user_meta( $user_id, 'dss_qty_messages', true );
if ( !(int)$sms_limit ) {
	$sms_limit = get_user_meta( $user_id, 'dss_qty_paid_messages', true );
}

?>
<div class="dashboard-menu-container">
	<div class="dss-account-menu-select-mob">Messages<i class="fas fa-chevron-right"></i></div>
	<div class="dss-account-menu-select">Messages<i class="fas fa-chevron-right"></i></div>
	<ul>
		<li><a href="/my-account/crs-orders/">CRS Orders</a></li>
		<li><a href="/sms-service/">New SMS</a></li>
		<li><a href="/custom-service/">New CRS</a></li>
		<li><a href="/my-payment/">My Payment</a></li>
		<li><a href="/edit-profile/'">Edit Profile</a></li>
	</ul>
</div>

<div class="dss-account-notification">
	<div class="dss-register-notice"><?php echo $notice; ?></div>
	<div><?php echo "SMS available: {$sms_limit}"; ?></div>
</div>

<?php
if( !$sms_arr ) {
	echo '<div class="dss-empty-orders">
		<h3>You have no Orders yet.</h3>
	</div>';

	return;
}
?>
<div class="sms-mobile-orders-container">
	
<?php foreach ( $sms_arr as $key => $sms ) : ?>
	<?php
	$order_date = strtotime( $sms_data[$key]['order_date'] );
	$delivery_date = $sms_data[$key]['process_date'];
	$count = '';
	if ( ($recipients[$key] - 1) > 0 ) {			
		$count = $recipients[$key] - 1;
		$count = "+{$count}";
	}	
	$color = Dropseal_Services_Public::get_status_color( $sms_data[$key]['status'] );
	?>
	<div class="dss-mobile-orders-wrap">
		<div class="dss-orders-row">
			<div><?php echo date("Y-m-d H:i", $order_date); ?></div>	
			<div><?php echo '<span class="dss-count-recipients">'.$count.'</span>';
				echo $sms->recipient_1->name; ?>	
			</div>
		</div>
		<div class="dss-orders-row">
			<div><?php echo date("Y-m-d H:i", $delivery_date); ?></div>
			<div style="color: <?php echo $color; ?>"><?php echo $sms_data[$key]['status']; ?></div>
		</div>
		<div class="dss-orders-row">
			<div>
			<?php if ( $sms_data[$key]['status'] == 'cancelled' || $sms_data[$key]['status'] == 'completed' ) : ?>			
				<a href="/edit-sms/?id=<?php echo $sms_data[$key]['id']; ?>">
					<img class="dss-eye" src="<?php echo dirname(plugin_dir_url( __FILE__ )) . '/img/eye.svg'; ?>">
				</a>
			<?php else : ?>
				<a href="/edit-sms/?id=<?php echo $sms_data[$key]['id']; ?>">
					<img src="<?php echo dirname(plugin_dir_url( __FILE__ )) . '/img/edit.svg'; ?>">
				</a>
			<?php endif; ?>	
			</div>
			<div>
				<img class="order-delete" data-service="sms" data-id="<?php echo $sms_data[$key]['id']; ?>"src="<?php echo dirname(plugin_dir_url( __FILE__ )) . '/img/delete.svg'; ?>">
			</div>
		</div>
	</div>
<?php endforeach; ?>

</div>


<table class="sms-orders-table">
  	<thead>
    <tr>
    	<th>Order</th>
		<th>Date Of Create</th>
		<th>Name</th>
		<th></th>
		<th>Delivery Date</th>
		<th>Status</th>
		<th>Edit</th>
		<th>Delete</th>
    </tr>
  	</thead>
  	<tbody>
    <?php foreach ( $sms_arr as $key => $sms ): ?>
    	<?php
    	$count = '';
    	if ( ($recipients[$key] - 1) > 0 ) {			
			$count = $recipients[$key] - 1;
			$count = "+{$count}";
		}	
    	$order_date = strtotime( $sms_data[$key]['order_date'] );
    	$delivery_date = $sms_data[$key]['process_date'];
    	$color = Dropseal_Services_Public::get_status_color( $sms_data[$key]['status'] );
    	?>
    <tr>
    	<td><?php echo $sms_data[$key]['id']; ?></td>
		<td><?php echo date("Y-m-d H:i", $order_date); ?></td>			
		<td><?php echo $sms->recipient_1->name; ?></td>
		<td><?php echo '<span class="dss-count-recipients">'.$count.'</span>'; ?></td>
		<td><?php echo date("Y-m-d H:i", $delivery_date); ?></td>
		<td style="color: <?php echo $color; ?>"><?php echo $sms_data[$key]['status']; ?></td>
		<td>
		<?php if ( $sms_data[$key]['status'] == 'cancelled' || $sms_data[$key]['status'] == 'completed' ) : ?>			
			<a href="/edit-sms/?id=<?php echo $sms_data[$key]['id']; ?>">
				<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
					 width="25px" height="25px" viewBox="0 0 497.25 497.25" style="fill: #191970"
					 xml:space="preserve">
				<g>
					<g>
						<circle cx="248.625" cy="248.625" r="19.125"/>
						<path d="M248.625,172.125c-42.075,0-76.5,34.425-76.5,76.5s34.425,76.5,76.5,76.5s76.5-34.425,76.5-76.5
							S290.7,172.125,248.625,172.125z M248.625,306c-32.513,0-57.375-24.862-57.375-57.375s24.862-57.375,57.375-57.375
							S306,216.112,306,248.625S281.138,306,248.625,306z"/>
						<path d="M248.625,114.75C76.5,114.75,0,248.625,0,248.625S76.5,382.5,248.625,382.5S497.25,248.625,497.25,248.625
							S420.75,114.75,248.625,114.75z M248.625,363.375c-153,0-225.675-114.75-225.675-114.75s72.675-114.75,225.675-114.75
							S474.3,248.625,474.3,248.625S401.625,363.375,248.625,363.375z"/>
					</g>
				</g>
				</svg>
			</a>
		<?php else : ?>
			<a href="/edit-sms/?id=<?php echo $sms_data[$key]['id']; ?>">
				<img src="<?php echo dirname(plugin_dir_url( __FILE__ )) . '/img/edit.svg'; ?>">
			</a>
		<?php endif; ?>					
		</td>
		<td><img class="order-delete" data-service="sms" data-id="<?php echo $sms_data[$key]['id']; ?>"src="<?php echo dirname(plugin_dir_url( __FILE__ )) . '/img/delete.svg'; ?>">
		</td>
    </tr>
	<?php endforeach; ?>
  </tbody>
</table>
<?php ?>