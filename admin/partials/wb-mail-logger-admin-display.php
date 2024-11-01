<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://profiles.wordpress.org/webbuilder143/
 * @since      1.0.0
 *
 * @package    Wb_Mail_Logger
 * @subpackage Wb_Mail_Logger/admin/partials
 */
?>
<div class="wb_mlr_detail_popup">
    <div class="wb_mlr_detail_popup_head">
        <span class="dashicons dashicons-email"></span>&nbsp;<?php _e('Mail content', 'wb-mail-logger'); ?>
        <div class="wb_mlr_detail_popup_close" title="<?php echo esc_attr(__('Close', 'wb-mail-logger')); ?>">X</div>
    </div>
    <div class="wb_mlr_popup_inner">
        
    </div> 
</div>
<div class="wrap wb_mlr">
    <h2><?php _e('Mail log', 'wb-mail-logger'); ?></h2>

    <div class="wb_mlr_action_panel">
        <button class="button button-secondary wb_mlr_delete_bulk" type="button"><?php _e('Delete', 'wb-mail-logger'); ?></button>

        <div class="wb_mlr_search_box">
            <form>
                <input type="text" name="wb_mlr_search" placeholder="<?php echo esc_attr(__('Email, Subject', 'wb-mail-logger')); ?>" value="<?php echo esc_attr($search);?>">
                <input type="hidden" name="page" value="<?php echo esc_attr(WB_MAIL_LOGGER_PLUGIN_NAME);?>">
                <button class="button button-primary" type="submit"><?php _e('Search', 'wb-mail-logger'); ?></button>
            </form>
        </div>
    </div>

    <table class="wp-list-table widefat fixed striped table-view-list wb_mlr_list_table">
        <thead>
            <tr>
                <th style="width:25px;"><input type="checkbox" value="1" class="wb_mlr_bulk_check_main"></th>
                <th><?php _e('Subject', 'wb-mail-logger'); ?></th>
                <th><?php _e('Reciever', 'wb-mail-logger'); ?></th>
                <th><?php _e('Time', 'wb-mail-logger'); ?></th>
                <th><?php _e('Attachments', 'wb-mail-logger'); ?></th>
                <th><?php _e('Errors', 'wb-mail-logger'); ?></th>
                <th><?php _e('Actions', 'wb-mail-logger'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if (count($mail_list) > 0) {
                $site_date_format = get_option('date_format');
                $site_time_format = get_option('time_format');
                foreach ($mail_list as $mail_item) {

                    $id = $mail_item['id_wb_mlr_data'];
                    ?>
                    <tr>
                        <td style="text-align:center;">
                            <input type="checkbox" value="1" class="wb_mlr_bulk_check_sub" data-id="<?php echo esc_attr($id);?>">
                        </td>
                        <td><?php echo esc_html($mail_item['subject']); ?></td>                       
                        <td>
                            <?php 
                            $to_email = maybe_unserialize($mail_item['to_email']);
                            if (is_array($to_email)) {
                                echo esc_html(implode(",", $to_email));
                            }
                            ?>
                        </td>
                        <td>
                            <?php 
                                echo wp_date($site_date_format . ' ' . $site_time_format, $mail_item['sent_date']); 
                            ?>                                   
                        </td>
                        <td>
                            <?php 
                            $attachments = maybe_unserialize($mail_item['attachments']);
                            if (is_array($attachments)) {
                                $attachments = array_filter($attachments);
                                $link_array = array();
                                echo '<ul class="wb_mlr_download_list">';
                                foreach($attachments as $path) {
                                    $file_name = basename($path);
                                    if(strpos($path, '://') === false) { 
                                        $url = content_url($path);
                                    } else {
                                        $url = $path;
                                    }
                                    $link_array[] = '<li><a href="'. esc_attr($url) . '" title="'. esc_attr($file_name) . '" target="_blank">' . esc_html($this->truncate_string_mid($file_name)) . '</a></li>';
                                }
                                echo implode("", $link_array);
                                echo '</ul>';
                            }
                            ?>
                        </td>
                        <td><?php echo esc_html($mail_item['status_msg']); ?></td>
                        <td>
                            <a class="button button-primary wb_mlr_view" data-id="<?php echo esc_attr($id);?>"><?php _e('View', 'wb-mail-logger');?></a>
                            <a class="button button-secondary wb_mlr_delete" data-id="<?php echo esc_attr($id);?>"><?php _e('Delete', 'wb-mail-logger');?></a>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="7" align="center"><?php _e('No logs found.', 'wb-mail-logger'); ?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>

    <div class="wb_mlr_pagination">
            <?php 
            if($offset > 0) {
                $prev_offset = $offset-$limit;
                $prev_offset = ($prev_offset < 0 ? 0 : $prev_offset);
                ?>
                <a class="button button-secondary" href="<?php echo esc_attr('tools.php?page='.WB_MAIL_LOGGER_PLUGIN_NAME.'&offset='.$prev_offset.'&wb_mlr_search='.$search);?>">
                    <?php _e('Previous', 'wb-mail-logger'); ?>   
                </a>
                <?php
            }else {
                ?>
                <a class="button button-secondary wb_mlr_btn_disabled"><?php _e('Previous', 'wb-mail-logger'); ?></a>
                <?php
            }

            $nxt_offset = $offset + $limit;
            if($nxt_offset < $mail_list_count) {
                ?>
                <a class="button button-secondary" href="<?php echo esc_attr('tools.php?page='.WB_MAIL_LOGGER_PLUGIN_NAME.'&offset='.$nxt_offset.'&wb_mlr_search='.$search);?>"><?php _e('Next', 'wb-mail-logger'); ?></a>
                <?php
            }else {
                ?>
                <a class="button button-secondary wb_mlr_btn_disabled"><?php _e('Next', 'wb-mail-logger'); ?></a>
                <?php
            }
            ?>
    </div>

    <div style="float:left; margin-top:25px; width:100%;">
        <div style="float:left; font-weight:bold; font-size:18px; width:100%;"><?php _e('Our free plugins', 'wb-mail-logger'); ?></div>
            <div style="float:left; width:99%; margin-left:1%; margin-top:15px; border:solid 1px #ccc; background:#fff; padding:15px; box-sizing:border-box;">
                <div style="float:left; margin-bottom:0px; width:100%;">
                    <div style="float:left; font-weight:bold; font-size:18px; width:100%;">
                        <a href="https://wordpress.org/plugins/wb-custom-product-tabs-for-woocommerce/" target="_blank" style="text-decoration:none;"><?php _e('Custom Product Tabs For WooCommerce', 'wb-mail-logger'); ?></a>
                    </div>
                    <div style="float:left; font-size:13px; width:100%;">
                        <ul style="list-style:none;">
                            <li>
                                <span style="color:green;" class="dashicons dashicons-yes-alt"></span> <?php _e('Add unlimited number of custom product tabs to WooCommerce products.', 'wb-mail-logger');?>
                            </li>
                            <li>
                                <span style="color:green;" class="dashicons dashicons-yes-alt"></span> <?php _e('Use Global tab option to add Product tabs to Product by Category/Tags.', 'wb-mail-logger');?>
                            </li>
                            <li>
                                <span style="color:green;" class="dashicons dashicons-yes-alt"></span> <?php _e('Tab position re-arrange option.', 'wb-mail-logger');?>
                            </li>
                            <li>
                                <span style="color:green;" class="dashicons dashicons-yes-alt"></span> <?php _e('Shortcode support in tab content.', 'wb-mail-logger');?>
                            </li>
                            <li>
                                <span style="color:green;" class="dashicons dashicons-yes-alt"></span> <?php _e('Filters for developers to alter tab content and position.', 'wb-mail-logger');?>
                            </li>
                        </ul>
                        <a href="https://wordpress.org/plugins/wb-custom-product-tabs-for-woocommerce/" target="_blank" class="button button-primary"><?php _e('Get the plugin now', 'wb-mail-logger');?></a>
                    </div>
                </div>
            </div>
    </div>
</div>