var $selectedItem;
var selectItem = function(item)
{
    $selectedItem = $(item).first();
    var modal = $('#ajaxModal');
    var link  = modal.attr('ref') + '&customerID=' + $selectedItem.data('key');
    modal.load(link, function(){$(this).find('.modal-dialog').css('width', $(this).data('width')); $.zui.ajustModalPosition()})
};

$(function()
{
    var showSearchModal = function()
    {
        var key      = $('#customer_chosen .chosen-results > li.no-results > span').text();
        var relation = 'client';
        var link     = createLink('customer', 'ajaxSearchCustomer', 'key=' + key + '&relation=' + relation);
        $('#ajaxModal').load(link);
    };

    $(document).on('change', '#customer', function()
    {
        if($(this).val() === 'showmore')
        {
             showSearchModal();
        }
    });

    $(document).on('click', '#customer_chosen .chosen-results > li.no-results', showSearchModal);

    $(document).on('hide.zui.modal', '#triggerModal', function()
    {
        var key       = '';
        var $customer = $('#customer');
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

    $('#selectCustomer').change(function()
    {
        if($(this).prop('checked'))
        {
            $('#customer_chosen').show();
            $(this).parents('.input-group').find('input[type=text][id=name]').hide();

            $('#customer').trigger("chosen:updated");
        }
        else
        {
            $(this).parents('.input-group').find('select').hide();
            $('#customer_chosen').hide();
            $(this).parents('.input-group').find('input[type=text][id=name]').show();

            $('#customer').trigger("chosen:updated");
        }
    });
    $('#selectCustomer').change();
})
