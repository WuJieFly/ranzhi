$(document).ready(function()
{
    $(document).on('click', '#makeupTable td:not(.idTD,.actionTD)', function()
    {
        $(this).parent().find('.actionTD a[href*=view]:not([href*=review])').click();
    });

    $(document).on('click', '.deleteMakeup', function()
    {
        if(confirm(v.lang.confirmDelete))
        {
            $(this).text(v.lang.deleting);
            $.getJSON($(this).attr('href'), function(data)
            {
                if(data.result == 'success')
                {
                    if(data.locate) return location.href = data.locate;
                    return location.reload();
                }
                else
                {
                    alert(data.message);
                    if(selecter.parents('#ajaxModal').size()) return $.reloadAjaxModal(1200);
                    return location.reload();
                }
            });
        }
        return false;
    });

    $(document).on('click', '.reviewPass', function()
    {
        if(confirm(v.confirmReview.pass))
        {
            var selecter = $(this);

            $.getJSON(selecter.attr('href'), function(data) 
            {
                if(data.result == 'success')
                {
                    if(data.locate) return location.href = data.locate;
                    return location.reload();
                }
                else
                {
                    alert(data.message);
                    return location.reload();
                }
            });
        }
        return false;
    });

    $('.batchPass').on('click', function()
    {
        $('#ajaxForm').submit();
    });

    /* expand active tree. */
    $('.tree li.active .hitarea').click();
});
