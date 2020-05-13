<?php
/**
 * Created by PhpStorm.
 * User: test
 * Date: 04.03.20
 * Time: 18:01
 */
$jsonSTR = array(
        'age' => judgeClass::$category_age,
        'weight' => judgeClass::$category_weight,
        'judge' => judgeClass::$category_judge
);
$jsonSTR = array_merge($jsonSTR,$dt);
$competitions = judgeClass::getCompetitions();

?>
<div class="form-wrapper">
    <div class="form-group">
        <label for="last_name">Змагання на які хочете подати заявку </label>
    </div>
    <form action="/<?=rest_get_url_prefix()?>/wp/v2/champion-helper/apply" method="post" class="judge-form">
        <div class="form-group full-size">
            <select name="competition_id">
                <?php foreach ($competitions as $competition):?>
                    <option value="<?=$competition['ID']?>"><?=$competition['name']?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="form-group size">
            <label for="last_name">Прізвище</label>
            <input type="text" id="last_name" name="last_name">
        </div>
        <div class="form-group size">
            <label for="first_name">Ім'я</label>
            <input type="text" id="first_name" name="first_name">
        </div>
        <div class="form-group size">
            <label for="father_name">По-батькові</label>
            <input type="text" id="middle_name" name="middle_name">
        </div>
        <input type="hidden" name="country" id="countryId" value="UA"/>
        <div class="form-group half-size">
            <label for="stateId">Виберіть область</label>
            <select name="state" class="states order-alpha minimal" id="stateId">
                <option value="">Виберіть область</option>
            </select>
        </div>
        <div class="form-group half-size">
            <label for="cityId">Виберіть місто</label>
            <select name="city" class="cities order-alpha minimal" id="cityId">
                <option value="">Виберіть місто</option>
            </select>
        </div>
        <div class="form-group full-size one-column">
            <label for="judge_category">Виберіть суддівську категорію</label>
            <select name="judge_category" id="judge_category" class="minimal">
                <?php foreach($jsonSTR['judge'] as $key=>$value):?>
                <option value="<?=$key?>"><?=$value?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group full-size form-group-category">
            <p class="dropdown-description">Виберіть вікові та вагові категорії</p>
            <div class="form-group">
                <label for="age_category">Вікова категорія</label>
                <select name="" id="age_category" class="minimal">
                    <?php foreach ($jsonSTR['age']['male']as $key=>$value):?>
                        <option age-value="<?=$key?>" value="male"><?=$value?></option>
                    <?php endforeach; ?>
                    <?php foreach ($jsonSTR['age']['female']as $key=>$value):?>
                        <option age-value="<?=$key?>" value="female"><?=$value?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" id="multi-select">
                <label for="weight_category">Вагова категорія</label>
                <select name="" id="weight_category" class="minimal not-active">
                    <?php foreach ($jsonSTR['weight']['male']as $key=>$value):?>
                        <option weight-value="<?=$key?>" value="male<?=$key?>" class="sub-male"><?=$value?></option>
                    <?php endforeach; ?>
                    <?php foreach ($jsonSTR['weight']['female']as $key=>$value):?>
                        <option weight-value="<?=$key?>" value="female<?=$key?>" class="sub-female" disabled><?=$value?></option>
                    <?php endforeach; ?>
                </select>
                <span id="optionstore" style="display:none;"></span>
            </div>
            <div class="form-group">
                <label for="add-category">ddd</label>
                <div class="add-category">Додати</div>
            </div>
        </div>
        <table>
            <thead>
            <tr>
                <td>Вікова категорія</td>
                <td>Вагова категорія</td>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <div class="form-group full-size date">
            <label for="from">Виберіть дату заїзду</label>
            <label for="to">Виберіть дату виїзду</label>
            <input type="text" id="from" name="from">
            <input type="text" id="to" name="to">
        </div>
        <div class="form-group full-size one-column">
            <label for="comment">Вкажіть вартість проїзду</label>
            <textarea name="comment" id="comment"></textarea>
        </div>
        <input type="hidden" id="fpu-id" name="fpu" value="<?=$post->ID?>">
        <input type="submit" value="Подати заявку">
    </form>
</div>
<script>
    var category = <?=json_encode($jsonSTR)?>;

    $( function() {
        var dateFormat = "yy-mm-dd",
            from = $( "#from" )
                .datepicker({
                    minDate: new Date('<?=$jsonSTR['start']?>'),
                    maxDate: new Date('<?=$jsonSTR['end']?>'),
                    changeMonth: true,
                    numberOfMonths: 1,
                    dateFormat: "yy-mm-dd",
                })
                .on( "change", function() {
                    to.datepicker( "option", "minDate", getDate( this ) );
                }),
            to = $( "#to" ).datepicker({
                minDate: new Date('<?=$jsonSTR['start']?>'),
                maxDate: new Date('<?=$jsonSTR['end']?>'),
                changeMonth: true,
                numberOfMonths: 1,
                dateFormat: "yy-mm-dd",
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


        $('form.judge-form').on('submit', function(e){
            e.preventDefault();
            var data =JSON.stringify({
                'fpu' : $('input#fpu-id').val(),
                'comment' : $('#comment').val(),
                'to' : $('#to').val(),
                'from' : $('#from').val(),
                'age_weight_gender_category' : $('tbody tr').map(function(){
                    var age = $(this).attr('age_id');
                    var weight = $(this).attr('weight_id');
                    var gender = $(this).attr('gender_id');
                    return {
                        age, weight, gender
                    }
                }).get(),
                'judge_category' : $('#judge_category').val(),
                'city' : $('#cityId').val(),
                'state' : $('#stateId').val(),
                'middle_name' : $('#middle_name').val(),
                'last_name' : $('#last_name').val(),
                'first_name' :  $('#first_name').val(),
            });
            $.ajax(
                {
                    url: window.location.origin + '/<?=rest_get_url_prefix()?>/wp/v2/champion-helper/apply',
                    type: "POST",
                    data: data,
                    dataType: "json",
                    contentType: 'application/json',
                    async: true,
                    cache: false,
                    timeout: 30000,
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


        $('#age_category').on("change", function() {
            var cattype = $(this).val();
            optionswitch(cattype);
        });

        // Add values with age and weight in the table
        addCategory();
    } );
    function optionswitch(myfilter) {
        if ($('#optionstore').text() == "") {
            $('option[class^="sub-"]').each(function() {
                var optvalue = $(this).attr('value');
                var optweightvalue = $(this).attr('weight-value');
                var optclass = $(this).prop('class');
                var opttext = $(this).text();
                optionlist = $('#optionstore').text() + "@%" + optvalue + "@%" + optweightvalue + "@%" + optclass + "@%" + opttext;
                $('#optionstore').text(optionlist);
            });
        }
        $('option[class^="sub-"]').remove();
        populateoption = rewriteoption(myfilter);
        $('#weight_category').html(populateoption);
    }

    function rewriteoption(myfilter) {
        var options = $('#optionstore').text().split('@%');
        var resultgood = false;
        var myfilterclass = "sub-" + myfilter;
        var optionlisting = "";

        myfilterclass = (myfilter != "")?myfilterclass:"all";
        for (var i = 4; i < options.length; i = i + 4) {
            if (options[i - 1] == myfilterclass || myfilterclass == "all") {
                optionlisting = optionlisting + '<option weight-value="' + options[i - 2] + '" value="' + options[i - 3] + '"  class="sub-' + options[i - 1] + '">' + options[i] + '</option>';
                resultgood = true;
            }
        }
        if (resultgood) {
            return optionlisting;
        }
    }
    function addCategory(){
        $('.add-category').on('click', function(){
            var age = $('#age_category option:checked').text();
            var weight = $('#weight_category option:checked').text();
            var gender_id = $('#age_category option:checked').val();
            var weight_id = $('#weight_category option:checked').attr('weight-value');
            var age_id = $('#age_category option:checked').attr('age-value');
            var markup = "<tr gender_id=" + gender_id + " weight_id =" + weight_id + " age_id=" + age_id + "><td>" + age + "</td><td>" + weight + "</td></tr>";
            $("table tbody").append(markup);
        });


    }

</script>

<script src="//geodata.solutions/includes/statecity.js"></script>