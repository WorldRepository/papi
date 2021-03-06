<div class="wrap">
	<div class="papi-options-logo"></div>
	<h2><?php echo papi()->name; ?></h2>

	<br/>

	<h3>Page types</h3>
	<table class="wp-list-table widefat papi-options-table">
		<thead>
		<tr>
			<th>
				<strong>Name</strong>
			</th>
			<th>
				<strong>Page type</strong>
			</th>
			<th>
				<strong>Template</strong>
			</th>
			<th>
				<strong>Number of pages</strong>
			</th>
		</tr>
		</thead>
		<tbody>
		<?php
		$url        = $_SERVER['REQUEST_URI'];
		$page_types = _papi_get_all_page_types( true );
		foreach ( $page_types as $key => $page_type ) {
			?>
			<tr>
				<td><a href="<?php echo $url; ?>&view=management-page-type&page-type=<?php echo _papi_get_page_type_base_path( $page_type->get_filepath() ); ?>"><?php echo $page_type->name; ?></a></td>
				<td><?php echo $page_type->get_filename(); ?></td>
				<td><?php
					if ( ! current_user_can( 'edit_themes' ) || defined( 'DISALLOW_FILE_EDIT' ) && DISALLOW_FILE_EDIT ) {
						echo $page_type->template;
					} else {
						$theme_dir  = get_template_directory();
						$theme_name = basename( $theme_dir );
						$url        = site_url() . '/wp-admin/theme-editor.php?file=' . $page_type->template . '&theme=' . $theme_name;
						if ( file_exists( $theme_dir . '/' . $page_type->template ) ):
							?>
							<a href="<?php echo $url; ?>"><?php echo $page_type->template; ?></a>
						<?php
						else:
							echo 'Missing';
						endif;
					}
					?></td>
				<td><?php echo _papi_get_number_of_pages( $page_type->get_filename() ); ?></td>
			</tr>
		<?php
		}
		?>
		</tbody>
	</table>
</div>
