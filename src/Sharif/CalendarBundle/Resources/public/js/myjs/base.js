/**
 * Created with JetBrains PhpStorm.
 * User: M.Bagher
 * Date: 7/15/13
 * Time: 2:39 AM
 * To change this template use File | Settings | File Templates.
 */
$(function() {

    $( "#accordion" ).accordion();



    var availableTags = [
        "ActionScript",
        "AppleScript",
        "Asp",
        "BASIC",
        "C",
        "C++",
        "Clojure",
        "COBOL",
        "ColdFusion",
        "Erlang",
        "Fortran",
        "Groovy",
        "Haskell",
        "Java",
        "JavaScript",
        "Lisp",
        "Perl",
        "PHP",
        "Python",
        "Ruby",
        "Scala",
        "Scheme"
    ];
    $( "#autocomplete" ).autocomplete({
        source: availableTags
    });



    $( "#button" ).button();
    $( "#radioset" ).buttonset();



    $( "#tabs" ).tabs();



    $( "#dialog" ).dialog({
        autoOpen: false,
        width: 400,
        buttons: [
            {
                text: "Ok",
                click: function() {
                    $( this ).dialog( "close" );
                }
            },
            {
                text: "Cancel",
                click: function() {
                    $( this ).dialog( "close" );
                }
            }
        ]
    });

    // Link to open the dialog
    $( "#dialog-link" ).click(function( event ) {
        $( "#dialog" ).dialog( "open" );
        event.preventDefault();
    });



//    $( "#datepicker" ).datepicker({
//        inline: true
//    });



    $( "#slider" ).slider({
        range: true,
        values: [ 17, 67 ]
    });



    $( "#progressbar" ).progressbar({
        value: 20
    });


    // Hover states on the static widgets
    $( "#dialog-link, #icons li" ).hover(
        function() {
            $( this ).addClass( "ui-state-hover" );
        },
        function() {
            $( this ).removeClass( "ui-state-hover" );
        }
    );



    // حالت پیشفرض
    $('#datepicker0').datepicker();
    //-----------------------------------
    // نمایش شماره هفته
    $('#datepicker1').datepicker({
        showWeek: true
    });
    //-----------------------------------
    // پرکردن فیلد اضافی
    $("#datepicker2").datepicker({
        dateFormat: 'dd/mm/yy',
        altField: '#alternate2',
        altFormat: 'DD، d MM yy'
    });
    //-----------------------------------
    // نمایش دکمه ها
    $('#datepicker3').datepicker({
        showButtonPanel: true
    });
    //-----------------------------------
    // تغییر قالب نمایش تاریخ و تغییر سایز خودکار فیلد
    $("#datepicker4").datepicker({
        dateFormat: 'dd/mm/yy',
        autoSize: true
    });
    $("#format4").change(function() {
        $('#datepicker4').datepicker('option', { dateFormat: $(this).val() });
    });
    //-----------------------------------
    // استفاده از dropdown
    $('#datepicker5').datepicker({
        changeMonth: true,
        changeYear: true
    });
    //-----------------------------------
    // انتخاب با کلیک بر روی عکس
    $("#datepicker6").datepicker({
        showOn: 'button',
        buttonImage: 'styles/images/calendar.png',
        buttonImageOnly: true
    });
    //-----------------------------------
    // نمایش inline
    $('#datepicker7').datepicker();
    //-----------------------------------
    // نمایش چند ماه
    $('#datepicker8').datepicker({
        numberOfMonths: 3,
        showButtonPanel: true
    });
    //-----------------------------------
    // غیرفعال کردن روزها
    $('#datepicker9').datepicker({
        beforeShowDay: function(date) {
            if (date.getDay() == 5)
                return [false, '', 'تعطیلات آخر هفته'];
            return [true];
        }
    });
    //-----------------------------------
    // تاریخ پیشفرض
    $('#datepicker10').datepicker({
        defaultDate: new JalaliDate(1361, 4, 10)	//this means "1361/05/10"
    });
    //-----------------------------------
    // تنظیم حداقل و حداکثر
    $('#datepicker11').datepicker({
        minDate: '-3d',
        maxDate: '+1w +2d'
    });
    //-----------------------------------
    // تنظیم حداقل بصورت پویا
    $('#datepicker12from').datepicker({
        onSelect: function(dateText, inst) {
            $('#datepicker12to').datepicker('option', 'minDate', new JalaliDate(inst['selectedYear'], inst['selectedMonth'], inst['selectedDay']));
        }
    });
    $('#datepicker12to').datepicker();
    //-----------------------------------
    // استفاده همزمان از تقویم میلادی
    $('#datepicker13').datepicker({
        regional: ''
    });
    //-----------------------------------
    // استفاده همزمان از تقویم هجری قمری
    $('#datepicker14').datepicker({
        regional: 'ar'
    });
});





function show_shamsi(){
    $('#datepicker0').fadeIn(500);
    $('#datepicker13').fadeOut(500);
    $('#datepicker14').fadeOut(500);
}

function show_ghamari(){
    $('#datepicker14').fadeIn(500);
    $('#datepicker0').fadeOut(500);
    $('#datepicker13').fadeOut(500);
}

function show_miladi(){
    $('#datepicker13').fadeIn(500);
    $('#datepicker0').fadeOut(500);
    $('#datepicker14').fadeOut(500);
}