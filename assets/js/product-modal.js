var page = 0;
var order = '';
var ascend = '';
var search = '';
var product_input = null;

function getUrl(){
    retval = '';
    if( order && ascend ){
        retval += 'order/'+order+'/ascend/'+ascend+'/';
    }
    if( search ){
        retval += search;
    }
    retval += 'page/'+page;
    return retval;
}

function loadData() {
    $('.modal-body').load('/modal', {'thisTableId': $(this).attr('modal'), 'thisUrl': getUrl(), 't': timestamp()}, function(data){
        // $(".modal-body").html(data);
    });
}

$(".trModal").on('click', '.showModal', function(){
    product_input = $(this).prev();
    $('.modal-body').load('/modal', {'thisTableId': $(this).attr('modal'), 'thisUrl': 'page/0', 't': timestamp()}, function(data){
        // $(".modal-body").html(data);
        $("#popupModal").fadeIn(100, function(){
            $(this).addClass("in");
        });
    });
});

function hideModal(){
    page = 0;
    order = '';
    ascend = '';
    search = '';
    product_input = null;
    $("#popupModal").removeClass("in").fadeOut(100);
}

/*
*   select
*/

function changePage(p){
    page = p;
    loadData();
}

function changeSort(o){
    if( order != o ){
        ascend = 'desc';
    }else if( ascend == 'desc' ){
        ascend = 'asc';
    }else if( ascend == 'asc' ){
        ascend = 'desc';
    }
    order = o;
    loadData();
}

function clickSearch(){
    search = '';
    $('.search-control input[type="text"]').each(function(){
        var name = $(this).attr("name");
        var value = $(this).val();
        if( value != '' ){
            search += name+'/'+value+'/';
        }
    });
    loadData();
}

function clickRecord(product_id){
    product_input.val(product_id);
    product_loader(product_input);
    hideModal();
}

/*
*   insert-update
*/

