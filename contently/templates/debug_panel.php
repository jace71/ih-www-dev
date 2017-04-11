<?php
$logger = \Contently\Log::instance();

if ( isset( $_POST['action'] ) ) {
	if ( $_POST['action'] === 'clear_log' ) {
		$logger->remove();
	}
}

$loggerData     = $logger->getData();
$loggerState    = ! ! $logger->getState();
$logger_created = ( $logger->getState() ) ? $logger->hasHandler() : true;
$logger_driver  = $logger->getUsedDriver();

$keys = array_keys( get_option( 'cl_profiles', array() ) );
if ( ! empty( $keys ) ) {
	$hash_key = base64_encode( $keys[0] );
}
?>

<div class="wrap">
	<h2 style="padding-right: 0;">
		<?php echo _e( 'Debug panel', 'contently' ) ?>
		<span style="float: right; font-size: 14px;">Writable: <?php echo ( $loggerState ) ? 'on' : 'off' ?></span>
		<span style="float: right; font-size: 14px; margin-right: 20px;">Storage: <?php echo $logger_driver; ?></span>
	</h2>
	<?php if ( ! $logger_created ) { ?>
		<h3>The log file is not created.</h3>
	<?php } ?>
	<div class="contently-main-settings">
		<div>
			<form method="POST" style="float: right;">
				<input type="hidden" name="page" value="contently_setting"/>
				<input type="hidden" name="debug" value="true"/>
				<input type="hidden" name="action" value="clear_log"/>
			</form>
		</div>

		<iframe style="width: 100%; height:800px;" src="/?contently_pull=<?php echo $hash_key ?>"></iframe>

	</div>
</div>