	<footer role="contentinfo" class="footer">
		<div class="container">
			&copy; <?php echo date( 'Y' ); ?> <?php echo get_bloginfo( 'name' ); ?> |  <?php _e( 'E-mail', 'onepixel' ); ?>: <?php echo get_option( 'admin_email' ); ?> | <a href="#"><?php _e( 'Disclaimer', 'onepixel' ); ?></a>
		</div>
	</footer>
<?php wp_footer(); ?>
</body>
</html>