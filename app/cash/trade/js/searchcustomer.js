var $selectedItem;
var selectItem = function(item)
{
    $selectedItem = $(item).first();
    $('#triggerModal').modal('hide');
};

$(document).ready(function()
{
    var showSearchModal = function()
    {
        var objectType = $('[name*=objectType]:checked').val();
        var e          = objectType !== undefined ? (objectType == 'contract' ? 'allCustomer' : 'customer') : 'trader';
        var key        = $('#' + e + '_chosen .chosen-results > li.no-results > span').text();
        var relation   = v.modeType == 'in' ? 'client' : (v.modeType == 'out' ? (objectType === undefined ? 'provider' : (objectType == 'contract' ? '' : 'client')) : '');
        var link       = createLink('customer', 'ajaxSearchCustomer', 'key=' + key + '&relation=' + relation);
        $.zui.modalTrigger.show({url: link, backdrop: 'static'});
    };

    $(document).on('change', '#trader, #customer, #allCustomer', function()
    {
        if($(this).val() === 'showmore')
        {
             showSearchModal();
        }
    });

    $(document).on('click', '#trader_chosen .chosen-results > li.no-results', showSearchModal);
    $(document).on('click', '#customer_chosen .chosen-results > li.no-results', showSearchModal);
    $(document).on('click', '#allCustomer_chosen .chosen-results > li.no-results', showSearchModal);

    $(document).on('hide.zui.modal', '#triggerModal', function()
    {
        var key        = '';
        var objectType = $('[name*=objectType]:checked').val();
        var $trader    = objectType === undefined ? $('#trader') : (objectType == 'contract' ? $('#allCustomer') : $('#customer'));
        if($selectedItem && $selectedItem.length)
        {
            key = $selectedItem.data('key');
            if(!$trader.children('option[value="' + key + '"]').length)
            {
                $trader.prepend('<option value="' + key + '">' + $selectedItem.text() + '</option>');
            }
        }
        $trader.val(key).trigger("chosen:updated");
        $selectedItem = null;
    });
})
