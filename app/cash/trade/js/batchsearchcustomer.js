var $selectedItem;
var selectItem = function(item)
{
    $selectedItem = $(item).first();
    $('#triggerModal').modal('hide');
};

$(document).ready(function()
{
    var showSearchModal = function(e)
    {
        $('.selected').removeClass('selected');
        if(e.hasClass('no-results')) 
        { 
            var key = e.parents('.chosen-container').find('.chosen-results > li.no-results > span').text();
            e.parents('.chosen-container').prev('select').addClass('selected');
        }
        else
        {
            var key = e.next('.chosen-container').find('.chosen-results > li.no-results > span').text();
            e.addClass('selected');
        }
        var relation = e.parents('tr').find('[name*=type]').val() == 'in' ? 'client' : 'provider';
        var link     = createLink('customer', 'ajaxSearchCustomer', 'key=' + key + '&relation=' + relation);
        $.zui.modalTrigger.show({url: link, backdrop: 'static'});
    };

    $(document).on('change', '[name*=trader]', function()
    {
        if($(this).val() === 'showmore')
        {
             showSearchModal($(this));
        }
    });

    $(document).on('click', '.chosen-container[id^=trader] .chosen-results > li.no-results', function(){showSearchModal($(this));});

    $(document).on('hide.zui.modal', '#triggerModal', function()
    {
        var key     = '';
        var $trader = $('.selected'); 
        var type    = $trader.parents('tr').find('[name*=type]').val();
        if($selectedItem && $selectedItem.length)
        {
            key = $selectedItem.data('key');
            $('[name^=' + type + 'trader], [name^=trader]').each(function()
            {
                if(!$(this).children('option[value="' + key + '"]').length)
                {
                    $(this).prepend('<option value="' + key + '">' + $selectedItem.text() + '</option>');
                }
                $(this).trigger('chosen:updated');
            });
        }
        $trader.val(key).trigger('chosen:updated');
        $selectedItem = null;
    });
})
