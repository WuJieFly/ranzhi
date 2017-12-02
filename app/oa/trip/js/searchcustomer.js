var $selectedItem;
var selectItem = function(item)
{
    $selectedItem = $(item).first();
    var modal = $('#ajaxModal');
    var link  = modal.attr('ref') + '&customers=' + $('input[type=hidden][name*=customers]').val() + ',' + $selectedItem.data('key');
    modal.load(link, function(){$(this).find('.modal-dialog').css('width', $(this).data('width')); $.zui.ajustModalPosition()})
};

$(document).ready(function()
{
    var showSearchModal = function()
    {
        var key       = $('#customers_chosen .chosen-results > li.no-results > span').text();
        var customers = $('[name*=customer]').val().join(',').replace(',showmore', '');
        var link      = createLink('customer', 'ajaxSearchCustomer', 'key=' + key + '&relation=&customers=' + customers);
        $('#ajaxModal').load(link);
    };

    $(document).on('change', '#customers', function()
    {
        if($.inArray('showmore', $(this).val()) > 0)
        {
             showSearchModal();
        }
    });

    $(document).on('click', '#customers_chosen .chosen-results > li.no-results', showSearchModal);

    $(document).on('hide.zui.modal', '#triggerModal', function()
    {
        var key       = '';
        var $customer = $('#customers');
        if($selectedItem && $selectedItem.length)
        {
            key = $selectedItem.data('key');
            if(!$customer.children('option[value="' + key + '"]').length)
            {
                $customer.prepend('<option value="' + key + '">' + $selectedItem.text() + '</option>');
            }
        }
        $customer.val(key).trigger("chosen:updated");
        $selectedItem = null;
    });
})
