<script type="text/javascript" src="<?php echo plugins_url( '/../js/lib/jquery-1.11.1.min.js', __FILE__ ); ?>"></script>
<script type="text/javascript" src="<?php echo plugins_url( '/../js/lib/underscore-min.js', __FILE__ ); ?>"></script>
<script type="text/javascript" src="<?php echo plugins_url( '/../js/lib/backbone-min.js', __FILE__ ); ?>"></script>

<script type="text/javascript" src="<?php echo plugins_url( '/../js/lib/pretty-json-min.js', __FILE__ ); ?>"></script>
<link rel="stylesheet" href="<?php echo plugins_url( '/../js/lib/pretty-json.css', __FILE__ ) ?>" type="text/css">

<link rel="stylesheet" href="<?php echo plugins_url( '/../css/style.css', __FILE__ ) ?>" type="text/css">
<link rel="stylesheet" href="<?php echo plugins_url( '/../css/debug_result.css?v=0.1', __FILE__ ) ?>" type="text/css">

<div class="menu">
	<ul>
		<li class="item active" data-target="log">Log</li>
		<li class="item" data-target="test">Testing</li>
		<li class="item" data-target="mapping">Mapping</li>
		<li class="item right"><a class="btn" onclick="clear_log()">Clear log</a></li>
	</ul>
</div>

<div class="container test">
	<?php require_once( plugin_dir_path( __FILE__ ) . '/page_testing.php' ) ?>
</div>

<div class="container log">
	<?php if ( is_array( $logData ) ): ?>
		<table class="common" cellpadding="0" cellspacing="0" style="width: 100%">
			<thead>
			<tr>
				<th>Time</th>
				<th>Type</th>
				<th>Title</th>
				<th>Data</th>
			</tr>
			</thead>
			<tbody>
			<?php $process_id = false;

			foreach ( $logData as $row ): ?>

				<?php
				// Fixed out old log.
				if ( ! isset( $row['time'] ) || empty( $row['time'] ) ) {
					continue;
				}

				$is_new_process = false;
				if ( $row['id'] !== $process_id ) {
					$process_id     = $row['id'];
					$is_new_process = true;
				}
				?>

				<tr class="<?php echo ( $is_new_process ) ? 'new_process' : '' ?>">
					<td class="time" valign="top">
						<nobr><?php echo $row['time']; ?></nobr>
					</td>
					<td class="type <?php echo $row['type'] ?>" valign="top"><?php echo $row['type']; ?></td>
					<td class="title" valign="top">
						<nobr><?php echo $row['message']; ?></nobr>
					</td>
					<td>
						<?php if ( ! empty( $row['context'] ) ): ?>
							<table class="parameters" cellpadding="0" cellspacing="0" style="width: 100%;">
								<?php foreach ( $row['context'] as $key => $value ): ?>

									<?php
									$data_type = 'default';
									$key_name  = trim( $key );
									if ( preg_match( '/((?<data_type>.+)!)?__(?<key_name>.+)/im', $key, $regs ) ) {
										$data_type = $regs['data_type'];
										$key_name  = $regs['key_name'];
									}
									?>

									<?php
									$expand_status = false;
									if ( preg_match( '/^_{2}(?<key_name>.+)/i', $key, $regs ) ) {
										// $expand_status = true;
										$key = $regs['key_name'];
									}
									?>
									<tr>
										<td valign="top">
											<nobr>
											<span class="key_name">
												<?php echo $key_name ?>
											</span>

												<span class="action-buttons">
												<?php if ( $data_type === 'json' ): ?>
													<button class="json-toggle-button">+</button>
													<button class="json-toggle-button" style="display: none">-</button>
												<?php endif; ?>
											</span>
											</nobr>
										</td>
										<td valign="top">

											<?php
											$template = false;
											$raw_data = false;
											if ( $data_type === 'json' ) {
												if ( is_string( $value ) ) {
													$value = json_decode( $value, true );
												}

												if ( isset( $value['template'] ) ) {
													$template = $value['template'];
													unset( $value['template'] );
												}

												$data = trim( json_encode( $value, true ), '"' );
											} elseif ( $data_type === 'raw' ) {
												$raw_data = $value;
												$data     = '';
											} else {
												$data = trim( preg_replace( '/[\\\\]/i', '', json_encode( $value ) ), '"' );
											}
											?>

											<div data-type="<?php echo $data_type ?>" class="collapsed <?php echo ( $expand_status ) ? 'hidden' : '' ?>">
												<?php echo $data; ?>
											</div>

											<?php if ( $raw_data ): ?>
												<div>
													<button class="template-toggle-button">Show data</button>
													<button class="template-toggle-button" style="display: none">Hide data</button>

													<div class="template-container" style="display: none;">
														<?php echo $raw_data ?>
													</div>
												</div>
											<?php endif; ?>

											<?php if ( $template ): ?>
												<div>
													<h3>With template:
														<button class="template-toggle-button">Show template</button>
														<button class="template-toggle-button" style="display: none">Hide template</button>
													</h3>

													<div class="template-container" style="display: none;">
														<?php echo $template ?>
													</div>
												</div>
											<?php endif; ?>

										</td>
									</tr>
								<?php endforeach; ?>
							</table>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
</div>

<div class="container mapping">
	<div>
		<!-- Each profiles -->
		<?php foreach ( $profiles as $key_profile => $profile ) { ?>
			<!-- Base info about profile -->
			<h2>Profile: <?php echo $profile['name']; ?></h2>
			<h3>Using mapping type: <?php echo $profile['mapping_type']; ?></h3>
			<?php foreach ( $profile['mapping_array'] as $mapping_type => $mapping ) { ?>
				<hr/>
				<table class="mapping">
					<td colspan="2">
						<h3>Type: <?php echo $mapping_type; ?></h3>
					</td>
					<?php foreach ( $mapping as $group_key => $group ) { ?>
						<?php if ( is_array( $group ) && count( $group ) > 0 ) { ?>
							<tr>
								<td colspan="2">
									<h3>Group: <?php echo $group_key ?></h3>
								</td>
							</tr>
							<?php foreach ( $group as $field_key => $field_name ) { ?>
								<?php if ( ! empty( $field_name ) ) { ?>
									<tr>
										<td valign="top">
											<?php echo $field_key; ?>
										</td>
										<?php if ( $group_key === 'mapping_fields_acf' ) { ?>
											<td valign="top">
												<div data-type="json">
													<?php
														$field = '';
														if ( function_exists('acf_get_field') ) {
															$field = acf_get_field( $field_key );
														}
														else {
															$field = $this->plugins_helper->get_acf_field_by_key( $field_key );
														}
														echo json_encode( $field );
													?>
												</div>
											</td>
										<?php } ?>
										<td valign="top"><?php echo $field_name; ?></td>
									</tr>
								<?php } ?>
							<?php } ?>
						<?php } ?>
					<?php } ?>
				</table>
			<?php } ?>
		<?php } ?>
	</div>
</div>

<script>

	function clear_log() {
		$.ajax({
			url: '?contently_pull=<?php echo $hash_key; ?>=&action=clear_log',
			type: 'GET'
		}).done(function (response) {
			window.location.reload();
		});
	}

	$('.menu').on('click', '.item', function () {
		$(this).closest('ul').find('li').removeClass('active');
		$(this).addClass('active');
		$('.container').hide();
		$('.container' + '.' + $(this).data('target')).show();
	});

	$('.template-toggle-button').click(function () {
		$(this).closest('div').find('.template-toggle-button').toggle();
		$(this).closest('div').find('.template-container').toggle();
	});

	$('div[data-type="json"]').each(function () {
		try {
			var data = JSON.parse($(this).html()),
				collapsed = false;

			var view = new PrettyJSON.view.Node({
				el: $(this),
				data: data
			});

			var meta = view.getMeta();

			$.each(data, function (key, value) {
				if (_.isArray(value) || _.isObject(value)) {
					collapsed = true;
				}
			});

			if (collapsed && meta.size > 0) {
				view.collapseAll();
			} else {
				$(this).closest('tr').find('.json-toggle-button').hide();

			}

			$(this).closest('tr').find('.json-toggle-button').click(function () {
				$(this).closest('tr').find('.json-toggle-button').toggle();
				if (collapsed)
					view.expandAll();
				else {
					view.collapseAll();
				}
				collapsed = !collapsed;
			});

		} catch (e) {
			$(this).closest('tr').find('.json-toggle-button').hide();
			console.warn('parse error', $(this));
		}
	});

</script>