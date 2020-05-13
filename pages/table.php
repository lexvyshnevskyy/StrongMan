<?php
/**
 * Created by PhpStorm.
 * User: test
 * Date: 23.12.19
 * Time: 19:19
 */
global $post;
$judgeData=judgeClass::getJudgesAssignee($post->ID);

$jsonSTR = array(
    'age' => judgeClass::$category_age,
    'weight' => judgeClass::$category_weight,
    'judge' => judgeClass::$category_judge
);

$filter_args = array (
    'date_start' => FILTER_SANITIZE_STRING,
    'date_end' => FILTER_SANITIZE_STRING,
    'sectionDSCR' => array('filter'=>FILTER_CALLBACK,'options'=>'plugin_json_decode')
);
$metadata = plugin_get_post_meta($post,$filter_args);
$date_dif = plugin_get_date_diff($metadata);
$date_period = plugin_get_date_period($metadata);
?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="<?=plugin_dir_url(__DIR__)?>/pages/app/css/jquery.multiselect.css">
<link rel="stylesheet" href="<?=plugin_dir_url(__DIR__)?>/pages/app/css/jquery-ui.min.css">
<link rel="stylesheet" href="<?=plugin_dir_url(__DIR__)?>/pages/app/css/style.css">

<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="<?=plugin_dir_url(__DIR__)?>/pages/app/js/jquery.multiselect.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<input id="panel-1-ctrl"
       class="panel-radios" type="radio" name="tab-radios" checked>
<input id="panel-2-ctrl"
       class="panel-radios" type="radio" name="tab-radios">
<ul id="tabs-list">
    <!-- MENU TOGGLE -->
    <label id="open-nav-label" for="nav-ctrl"></label>
    <li id="li-for-panel-1">
        <label class="panel-label"
               for="panel-1-ctrl">Список суддів</label>
    </li>
    <li id="li-for-panel-2">
        <label class="panel-label"
               for="panel-2-ctrl">Розподіл роботи суддів</label>
    </li>
</ul>

<article id="panels">
    <div class="container">
        <section id="panel-1">
            <main>
                <section id="judge_list">
                    <table>
                        <thead>
                        <tr>
                            <th>Прізвище ім'я по-батькові</th>
                            <th>Суддівська категорія</th>
                            <th>Місто, область</th>
                            <th>Вартість проїзду</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($judgeData as $judgeID=>$judge):?>
                        <tr id="<?=$judgeID?>">
                            <td id="name"><?=$judge->name?></td>
                            <td id="judge_category"><?=$jsonSTR['judge'][$judge->judge_category]?></td>
                            <td id="location"><?=$judge->location?></td>
<!--                            <td id="weight_category">--><?//=$jsonSTR['weight'][$judge->gender_category][$judge->weight_category]?><!--</td>-->
<!--                            <td id="age_category">--><?//=$jsonSTR['age'][$judge->gender_category][$judge->age_category]?><!--</td>-->
<!--                            <td id="from">--><?//=$judge->from?><!--</td>-->
<!--                            <td id="to">--><?//=$judge->to?><!--</td>-->
                            <td id="comment"><?=$judge->comment?></td>
                        </tr>
                        <?php endforeach;?>
                        </tbody>
                    </table>
                </section>
            </main>
        </section>
        <section id="panel-2">
            <main>
                <button class="export_to_excel" id="<?=$post->ID?>">
                    <img src="https://cdn.zapier.com/storage/services/8913a06feb7556d01285c052e4ad59d0.png" alt="">
                    <span>Перенести в Excel</span>
                </button>
                <section id="main_table">
                    <div class="table">
                        <div class="left-side">
                            <div class="td day">День</div>
                            <div class="td flow">Потоки</div>
                            <div class="td age-category">Вікова категорія</div>
                            <div class="td weight-category">Вагова категорія</div>
                            <?php foreach ($judgeData as $judgeID=>$judge):?>
                            <div class="td judje-field">
                                <span><?=$judge->name?></span>
                                <span class="category"><?=$jsonSTR['judge'][$judge->judge_category]?></span>
                            </div>
                            <?php endforeach;?>
                        </div>
                        <div class="right-side">
                            <?php foreach ($date_period as $el):
                                $len = count($metadata['sectionDSCR'][$el]);
                                if ($len >0):
                            ?>
                            <div class="single-days">
                                <div class="td day">
                                    <?=$el?>
                                </div>
                                <div class="flow">
                                    <?php for ($i=1; $i<=$len; $i++):?>
                                    <span class="td"><?=$i?></span>
                                    <?php endfor; ?>

                                </div>
                                <div class="age-cetogory">
                                    <?php for ($i=0; $i<$len; $i++):
                                        $temp=$metadata['sectionDSCR'][$el][$i];?>
                                    <div class="td" date_id="<?=$el?>" section_id="<?=$i?>">
                                        <select name="" id="" class="ac">
                                            <?php foreach ($jsonSTR['age'][$temp] as $age_id=>$age_el):?>
                                            <option value="<?=$age_id?>"><?=$age_el?></option>
                                            <?php endforeach;?>
                                        </select>
                                    </div>
                                    <?php endfor;?>
                                </div>
                                <div class="weight-category">
                                    <?php for ($i=0; $i<$len; $i++):
                                        $temp=$metadata['sectionDSCR'][$el][$i];?>
                                    <div class="td" date_id="<?=$el?>" section_id="<?=$i?>">
                                        <select name="" id="">
                                            <?php foreach ($jsonSTR['weight'][$temp] as $weight_id=>$weight_el):?>
                                                <option value="<?=$weight_id?>"><?=$weight_el?></option>
                                            <?php endforeach;?>
                                        </select>
                                    </div>
                                    <?php endfor;?>
                                </div>
                                <?php foreach ($judgeData as $judgeID=>$judge):
                                    //var_dump($judge);
                                    $region = preg_split("/[,]+/", $judge->location)[1];
                                    $judge = new helperPluginClass($judge);

                                ?>
                                <div class="judje-field">
                                    <?php for ($i=0; $i<$len; $i++): ?>
                                    <div class="td judje-type-select"
                                         judge_id="<?=$judge->getJudgeID($el)?>"
                                         date_id="<?=$el?>"
                                         section_id="<?=$i?>"
                                         region="<?=$region?>"
                                    >
                                        <?php //var_dump( $judge->assigned["assigned_info"][$el][$i]);?>
                                        <!--<select name="" id="" class="judje-type <?=$judge->getCurrentClass($el,$i)?>" onclick="updateJudgeSelector(this)">-->

                                        <select name="" id="" class="judje-type" onclick="updateJudgeSelector(this)">
                                            <option value="default"></option>
                                            <option value="0" <?=$judge->is_selected($el,$i,0)?>>СтС</option>
                                            <option value="1" <?=$judge->is_selected($el,$i,1)?>>БС</option>
                                            <option value="2" <?=$judge->is_selected($el,$i,2)?>>СУ</option>
                                            <option value="3" <?=$judge->is_selected($el,$i,3)?>>Секр</option>
                                            <option value="4" <?=$judge->is_selected($el,$i,4)?>>ПС</option>
                                            <option value="5" <?=$judge->is_selected($el,$i,5)?>>КС</option>
                                            <option value="6" <?=$judge->is_selected($el,$i,6)?>>ГЖ</option>
                                            <option value="7" <?=$judge->is_selected($el,$i,7)?>>Ж</option>
                                        </select>
                                    </div>
                                    <?php endfor;?>
                                </div>
                                <?php endforeach;?>
                            </div>
                            <?php endif;
                                endforeach; ?>
                        </div>
                    </div>
                </section>
            </main>
        </section>
    </div>
</article>
<script>
    var wpApiSettings =<?=json_encode(array(
		'root' => esc_url_raw( rest_url() ),
		'nonce' => wp_create_nonce( 'wp_rest' )
	))?>,
         all_positions = {'СтС' : 0,'БС' : 1,'СУ' : 2,'Секр' : 3,'ПС' : 4,'КС' : 5,'ГЖ' : 6,'Ж' : 7};
         role_titles = Object.keys(all_positions),
         role_count = {'СтС' : 1,'БС' : 2,'СУ' : 1,'Секр' : 1,'ПС' : 1,'КС' : 1,'ГЖ' : 1,'Ж' : 2};
    $( function() {
        // $('.judje-type').select2();
        var dateFormat = "mm/dd/yy",
            from = $( "#from" )
                .datepicker({
                    defaultDate: "+1w",
                    changeMonth: true,
                    numberOfMonths: 1,
                    dateFormat: "DD, d MM, yy"
                })
                .on( "change", function() {
                    to.datepicker( "option", "minDate", getDate( this ) );
                }),
            to = $( "#to" ).datepicker({
                defaultDate: "+1w",
                changeMonth: true,
                numberOfMonths: 1,
                dateFormat: "DD, d MM, yy"
            })
                .on( "change", function() {
                    from.datepicker( "option", "maxDate", getDate( this ) );
                });

        function getDate( element ) {
            var date;
            try {
                date = $.datepicker.parseDate( dateFormat, element.value );
            } catch( error ) {
                date = null;
            }

            return date;
        }
        $('ul').hover(
            function() {
                $('ul').removeClass('active');
                $(this).addClass('active');
            }, function() {
                $( this ).removeClass( "hover" );
            }
        );

        $('li').on('click', function(){
            console.log('1 #################');
            var choosen = $(this).text();
            var parent = $(this).parent('ul');
            var grandparent = parent.parent('.td');
            var firstElement = $(this).parent('ul').find('li:first-child');
            firstElement.text(choosen);
            firstElement.val(choosen);
            parent.removeClass('active');
            parent.addClass('choosen');
            grandparent.next().find('ul').addClass('not-active');
            grandparent.prev().find('ul').addClass('not-active')
        });

        $("#multi-select").multiSelect({
            label: "Виберіть вагову категорію"
        });

        $('.judje-type').on('change', function(){
            var current_selector = $(this);

            checkField(current_selector);
            checkSelectorColor(current_selector);
            checkEnabledByRegion(current_selector.parent('.td'));
        });

        $('.ac').on('change', function () {
            checkAgeField($(this));
        });

        $('.wc').on('change', function () {
            checkWeightField($(this));
        });

        makeActive();

        function checkField(obj){
            console.log('5 #################');
            var data ={
                'ID':       <?=$post->ID?>,
                'judgeID':  obj.parent('div').attr('judge_id'),
                'dateID':   obj.parent('div').attr('date_id'),
                'sectionID':   obj.parent('div').attr('section_id'),
                'positionID':   obj.val(),
            };
            $.ajax(
                {
                    url: window.location.origin + '/<?=rest_get_url_prefix()?>/wp/v2/champion-helper/set',
                    type: "POST",
                    data: JSON.stringify(data),
                    dataType: "json",
                    contentType: 'application/json',
                    async: true,
                    cache: false,
                    timeout: 30000,
                    beforeSend: function ( xhr ) {
                        xhr.setRequestHeader( 'X-WP-Nonce', wpApiSettings.nonce );
                    },
                    success: function (response) {
                        //if success then remove
                        try {
                            if (response.code)
                            {
                            }
                        }
                        catch (exception){}
                    },
                    error: function (xhr, ajaxOptions, thrownError) {

                    }
                });
        }

        function checkAgeField(obj){
            var data ={
                'ID':       <?=$post->ID?>,
                'dateID':   obj.parent('div').attr('date_id'),
                'sectionID':   obj.parent('div').attr('section_id'),
                'age_categoryID':   obj.val(),
            };
            $.ajax(
                {
                    url: window.location.origin + '/<?=rest_get_url_prefix()?>/wp/v2/champion-helper/set',
                    type: "PUT",
                    data: JSON.stringify(data),
                    dataType: "json",
                    contentType: 'application/json',
                    async: true,
                    cache: false,
                    timeout: 30000,
                    beforeSend: function ( xhr ) {
                        xhr.setRequestHeader( 'X-WP-Nonce', wpApiSettings.nonce );
                    },
                    success: function (response) {
                        //if success then remove
                        try {

                            if (response.code)
                            {

                            }

                        }
                        catch (exception){}
                    },
                    error: function (xhr, ajaxOptions, thrownError) {

                    }
                });
        }

        function checkWeightField(obj){
            var data ={
                'ID':       <?=$post->ID?>,
                'dateID':   obj.parent('div').attr('date_id'),
                'sectionID':   obj.parent('div').attr('section_id'),
                'weight_categoryID':   obj.val(),
            };
            $.ajax(
                {
                    url: window.location.origin + '/<?=rest_get_url_prefix()?>/wp/v2/champion-helper/set',
                    type: "PATCH",
                    data: JSON.stringify(data),
                    dataType: "json",
                    contentType: 'application/json',
                    async: true,
                    cache: false,
                    timeout: 30000,
                    beforeSend: function ( xhr ) {
                        xhr.setRequestHeader( 'X-WP-Nonce', wpApiSettings.nonce );
                    },
                    success: function (response) {
                        //if success then remove
                        try {

                            if (response.code)
                            {

                            }

                        }
                        catch (exception){}
                    },
                    error: function (xhr, ajaxOptions, thrownError) {

                    }
                });
        }

        $('button.export_to_excel').on('click', function(e){
            e.preventDefault();
            $.ajax(
                {
                    url: window.location.origin + '/<?=rest_get_url_prefix()?>/wp/v2/champion-helper/export',
                    type: "POST",
                    data: JSON.stringify({
                        'ID':       <?=$post->ID?>
                    }),
                    dataType: "json",
                    contentType: 'application/json',
                    async: true,
                    cache: false,
                    timeout: 30000,
                    beforeSend: function ( xhr ) {
                        xhr.setRequestHeader( 'X-WP-Nonce', wpApiSettings.nonce );
                    },
                    success: function (response) {
                        //if success then remove
                        try {

                            if (response.code)
                            {

                            }

                        }
                        catch (exception){}
                    },
                    error: function (xhr, ajaxOptions, thrownError) {

                    }
                });
        });
        // selectJudge();
    } );
    function makeActive(){
        console.log('6 $$$$$$$$$$$$$$$');
        //$('.choosen').each(function () {
            // var grandparent = $(this).parent('.td');
            // grandparent.next().find('select').addClass('deactivated');
            // grandparent.prev().find('select').addClass('not-active');
            //checkSelectorColor($(this));
        //})
        $('#main_table .right-side .judje-field select').each(function() {
            if ($(this, 'option:selected').val() != 'default') {
               checkSelectorColor($(this), true); 
            }
        });

        var first_judge_id = $($('#main_table .right-side .judje-type-select')[0]).attr('judge_id');
        $('#main_table .right-side .judje-type-select[judge_id="'+first_judge_id+'"]').each(function() {
            checkEnabledByRegion($(this)); 
        });
    }
    function updateJudgeSelector(selector){
        console.log('7 $$$$$$$$$$$$$$$');
        var selector = $(selector);
        var parent = selector.parent();
        var date = parent.attr('date_id');
        var section = parent.attr('section_id');
        var selected_positions = checkJudgeList(date, section);

        $.each(role_titles, function(index, id){
            if (positionDisabled(id, selected_positions)) {
                selector.find('option:contains('+id+')').remove();
            }
        });
    }

    function selectJudge(e){
        $('.judje-type-select select').on('click', function(e){
            console.log('8 $$$$$$$$$$$$$$$');
            e.preventDefault();
            var selector = $(this);
            var parent = $(this).parent();
            var date = parent.attr('date_id');
            var section = parent.attr('section_id');
            var selected_positions = checkJudgeList(date, section);

            $.each(role_titles, function(index, id){
                if (positionDisabled(id, selected_positions)) {
                    selector.find('option:contains('+id+')').remove();
                }
            });
            e.stopPropagation();
        })
    }

    function checkJudgeList(date, section){
        console.log('9 $$$$$$$$$$$$$$$');
        var list = $('.judje-type-select[date_id = ' + date + '][section_id = ' + section + '] ').map(function(){
            var option = $(this).find('select option:checked').text();
            return option;
        }).get();
        var filtered = list.filter(function (el) {
            return el != '';
        });
        return filtered;
    }

    function positionDisabled(id, selected_ids){
        console.log('10 $$$$$$$$$$$$$$$');
        var list = selected_ids.filter(item => item == id);
        return list.length >= role_count[id];
    }

    //###### start COLOR FUNCTIONS ######

    function checkSelectorColor(current_selector, init_table) {
        var parent_td     = current_selector.parent('.td'),
            prev_selector = parent_td.prev().find('select'),
            next_selector = parent_td.next().find('select');

        // console.log('===== Update Current Selector');
        setColorForSelectors(current_selector, init_table);
        if (prev_selector.length > 0) {
            // console.log('===== Update Prev Selector');
            setColorForSelectors(prev_selector);
        }
        if (next_selector.length > 0) {
            // console.log('===== Update Next Selector');
            setColorForSelectors(next_selector);
        }
    }
    
    function setColorForSelectors(current_selector, init_table) {
        var parent_td     = current_selector.parent('.td'),
            prev_selector = parent_td.prev().find('select'),
            next_selector = parent_td.next().find('select');

        console.log('section_id', parent_td.attr('section_id'));
        if (current_selector.val() == "default") {
            console.log('Selector empty');
            console.log('near', nearSelectorsAbsentOrEmpty(prev_selector, next_selector));
            if (nearSelectorsAbsentOrEmpty(prev_selector, next_selector)) {
              console.log('Near Selectors empty or absent');
              current_selector.removeClass('disabled not-active choosen');
            }

            console.log('near', nearSelectorsWithData(prev_selector, next_selector));
            if (nearSelectorsWithData(prev_selector, next_selector)) {
              console.log('Near Selectors With data');
              current_selector.removeClass('disabled choosen');
              current_selector.addClass('not-active');
            }
        } else {
            console.log('Selector With data');
            console.log('near', nearSelectorsAbsentOrEmpty(prev_selector, next_selector));
            var init_and_first_section = (init_table && parent_td.attr('section_id') == 0);

            if (nearSelectorsAbsentOrEmpty(prev_selector, next_selector) || init_and_first_section) {
              console.log('Near Selector Absent or Empty or First with init_table');
              current_selector.removeClass('disabled not-active');
              current_selector.addClass('choosen');
            }
            if (init_and_first_section) { next_selector.addClass('not-active'); }
        }
    }

    function nearSelectorsAbsentOrEmpty(prev_selector, next_selector) {
        var prev_selector_absent = prev_selector.length == 0,
            next_selector_absent = next_selector.length == 0;

        return ((prev_selector_absent || prev_selector.val() == "default") &&
               (next_selector_absent || next_selector.val() == "default"))
    }

    function nearSelectorsWithData(prev_selector, next_selector) {
        var prev_selector_absent = prev_selector.length == 0,
            next_selector_absent = next_selector.length == 0;

        return ((!prev_selector_absent && prev_selector.val() != "default") ||
               (!next_selector_absent && next_selector.val() != "default"))
    }

    function checkEnabledByRegion(parent_td) {
        let date_id    = parent_td.attr('date_id'),
            section_id = parent_td.attr('section_id'),
            region     = parent_td.attr('region'),
            judge_role_ids = ['0', '1'],
            jury_roles_ids = ['6', '7'],
            judges     = $('#main_table [date_id="'+date_id+'"][section_id="'+section_id+'"][region="'+region+'"]'),
            judge_roles = [],
            jury_roles = [];

        if (judges.length > 1) {
          judges.map(function(){
            var selector = $(this).find('select');
            selector.removeClass('disabled');
            setColorForSelectors(selector);

            var option = $(this).find('select option:selected').val();
            if (judge_role_ids.includes(option)) { judge_roles.push($(this)) };
            if (jury_roles_ids.includes(option)) { jury_roles.push($(this)) };
          });

          if (judge_roles.length > 1) {
            $.each(judge_roles.splice(1), function(index, role){
              var selector = role.find('select');
              selector.removeClass('choosen not-active');
              selector.addClass('disabled');
            });
          }

          if (jury_roles.length > 1) {
            $.each(jury_roles.splice(1), function(index, role){
              var selector = role.find('select');
              selector.removeClass('choosen not-active');
              selector.addClass('disabled');
            });
          }
        }
    }
    //######  end COLOR FUNCTIONS  ######
</script>