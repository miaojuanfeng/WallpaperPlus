var thisInputName = '';
var nameId = '';
var nameStatus = '';
var nameDate = '';
var nameRemark = '';

$('input[name^="checklistStatus-"]').click(function(){
	thisInputName = $(this).attr('name');
	nameId = $(this).attr('name-id');
	nameStatus = $(this).attr('name-status');
	nameDate = $(this).attr('name-date');
	nameRemark = $(this).attr('name-remark');
    $("#popupModal").fadeIn(100, function(){
        $(this).addClass("in");
    });
});

function hideModal(){
	$('input[name="'+thisInputName+'"]').prop('checked', false);
    thisInputName = '';
    nameId = '';
	nameStatus = '';
	nameDate = '';
	nameRemark = '';
    $("#popupModal").removeClass("in").fadeOut(100);
}

function clickSave() {
	thisId = thisInputName.replace('checklistStatus-', '');
	$('input[name="'+nameId+'"]').val(thisId);
	$('input[name="'+nameStatus+'"]').val('complete');
	$('input[name="'+nameDate+'"]').val($('input[name="modal_date"]').val());
	$('input[name="'+nameRemark+'"]').val(encodeURI($('textarea[name="modal_remark"]').val()));
	$('form[name="list"]').submit();
}