$(document).ready(function()
{
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
})
