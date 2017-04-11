<?php
$options     = get_option( 'cl_options', array( 'debug_state' => true ) );
$debug_state = filter_var( $options['debug_state'], FILTER_VALIDATE_BOOLEAN );
?>

<div>
	<input type="hidden" name="api_key" value="<?php echo ( $api_key != 'api_key' ) ? $api_key : ''; ?>">
	<input type="hidden" name="action" value="set_profile_api_key">
	<div class="cl-row">
		<div class="cl-left cl-label">
			<span><?php echo _e( 'API key', 'contently' ) ?></span>
		</div>
		<div class="cl-left">
			<input value="<?php echo ( $api_key != 'api_key' ) ? $api_key : ''; ?>" type="text" name="api_key_new" style="width: 300px;"/>
		</div>
	</div>

	<div class="cl-row">
		<input style="margin-left: 20px;" id="debug-mode" type="checkbox" <?php echo ( $debug_state ) ? 'checked' : '' ?>>
		<span style="padding-left: 5px;" for="debug-mode">Allow Contently access to debug data</span>
		<a style="margin-left: 10px;" class="cm-popup" data-block="what-i-am-sharing">(See what I am sharing)</a>
	</div>
</div>

<script type="text/html" id="what-i-am-sharing">
	<div class="b-popup cl_inside">
		<div class="b-popup-content">
			<h3>What I am sharing?</h3>
			<p>
				This option provides an access to the information about the plugin's work for developers.
				In case of errors this will help to better determine and resolve the issue.
			</p>
			<p>
				The following information will be displayed:
			<ul>
				<li>PHP version</li>
				<li>Wordpress version</li>
				<li>The information about used plugins (name, version, source)</li>
				<li>The log of thew plugin's work</li>
			</ul>
			</p>

			<button class="button button-primary button-large" data-action="close">Close</button>
		</div>
	</div>
</script>