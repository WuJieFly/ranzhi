$(document).ready(function()
{    
    if(v.status == 'reviewed')
    {
        $('#menu .nav li').find('a[href*=' + v.status + ']').parent().addClass('active');
        $('#menu .nav li').find('[href*=unreviewed]').parent().removeClass('active');
    }
})
