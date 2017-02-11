<h2 class="nav-tab-wrapper get-started-tabs">
	<?php $tasks = Mobiloud_Admin::get_started_tasks();
	array_splice( $tasks, 2 );
	foreach ( $tasks as $task_key => $task ): ?>
		<?php
		$active_task = '';
		if ( ( ! isset( $_GET['tab'] ) && $task_key == 'design' ) || ( isset( $_GET['tab'] ) && $_GET['tab'] == $task_key ) ) {
			$active_task = 'nav-tab-active';
		}
		?>
		<a class="nav-tab <?php echo $active_task; ?>" class="<?php echo $task['class']; ?>"
		   href="<?php echo $task['url']; ?>"><?php echo esc_html( $task['nav_text'] ); ?></a>
	<?php endforeach; ?>
	<a href="<?php echo admin_url( 'admin.php?page=mobiloud_settings' ); ?>" class="tab-button">More Settings</a>
	<a class="tab-button" href="mailto:support@mobiloud.com">Contact Support</a>

</h2>