$(function()
{
    $('.panel-history a').click(function()
    {
        var href = $(this).prop('href');
        var app  = '';
        if(href.indexOf('/crm/') != -1) app = 'crm';
        if(href.indexOf('/oa/') != -1)  app = 'oa';

        if(app != '' && $(this).data('toggle') == undefined)
        {
            $.openEntry(app, href);
            return false;
        }
    });
})
