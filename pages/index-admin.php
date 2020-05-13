<?php
/**
 * Created by PhpStorm.
 * User: test
 * Date: 17.12.19
 * Time: 20:48
 */
global $post;

$filter_args = array (
  'date_start' => FILTER_SANITIZE_STRING,
  'date_end' => FILTER_SANITIZE_STRING,
  'sectionDSCR' => array('filter'=>FILTER_CALLBACK,'options'=>'plugin_json_decode')
);
$metadata = plugin_get_post_meta($post,$filter_args);
$date_dif = plugin_get_date_diff($metadata);
$date_period = plugin_get_date_period($metadata);
wp_enqueue_script('jquery-ui-datepicker');
wp_register_style('jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css');
wp_enqueue_style('jquery-ui');
wp_enqueue_script('champion',plugins_url() .'/champion_helper/pages/admin_panel.js');
?>
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<section class="config">
<!--step 1-->
    <div class="__select_date__">
        <label for="from">Дата початку змагань:</label>
        <label for="to">Дата завершення змагань:</label>
        <input id="from" type="date" name="date_start" value="<?=esc_textarea( $metadata['date_start'] )?>" class="widefat">
        <input id="to" type="date" name="date_end" value="<?=esc_textarea( $metadata['date_end'] )?>" class="widefat">
    </div>
<!--after user select dates show next fields:-->
<!--if user will select 10.10.10-12.10.10 > we will got 3 times next field-->
    <?php if ($date_dif==0): ?>
        <input type="button" id="controlla_button" class="button button-primary" onclick="change_s()" value="Наступний крок" >
        <script type="text/javascript">
            function change_s(){
                document.getElementById("publish").click();
            }
        </script>
    <?php endif; ?>
<!--select count of sections 0-4-->
<!--where 0 -> this day is weekend-->

<!--step2-->
    <?php if ($date_dif>0): ?>
    <div class="__days_block__" style="display:block">
        <?php foreach ($date_period as $period):
            $len = count($metadata['sectionDSCR'][$period]);
            ?>
            <hr>
        <div class="__dt_period" period="<?=$period?>">
            <label><?=$period?></label>
            <select class="section_comment_block minimal">
                <option<?=($len==1)?' selected':''?>>1</option>
                <option<?=($len==2)?' selected':''?>>2</option>
                <option<?=($len==3)?' selected':''?>>3</option>
                <option<?=($len==4)?' selected':''?>>4</option>
            </select>
            <div class="section_block">
                <!--step3-->
                <!--after you select number of sections youll got times next field-->
                <?php
                if ($metadata['sectionDSCR'][$period]):
                    foreach ($metadata['sectionDSCR'][$period] as $key=>$value):

                        ?>
                <div class="wrapper __section_<?=$key + 1?>__">
                    <span>Потік <?=$key + 1?></span>
                    <select class="minimal" name="<?=implode('_',array('sectionDSCR',$period,$key))?>">
                        <option>Виберіть стать учасників</option>
                        <option value = "male" <?=($value=='male')?' selected':''?>>Чоловіки</option>
                        <option value = "female" <?=($value=='female')?' selected':''?>>Жінки</option>
                    </select>
                </div>
                <?php
                    endforeach;
                    endif;?>
                <!--limit symbols 50-->
            </div>
        </div>
        <?php $len = 0; endforeach; ?>
    </div>
    <?php endif; ?>
</section>
