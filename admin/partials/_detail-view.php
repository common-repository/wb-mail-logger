<?php

/**
 * Mail data detail view ajax page
 *
 *
 * @link       https://profiles.wordpress.org/webbuilder143/
 * @since      1.0.0
 *
 * @package    Wb_Mail_Logger
 * @subpackage Wb_Mail_Logger/admin/partials
 */
?>
    <div class="wb_mlr_subject">
        <?php
        if(isset($mail_data['subject'])){ 
            echo wp_kses_post($mail_data['subject']);
        }
        ?>
    </div>
    <div class="wb_mlr_content">
        <?php
        if(isset($mail_data['message']))
        {  
            if($mail_data['message'] == strip_tags($mail_data['message']))
            {
               $mail_data['message']=nl2br($mail_data['message']);
            }
            
            echo wp_kses_post($mail_data['message']);
        }
        ?>
    </div>