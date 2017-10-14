//*************** JS FUNKCIJE ZA DESIGNERS PERFORMANCE ***********
//
//                         2015.
//
// @Author
// Boris
//****************************************************************
// **********  **********
//****************************************************************

//*********************************************************************
//********** FUNKCIJA ZA FILTERE - STAVLJA PARAMETRE U GET ************
//*********************************************************************
$.ajaxSetup({
            cache:false
          });
var SearchFormSimple = {};

SearchFormSimple.search = function(obj) {

  var button  = $(obj);
  var forma   = button.parents('form');
  var objekti = forma.find("input,select");

  var podaci = '';
  objekti.each(function(){
    
    var ime = $(this).attr("name");
    var val = $(this).val();
    podaci += ime + "=" + val + "&";

  });
  
  location.search = encodeURI(podaci);
  return false;

}
//*********************************************************************
//********** BRISANJE REDOVA IZ BAZE    *******************************
//*********************************************************************

function deleteRow(table,obj,rowNum) {
    var r = confirm("Are you shure you want to delete the record?");
    if (r == true) {

        var idNum = $(obj).data('id');
        var podaci = {action:"deleteRow",id:idNum,table:table};
        var x = obj.rowIndex;

            $.ajax({
                url:"../includes/adapter.php",
                type:"POST",
                dataType:"JSON",
                data:podaci,
                async: true,
                success:function(data){
                    if(data == 1)
                    {
                        deleteTableRow(rowNum);
                        showSuccess("Record removed from database!");
                    } else {
                        showError("Error occured!");
                    }
                }
            });

    } else {}

}
//*********************************************************************
//********** ALERT  BOXES ***********************************************
//*********************************************************************

function showError($msg) {

    $('#messageE').empty();
    $('#messageE').append($msg);
    $('.errorB').fadeIn('fast');
    setTimeout(function(){$('.errorB').fadeOut('fast');},3000);
}
function showSuccess($msg) {

    $('#messageS').empty();
    $('#messageS').append($msg);
    $('.successB').fadeIn('fast');
    setTimeout(function(){$('.successB').fadeOut('fast');},3000);
}
function showWarning($msg) {

    $('#messageW').empty();
    $('#messageW').append($msg);
    $('.warningB').fadeIn('fast');
    setTimeout(function(){$('.warningB').fadeOut('fast');},3000);
}
//*********************************************************************
//********** DELETE ROW FROM TABLE ************************************
//*********************************************************************
function deleteTableRow(rowid)
{
    var row = document.getElementById(rowid);
    row.parentNode.removeChild(row);
}
//*********************************************************************
//********** select option by GET *************************************
//*********************************************************************
function getToOption(elementId,getName){
    //uhvati parametre iz GET-a
    var parseQueryString = function() {

        var str = window.location.search;
        var objURL = {};

        str.replace(
            new RegExp( "([^?=&]+)(=([^&]*))?", "g" ),
            function( $0, $1, $2, $3 ){
                objURL[ $1 ] = $3;
            }
        );
        return objURL;
    };
//sredi parametre
    var params = parseQueryString();
    var getFromGet = params[getName];
//Funkcija za odabir opcije
    function setOption(selectElement, value) {
        var options = selectElement.options;
        for (var i = 0, optionsLength = options.length; i < optionsLength; i++) {
            if (options[i].value == value) {
                selectElement.selectedIndex = i;
                return true;
            }
        }
        return false;
    }
    //Zavrsi odabir opcije
    setOption(document.getElementById(elementId), getFromGet);
}

//*********************************************************************
//**********Selekcija polja tabele za EDIT - GENERALIZOVANO ***********
//*********************************************************************

    function tdOption(obj){
        var typeObj = $(obj).find(".fSel").attr('type');
        var obValue = $(obj).find(".fSpan").text();
        if (typeObj == "text"){
            $(obj).find(".fSel").val(obValue);
        }
        $(obj).find(".fSpan").hide();
        $(obj).find(".fSpan").addClass('fHidden');
        $(obj).find(".fSel").show();
        $(obj).find(".fSel").focus();
    }
    function changeFieldValue(id,field,value){
        var podaci = {};
        podaci["action"] = "changeFieldValue";
        podaci["table"] = table;
        podaci["id"] = id;
        podaci["field"] = field;
        podaci["value"] = value;

        $.ajax({
            url:"../includes/adapter.php",
            type:"POST",
            dataType:"JSON",
            data:podaci,
            async: true,
            success:function(data){
                if(data > 0)
                {
                    location.reload();
                }
            }
        });
        return false;
    }
 $(document).ready(function(){

    $('#datumFrom,#datumTo,#_dtFrom,#_dtTo').datepicker({
          dateFormat: "yy-mm-dd"
      });
    
    $(".fSel").blur(function(){
        $(this).hide();
        $('.fHidden').show();
        $('.fHidden').removeClass('fHidden');
    });
    $(".fSel").change(function(){
        var vrijednost = $(this).val();
        var id = $(this).data('id');
        var field = $(this).data('field');
        if (vrijednost !== "") {
            showSuccess("Value changed!");
            changeFieldValue(id,field,vrijednost);
        } else {
        }
    });
    $('.fSel').keyup(function(event){
        if(event.keyCode == 13){
            $('.fSel').trigger('change');
        }
    });
     $('._timeTo').blur(function(){

         var container = $(this).closest("tr");
         var timeTo = container.find('._timeTo').val();
         var fromValue = container.find('._timeFrom').val();
         var ukupnoHRS = container.find('._timeHRS');

         var ukupno = timeTo - fromValue;
         
         ukupnoHRS.val(ukupno);
     });

     $('.vacation').click(function(){
        if ($(this).hasClass("toolActive") == true){
                $(this).removeClass("toolActive");
                $('.toolDate').fadeOut('slow');
                $('._timeFrom,._timeTo,._type,._description,._thread,._site').removeAttr("disabled");
                $('._timeFrom,._timeTo,._type,._description,._thread,._site').css("background-color", "#fff");
            } else { 
                $(this).addClass("toolActive");
                $('.sickness').removeClass("toolActive");
                $('.toolDate').fadeIn('slow');
                $('._timeFrom,._timeTo,._type,._description,._thread,._site').attr("disabled", "disabled");
                $('._timeFrom,._timeTo,._type,._description,._thread,._site').css("background-color", "#999");
            }
        
     });
     $('.sickness').click(function(){
        if ($(this).hasClass("toolActive") == true){
                $(this).removeClass("toolActive");
                $('.toolDate').fadeOut('slow');
                $('._timeFrom,._timeTo,._type,._description,._thread,._site').removeAttr("disabled");
                $('._timeFrom,._timeTo,._type,._description,._thread,._site').css("background-color", "#fff");
            } else {
                $(this).addClass("toolActive");
                $('.vacation').removeClass("toolActive");
                $('.toolDate').fadeIn('slow');
                $('._timeFrom,._timeTo,._type,._description,._thread,._site').attr("disabled", "disabled");
                $('._timeFrom,._timeTo,._type,._description,._thread,._site').css("background-color", "#999");
            }
        
     });

});
//*********************************************************************
//********** TOOGLE COLLAPSE ZA REPORTE *******************************
//*********************************************************************

function toogleCollapse(obj){

    var tabela = $(obj).closest("table");
    var bodyElement = tabela.find(".reportHolder");

    if (bodyElement.hasClass("collapse") == true){
        bodyElement.removeClass("collapse");
        bodyElement.fadeIn('slow');
    } else {
        bodyElement.addClass("collapse");
        bodyElement.fadeOut('slow');
    }


}

//*********************************************************************
//********** Link na view reporta *******************************
//*********************************************************************

function linkToView(uId){
    window.location = 'viewReport.php?id='+uId; 
}

//*********************************************************************
//********** Link na view reporta *******************************
//*********************************************************************

function linkToReportSearch(uId){
    var fromT = $('#datumFrom').val();
    var toT =  $('#datumTo').val();

    window.location = 'viewReport.php?id='+uId+'&from='+ fromT +'&to='+ toT; 
}
//*********************************************************************
//********** Link na view reporta *******************************
//*********************************************************************

function countLoggedHours(){
$('.countHours').empty();
    var totalLoggedH = 0;
    var totalLoggedM = 0;
    $('._timeHour').each(function(){
            var sati = $(this).val();
            totalLoggedH = totalLoggedH + Number(sati);
    });

    $('._timeMin').each(function(){
            var minute = $(this).val();
            totalLoggedM = totalLoggedM + Number(minute);
    });
    var addHour = Math.floor(totalLoggedM / 60);
    var restMin = totalLoggedM%60;

    totalLoggedH = totalLoggedH + addHour;

    $('.countHours').append('Total logged: '+ totalLoggedH +'h '+ restMin +'min');
}
//*********************************************************************
//********** Dodaj novi red na report *******************************
//*********************************************************************

// function addNewReportRow(){
//     var repContent = $('.reportItem').html();


//     var itemEntry = '<tr class="reportItem">'+
//                     repContent+
//                     '</tr>';

//     $('#items').append(itemEntry);
//     $('_product').last().trigger("chosen:updated");
    
// }

//*********************************************************************
//********** Omoguci polja za dizajnere *******************************
//*********************************************************************

function enableFields(o){
    var selektovanDrop = o.id;
    
    $('#'+selektovanDrop).closest('._state').removeAttr('disabled');

}

