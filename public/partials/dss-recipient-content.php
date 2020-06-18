<?php ?>

<div class="dss-recipient-content">
	<div><?php if ( $meta['content'] ) : ?>
		<h2>Message text:</h2>
		<?php echo $meta['content']; ?>
		<?php endif; ?>
	</div>
	<?php if ( $meta['file_path'] ) : ?>
		<h2>Attachment:</h2>
		<div>
	        <a href="<?php echo plugins_url( strstr( $meta['file_path'], 'dropseal-services') ); ?>" target="_blank"><?php echo basename( $meta['file_path'] ); ?></a> 
		</div>
	<?php endif; ?>
	<?php if ( $meta['links'][0] ) : ?>
		<h2>Links:</h2>
		<div>
			<?php foreach ($meta['links'] as $v) : ?>
				<div><?php echo $v; ?></div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
<?php ?>