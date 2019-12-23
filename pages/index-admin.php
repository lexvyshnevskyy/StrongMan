<?php
/**
 * Created by PhpStorm.
 * User: test
 * Date: 17.12.19
 * Time: 20:48
 */
global $post;
wp_nonce_field( basename( __FILE__ ), 'event_fields' );
$location = get_post_meta( $post->ID, 'location', true );
?>
<section class="config">
<!--step 1-->
    <div class="__select_date__">
        date from:<input type="text" name="" value="<?=esc_textarea( $location )?>" class="widefat">
        date to:<input type="text" name="" value="<?=esc_textarea( $location )?>" class="widefat">
    </div>
<!--after user select dates show next fields:-->
<!--if user will select 10.10.10-12.10.10 > we will got 3 times next field-->
<hr>
<!--select count of sections 0-4-->
<!--where 0 -> this day is weekend-->

<!--step2-->
    <div class="__days_block__">
        <div class="__10.10.10__">
            <label>10.10.10</label>
            number of sections
            <select>
                <option>0</option>
                <option>1</option>
                <option>2</option>
                <option>3</option>
                <option>4</option>
            </select>
            <div >
                <!--step3-->
                <!--after you select number of sections youll got times next field-->
                <div class="__section_1__">section comment <input type="text" name="" value="<?=esc_textarea( $location )?>" class="widefat"></div><!--limit symbols 50-->
            </div>
        </div>
    </div>
</section>