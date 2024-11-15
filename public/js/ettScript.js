/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/javascript.js to edit this template
 */

var hideFlashmessageTimerId;
function showAndHideFlashMessages()  {
    $('.flash-message-wrapper').removeClass('hidden');
    
    hideFlashmessageTimerId = setTimeout(function() { 
        $('.flash-message-wrapper').addClass('hidden'); 
        hideFlashmessageTimerId = setTimeout(function() { 
            $('.flash-message-area').addClass('hidden');
        }, 500);
    }, 3000);
}

function setFocusToScanInput() {
    $('#form_scan').focus();
}

    
$(document).ready(function() {
    
    if ($('.flash-message-area .flash-message').length > 0) {
        showAndHideFlashMessages();
        $('.flash-message-area .flash-message').on('mouseenter', function() {
            clearTimeout(hideFlashmessageTimerId);
        });
        $('.flash-message-area .flash-message').on('mouseleave', function() {
            showAndHideFlashMessages();
        });
    }
    

    if ($('.managing-tabs').length > 0 && $('.managing-tab-input:checked').length == 0) {
        $('.managing-tab-input').first().prop('checked', true);
    }
    
    if ($('#form_scan').length > 0) {
        $('#form_scan').val('');
        setFocusToScanInput();

        $('.assignmentGroup-data-wrapper .assignmentGroup-data').each(function() {
            var newBesatzung = $(this).clone();
            var id = newBesatzung.attr('data-id');

            $('.assignmentGroups input[data-id='+id+'] + label').append(newBesatzung);
            $('.assignmentGroups input[data-id='+id+'] + label').addClass(newBesatzung.attr('class'));
        });
        
        $('.layoutMainWrapper .content').click(function() {
            setFocusToScanInput();
        });
    }
});