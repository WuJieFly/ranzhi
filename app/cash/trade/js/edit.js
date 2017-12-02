$(document).ready(function()
{
    $('#depositor').change(function()
    {
        $.get(createLink('trade', 'ajaxGetCurrency', 'depositorID=' + $(this).val()), function(currency)
        {
            if(!currency) return false;

            $('#currency').val(currency);
            $('#currencyLabel').val(currency);
            $('.exchangeRate').toggle(currency != v.mainCurrency);
        });
    });

    $('#order, #contract').change(function()
    {
        $('#money').val($(this).find('option:selected').attr('data-amount'));
        $('#customer').val($(this).find('option:selected').attr('data-customer'));
        $('#customer').trigger('chosen:updated');
    })

    $('.contractTD select').empty().load(createLink('crm.contract', 'getOptionMenu', 'traderID=' + $('#trader').val()), function()
    {
        $('#contract').val(v.contract);
    });

    $('#depositor').change();
})
