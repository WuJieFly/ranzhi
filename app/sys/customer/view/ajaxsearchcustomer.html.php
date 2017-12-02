<?php
/**
 * The search customer view file of customer module of RanZhi.
 *
 * @copyright   Copyright 2009-2017 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Gang Liu <liugang@cnezsoft.com>
 * @package     customer 
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
?>
<?php include '../../common/view/header.modal.html.php';?>
<style>
.searchInput {margin: 10px 30px 10px 30px;}
.searchInput .icon {position: absolute; display: block; left: 39px; top: 19px; z-index: 5; color: #808080;}
#customerSearchInput {padding-left: 30px;}

.modal-body {height: 300px; overflow-y: auto; padding: 0;}
#searchResult {padding-left: 0; list-style: none; width: 100%}
#searchResult > li {display: block}
#searchResult > li.tip {padding: 6px 30px; color: #808080}
#searchResult > li.loading {text-align: center; padding: 50px}
#searchResult > li.loading > .icon-spinner:before {font-size: 28px;}
#searchResult > li > a {display: block; padding: 6px 30px; color: #333; border-bottom: 1px solid #e5e5e5}
#searchResult > li > a:hover, #searchResult > li > a.selected {color: #1a4f85; background-color: #ddd;}
</style>
<div class='searchInput '>
  <?php echo html::input('customerSearchInput', $key, "class='form-control' placeholder='{$lang->customer->searchInput}'");?>
  <?php echo html::hidden('relation', $relation);?>
  <?php echo html::hidden('customers', $customers);?>
  <i class='icon icon-search'></i>
</div>
<ul id='searchResult'></ul>
<script>
var lastSearchFn  = false;
var $searchInput  = $('#customerSearchInput');
var $searchResult = $('#searchResult');

$searchResult.on('click', 'a', function(){selectItem(this);}).on('mouseenter', 'a', function()
{
    $searchResult.find('a.selected').removeClass('selected');
    $(this).addClass('selected');
}).on('mouseleave', 'a', function()
{
    $(this).removeClass('selected');
});

$searchInput.on('paste change keyup', function()
{
    if(lastSearchFn) clearTimeout(lastSearchFn);
    lastSearchFn = setTimeout(function()
    {
        var key      = $searchInput.val() || '';
        var relation = $('#relation').val() || '';
        if(key && key != $searchInput.data('lastkey'))
        {
            $searchResult.empty().append('<li class="loading"><i class="icon-spin icon-spinner icon-2x"></i></li>');
            var branch = $('#branch').val();
            if(typeof(branch) == 'undefined') branch = 0;
            var link = createLink('customer', 'ajaxGetPairs', 'key=' + key + '&relation=' + relation);
            $.getJSON(link, function(result)
            {
                $searchResult.empty();
                if(result)
                {
                    for(var key in result)
                    {
                        if(key === 'info')
                        {
                            $searchResult.append('<li class="tip">' + result[key] + '</li>');
                        }
                        else
                        {
                            $searchResult.append("<li><a href='javascript:;' data-key='" + key + "'>" + result[key] + "</a></li>");
                        }
                    }
                    $searchResult.find('li:first > a').addClass('selected');
                }
            });
            $searchInput.data('lastkey', key);
        }
        else if(!key.length)
        {
            $searchResult.empty();
        }
    }, 500);
}).on('keyup', function(e)
{
    var $selected = $searchResult.find('a.selected').first();
    if(e.keyCode == 38) // keyup
    {
        var $prev = $selected.closest('li').prev().children('a');
        if($prev.length)
        {
            $selected.removeClass('selected');
            $prev.addClass('selected');
        }
    }
    else if(e.keyCode == 40) // keydown
    {
        var $next = $selected.closest('li').next().children('a');
        if($next.length)
        {
            $selected.removeClass('selected');
            $next.addClass('selected');
        }
    }
    else if(e.keyCode == 13) selectItem($selected);
});

$searchInput.change().focus();
</script>
<?php include '../../common/view/footer.modal.html.php';?>
