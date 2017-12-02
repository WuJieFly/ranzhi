$(document).ready(function()
{
    $('#menu .nav li').removeClass('active').find('[href*=' + v.mode + ']').parent().addClass('active');
    if(v.mode == 'reviewed') $('#menu .nav li').find('[href*=unreviewed]').parent().removeClass();

    $.setAjaxJSONER('.refund', function(response)
    {
        if(response.result == 'success')
        {
            bootbox.dialog(
            {  
                message: v.createTradeTip,  
                buttons:
                {  
                    trade:
                    {  
                        label: v.lang.yes,
                        className: 'btn-primary',  
                        callback:  function()
                        {
                            $('.modal').load(createLink('refund', 'createTrade', 'refundID=' + response.refundID), '', function()
                            {
                                $('.modal').modal('ajustPosition', 'fit');
                            });
                            return false;
                        }

                    },
                    back:
                    {  
                        label: v.lang.no,
                        className: '',  
                        callback:  function(){location.reload();}  
                    }
                }  
            });
        }

        return false;
    })

    $('.panel-history a').click(function()
    {
        var href = $(this).prop('href');
        var app  = '';
        if(href.indexOf('/cash/') != -1) app = 'cash';

        if(app != '' && $(this).data('toggle') == undefined)
        {
            $.openEntry(app, href);
            return false;
        }
    });
})
