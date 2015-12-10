<?php

/**
 *
 * PHP version 5
 *
 * Created: 12/7/15, 3:43 PM
 *
 * LICENSE:
 *
 * @author         Jeff Behnke <code@validwebs.com>
 * @copyright  (c) 2015 ValidWebs.com
 *
 * dashboard
 * dashboard.php
 */
?>
	<div class="page-top">
		<h1 class="page-header"><i class="fa fa-tachometer"></i> VVV Dashboard</h1>
		<form class="get-backups" action="" method="get">
			<input type="submit" class="btn btn-danger btn-xs" name="get_backups" value="Backups" />
		</form>
	</div>


	<div class="row">
		<div class="col-sm-12 hosts">
			<?php

			$close = '<a class="close" href="./">Close</a>';

			if ( ! empty( $backups_table ) ) {
				?><h4 class="title">Backups List
				<span class="small"> Path: {VVV}/default/dashboard/dumps/ </span> <?php echo $close; ?></h4><?php
				echo $backups_table;
			}

			if ( ! empty( $plugins ) ) {
				if ( isset( $_GET['host'] ) ) {
					?><h4>The plugin list for
					<span class="red"><?php echo $_GET['host']; ?></span> <?php echo $close; ?></h4><?php

					echo format_table( $plugins, $_GET['host'], 'plugins' );
				}

			}

			if ( ! empty( $themes ) || isset( $_GET['themes'] ) ) {
				if ( isset( $_GET['host'] ) ) {
					?><h4>The theme list for
					<span class="red"><?php echo $_GET['host']; ?></span> <?php echo $close; ?></h4><?php


					if ( isset( $_POST['create_child'] ) && isset( $_POST['child'] ) && ! empty( $_POST['child'] ) ) {

						$themes_array = get_csv_names( $themes );

						$host_info = $vvv_dash->set_host_info( $_POST['host'] );
						$host_path = VVV_WEB_ROOT . '/' . $host_info['host'] . $host_info['path'];
						$child     = strtolower( str_replace( ' ', '_', $_POST['child'] ) );

						if ( in_array( $child, $themes_array ) ) {
							echo vvv_dash_error( 'Error: That theme already exists!' );
						} else {
							$cmd       = 'wp scaffold child-theme ' . $child . ' --theme_name="' . $_POST['theme_name'] . '" --parent_theme=' . $_POST['item'] . ' --path=' . $host_path . ' --debug';
							$new_theme = shell_exec( $cmd );

							if ( $new_theme ) {
								$purge_status = $cache->purge( '-themes' );
								echo vvv_dash_notice( $purge_status . ' files were purged from cache!' );
								echo vvv_dash_notice( $new_theme );
							} else {
								echo vvv_dash_error( '<strong>Error:</strong> Something went wrong!' );
							}
						}
					}

					$host_info = $vvv_dash->set_host_info( $_GET['host'] );
					$themes    = $vvv_dash->get_themes_data( $host_info['host'], $host_info['path'] );

					echo format_table( $themes, $_GET['host'], 'themes' );
				}
			}

			if ( $debug_log_path && file_exists( $debug_log_path ) && ! empty( $debug_log_lines ) ) {

				if ( isset( $_GET['host'] ) ) {
					?><h4>Debug Log for
					<span class="red"><?php echo $_GET['host']; ?></span> <?php echo $close; ?></h4><?php

					?>
					<div class="wp-debug-log">
						<?php echo $debug_log_lines; ?>
					</div>
					<?php
				}
			}

			vvv_dash_xdebug_status();


			include_once 'views/hosts-list.php'; ?>
		</div>
	</div>

<?php include_once 'views/php-error-logs.php';

// End dashboard.php