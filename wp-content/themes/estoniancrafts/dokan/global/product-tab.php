<?php
/**
 * Dokan Seller Single product tab Template
 *
 * @since 2.4
 *
 * @package dokan
 */
?>

<table class="shop_attributes">
    <tbody>
    <?php if ( !empty( $store_info['store_name'] ) ) { ?>
        <tr class="store-name">
            <th><?php _e( 'Store Name:', 'dokan' ); ?></th>
            <td>
                <?php echo esc_html( $store_info['store_name'] ); ?>
            </td>
        </tr>
    <?php } ?>

    <tr class="seller-name">
        <th>
            <?php _e( 'Seller:', 'dokan' ); ?>
        </th>

        <td>
            <?php printf( '<a href="%s">%s</a>', ec_dokan_get_store_url( $author->ID ), $author->display_name ); ?>
        </td>
    </tr>
    <?php if ( !empty( $store_info['address'] ) ) { ?>
        <tr class="store-address">
            <th><b><?php _e( 'Address:', 'dokan' ); ?></b></th>
            <td>
                <?php echo dokan_get_seller_address( $author->ID ) ?>
            </td>
        </tr>
    <?php } ?>

    <tr class="">
        <td colspan="2">
            <?php dokan_get_readable_seller_rating( $author->ID ); ?>
        </td>
    </tr>

    </tbody>
</table>
