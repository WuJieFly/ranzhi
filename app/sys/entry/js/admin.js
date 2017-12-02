$(document).ready(function()
{
    $.setAjaxDeleter('.entry-deleter', function(response)
    {   
        if(response.result == 'success')
        {   
            if(response.entries) 
            {   
                v.entries = JSON.parse(response.entries);
                $.refreshDesktop(v.entries, true);
            }   
        }   
    }); 

    $('#entryList').sortable(
    {
        selector: 'li',
        dragCssClass: 'drag-row',
        trigger: '.sort-handler-1',
        finish: function(data)
        {
            var orders = {};
            var orderNext = 1;
            data.list.each(function()
            {
                orders[$(this).data('entryid')] = orderNext++;
            });
            $.post(createLink('entry', 'customSort'), orders);
        }
    });
    $('#entryList .ulGrade2').sortable(
    {
        selector: 'li',
        dragCssClass: 'drag-row',
        trigger: '.sort-handler-2',
        finish: function(data)
        {
            var orders = {};
            var orderNext = 1;
            data.list.each(function()
            {
                orders[$(this).data('entryid')] = orderNext++;
            });
            $.post(createLink('entry', 'customSort'), orders);
        }
    });
});
