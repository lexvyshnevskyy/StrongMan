$(document).ready(function(){

    $('.__select_date__ input').on('change',function(){
        if ($('input[name="date_start"]').val() && $('input[name="date_end"]').val())

            var days = get_days_in_interval($('input[name="date_start"]').val(),$('input[name="date_end"]').val());

        //if ($('input[name="date_start"]').val())
    });

    function get_days_in_interval(start, end){
        var date1 = new Date(start);
        var date2 = new Date(end);
        // To calculate the time difference of two dates
        var Difference_In_Time = date2.getTime() - date1.getTime();

        // To calculate the no. of days between two dates
        var Difference_In_Days = Difference_In_Time / (1000 * 3600 * 24);
        return Difference_In_Days;
    }

    $('.section_comment_block').on('change', function () {
        console.log('click');
        var selected_blocks=$(this).val();
        var period = $(this).parent('div').attr('period');
        var data='';
        $(this).parent('div').find('.section_block').empty();
        for (var i=0; i<selected_blocks; i++){
            data='                <div class="wrapper __section_'+i+'__">\n' +
                '                    Потік '+(i+1)+'\n' +
                '                    <select class="minimal" name="sectionDSCR_'+period+'_'+i+'" >' +
                '                        <option>Виберіть стать учасників</option>\n' +
                '                        <option value = "male" value="male">Чоловіки</option>\n' +
                '                        <option value = "female" value="female">Жінки</option>\n' +
                '                    </select>'
                '                </div>';

            $(this).parent('div').find('.section_block').append(data);
        }
    })
});

