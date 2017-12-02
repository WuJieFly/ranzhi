$(function()
{
    if(v.mode == 'browse') $('#menu li').removeClass('active').find('[href*=' + v.status + ']').parent().addClass('active');
})
