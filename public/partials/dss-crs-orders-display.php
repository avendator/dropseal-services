<?php ?>

<div class="dashboard-menu-container">
	<div class="dss-account-menu-select-mob">CRS Orders<i class="fas fa-chevron-right"></i></div>
	<div class="dss-account-menu-select">CRS Orders<i class="fas fa-chevron-right"></i></div>
	<ul>
		<li><a href="/my-account/">Messages</a></li>
		<li><a href="/sms-service/">New SMS</a></li>
		<li><a href="/custom-service/">New CRS</a></li>
		<li><a href="/my-payment/">My Payment</a></li>
		<li><a href="/edit-profile/'">Edit Profile</a></li>
	</ul>
</div>

<?php
if( !$crs_data ) : ?>
	<div class="dss-empty-orders">
		<h3>You have no orders yet.</h3>
	</div>
	<?php return; ?>
<?php endif; ?>

<div class="crs-mobile-orders-container">

<?php foreach ( $crs_meta as $key => $crs ) :
	$order_date = strtotime( $crs_data[$key]['order_date'] );
	$delivery_date = $crs_data[$key]['process_date'];
	$color = Dropseal_Services_Public::get_status_color( $crs_data[$key]['status'] );
	?>		
	<div class="dss-mobile-orders-wrap">
		<div class="dss-orders-row">
			<div><?php echo date("Y-m-d H:i", $order_date); ?></div>	
			<div><?php echo mb_strimwidth($crs->meta_value, 0, 15, '...'); ?></div>
		</div>
		<div class="dss-orders-row">
			<div><?php echo date("Y-m-d H:i", $delivery_date); ?></div>
			<div style="color: <?php echo $color; ?>"><?php echo $crs_data[$key]['status']; ?></div>
		</div>
		<div class="dss-orders-row">
			<div>
			<?php if ( $crs_data[$key]['status'] == 'cancelled' || $crs_data[$key]['status'] == 'completed' ) : ?>			
				<a href="/edit-crs/?id=<?php echo $crs_data[$key]['id']; ?>">
					<img class="dss-eye" src="<?php echo dirname(plugin_dir_url( __FILE__ )) . '/img/eye.svg'; ?>">
				</a>
			<?php else : ?>
				<a href="/edit-crs/?id=<?php echo $crs_data[$key]['id']; ?>">
					<img src="<?php echo dirname(plugin_dir_url( __FILE__ )) . '/img/edit.svg'; ?>">
				</a>
			<?php endif; ?>
			</div>
			<div>
				<img class="order-delete" data-service="crs" data-id="<?php echo $crs_data[$key]['id']; ?>"src="<?php echo dirname(plugin_dir_url( __FILE__ )) . '/img/delete.svg'; ?>">
			</div>
		</div>
	</div>
<?php endforeach; ?>
	
</div>

<table class="crs-orders-table">
  	<thead>
    <tr>
    	<th>Order</th>
		<th>Date Of Create</th>
		<th>Content</th>
		<th>Delivery Date</th>
		<th>Status</th>
		<th>Edit</th>
		<th>Delete</th>
    </tr>
  	</thead>
  	<tbody>
    <?php foreach ( $crs_meta as $key => $crs ):

    	$order_date = strtotime( $crs_data[$key]['order_date'] );
    	$delivery_date = $crs_data[$key]['process_date'];
		$color = Dropseal_Services_Public::get_status_color( $crs_data[$key]['status'] );
    	?>
	    <tr>
	    	<td><?php echo $crs_data[$key]['id']; ?></td>
			<td><?php echo date("Y-m-d H:i", $order_date); ?></td>			
			<td><?php echo mb_strimwidth($crs->meta_value, 0, 15, '...'); ?></td>
			<td><?php echo date("Y-m-d H:i", $delivery_date); ?></td>
			<td style="color: <?php echo $color; ?>"><?php echo $crs_data[$key]['status']; ?></td>
			<td>
			<?php if ( $crs_data[$key]['status'] == 'cancelled' || $crs_data[$key]['status'] == 'completed' ) : ?>			
				<a href="/edit-crs/?id=<?php echo $crs_data[$key]['id']; ?>">
					<img class="dss-eye" src="<?php echo dirname(plugin_dir_url( __FILE__ )) . '/img/eye.svg'; ?>">
				</a>
			<?php else : ?>
				<a href="/edit-crs/?id=<?php echo $crs_data[$key]['id']; ?>">
					<img src="<?php echo dirname(plugin_dir_url( __FILE__ )) . '/img/edit.svg'; ?>">
				</a>
			<?php endif; ?>
			</td>
			<td>
				<img class="order-delete" data-service="crs" data-id="<?php echo $crs_data[$key]['id']; ?>"src="<?php echo dirname(plugin_dir_url( __FILE__ )) . '/img/delete.svg'; ?>">
			</td>
	    </tr>
	<?php endforeach; ?>
  </tbody>
</table>
<?php ?>