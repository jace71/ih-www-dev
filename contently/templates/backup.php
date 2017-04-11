<div class="wrap">
    <h2><?php _e( 'Contently Backup', 'contently' ); ?></h2>
    <?php settings_errors( 'contently_admin_notice' ); ?>
    <div class="card">
        <form action="<?php echo admin_url( 'admin.php?page=contently_backup' ); ?>" method="POST" enctype="multipart/form-data">
            <h3><?php _e( 'Import Settings', 'contently' ); ?></h3>
            <p><?php _e( 'Select the Contently JSON file you would like to import.' ); ?></p>
            <p><?php _e( 'When you click the import button below, Contently will import settings from file.' ); ?></p>
            <table class="form-table">
                <tbody>
                    <tr>
                        <td>
                            <input type="file" name="contently_import_file">
                        </td>
                        <td>
                            <button type="submit" class="button button-primary"><?php _e( 'Upload file and import', 'contently' ); ?></button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <input type="hidden" name="contently_nonce" value="<?php echo wp_create_nonce( 'import' ) ?>">
        </form>
    </div>
    <div class="card">
        <h3><?php _e( 'Export Settings', 'contently' ); ?></h3>
        <p><?php _e( 'When you click the button below Contently will create an JSON file for you to save to your computer.', 'contently' ); ?></p>
        <p><?php _e( 'This format, will contain your Contently integrations such as: API keys, publishing settings, mapping fields.', 'contently' ); ?></p>
        <p><?php _e( 'Once you`ve saved the download file, you can use the Import function to import or restore Contently settings.', 'contently' ); ?></p>
        <p class="submit">
            <a class="button button-primary" href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=contently_backup' ), 'export', 'contently_nonce' ); ?>">
                <?php _e( 'Download Export File', 'contently' ); ?>
            </a>
        </p>
    </div>
</div>