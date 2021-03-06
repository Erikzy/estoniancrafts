<?php
/**
 * Dokan Dashboard Template
 *
 * Dokan Dashboard Order Main Content Template
 *
 * @since 2.4
 *
 * @package dokan
 */
?>
<div class="dokan-order-filter-serach">
    <form action="" method="GET" class="dokan-left">
        <div class="dokan-form-group">
            <label for="from"><?php _e( 'Date:', 'dokan' ) ?></label> <input type="text" class="datepicker" name="order_date" id="order_date_filter" value="<?php echo isset( $_GET['order_date'] ) ? sanitize_key( $_GET['order_date'] ) : ''; ?>">
            <input type="submit" name="dokan_order_filter" class="dokan-btn dokan-btn-sm dokan-btn-danger dokan-btn-theme f-top smaller-orange-button"  value="<?php esc_attr_e( 'Filter', 'dokan' ); ?>">
            <input type="hidden" name="order_status" value="<?php echo isset( $_GET['order_status'] ) ? sanitize_key( $_GET['order_status'] ) : 'all'; ?>">
        </div>
    </form>

<!--     <form action="" method="POST" class="dokan-right">
        <?php
        $slug = get_option('_csv_import_export_page_path');
        if ($slug) : ?>
            <a href="<?= get_option('home').'/'.$slug ?>" target="_blank"><?= __('CSV import/export', 'ktt') ?></a>
        <?php endif; ?>
        <div class="dokan-form-group">
            <input type="submit" name="dokan_order_export_all"  class="dokan-btn dokan-btn-sm dokan-btn-danger dokan-btn-theme   smaller-gray-button m10 " value="<?php esc_attr_e( 'Export All', 'dokan' ); ?>">
            <input type="submit" name="dokan_order_export_filtered"  class="dokan-btn dokan-btn-sm dokan-btn-danger dokan-btn-theme  smaller-gray-button m10" value="<?php esc_attr_e( 'Export Filtered', 'dokan' ); ?>">
            <input type="hidden" name="order_date" value="<?php echo isset( $_GET['order_date'] ) ? sanitize_key( $_GET['order_date'] ) : ''; ?>">
            <input type="hidden" name="order_status" value="<?php echo isset( $_GET['order_status'] ) ? sanitize_key( $_GET['order_status'] ) : 'all'; ?>">
        </div>
    </form> -->

    <div class="dokan-clearfix"></div>
</div>
