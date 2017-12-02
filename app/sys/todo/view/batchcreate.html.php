<?php
/**
 * The batch create view of todo module of RanZhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Congzhi Chen <congzhi@cnezsoft.com>
 * @package     todo
 * @version     $Id: create.html.php 2741 2012-04-07 07:24:21Z areyou123456 $
 * @link        http://www.ranzhico.com
 */
?>
<?php include '../../../sys/common/view/header.modal.html.php';?>
<?php include '../../../sys/common/view/datepicker.html.php';?>
<?php include '../../../sys/common/view/chosen.html.php';?>
<form class='form-condensed' id='ajaxForm' method='post' action='<?php echo $this->createLink('todo', 'batchcreate')?>'>
  <div id='titlebar'>
    <div class='input-group w-200px' id='datepicker'>
      <span class='input-group-addon'><?php echo $lang->todo->date;?></span>
      <?php echo html::input('date', $date, "class='form-control form-date' onchange='updateAction(this.value)'");?>
      <span class='input-group-addon'><label class='checkbox-inline'><input type='checkbox' id='switchDate' onclick='switchDateTodo(this);'> <?php echo $lang->todo->periods['future'];?></label></span>
    </div>
  </div>
  <div class='panel'>
    <table class='table table-form table-fixed'>
      <thead>
        <tr class='text-center'>
          <th class='w-30px'><?php echo $lang->todo->id;?></th> 
          <th class='w-120px'><?php echo $lang->todo->type;?></th>
          <th class='w-80px'><?php echo $lang->todo->pri;?></th>
          <th class='w-100px'><?php echo $lang->todo->assignedTo;?></th>
          <th class='w-p30 red'><?php echo $lang->todo->name;?></th>
          <th><?php echo $lang->todo->desc;?></th>
          <th class='w-160px'><?php echo $lang->todo->beginAndEnd;?></th>
          <th class='w-70px'><label class='checkbox-inline'><input type='checkbox' name='switchAll' id='switchAll' onclick='switchDateAll(this)'> <?php echo $lang->todo->periods['future'];?></label></th>
        </tr>
      </thead>
      <?php $pri = 3;?>
      <?php $time = $date != date('Y-m-d') ? key($times) : $time;?>
      <?php for($i = 0; $i < $config->todo->batchCreate; $i++):?>
      <tr class='text-center'>
        <td><?php echo $i+1;?></td>
        <td><?php echo html::select("types[$i]", $lang->todo->typeList, '', "onchange='loadList(this.value, " . ($i + 1) . ")' class='form-control'");?></td>
        <td><?php echo html::select("pris[$i]", $lang->todo->priList, $pri, "class='form-control'");?></td>
        <td><?php echo html::select("assignedTo[$i]", $users, $pri, "class='form-control'");?></td>
        <td class='text-left' style='overflow:visible'>
          <div id='<?php echo "nameBox" . ($i+1);?>' class='hidden'><?php echo html::input("names[$i]", '', 'class="text-left form-control"');?></div>
          <div class='<?php echo "nameBox" . ($i+1);?>'><?php echo html::input("names[$i]", '', 'class="text-left form-control"');?></div>
        </td>
        <td><?php echo html::textarea("descs[$i]", '', "rows='1' class='form-control'");?></td>
        <td colspan='2'>
          <div class='input-group'>
            <?php echo html::select("begins[$i]", $times, $time, "onchange=\"setBeginsAndEnds($i, 'begin');\" class='form-control' style='width: 50%'") . html::select("ends[$i]", $times, '', "onchange=\"setBeginsAndEnds($i, 'end');\" class='form-control' style='width: 50%'");?>
            <span class='input-group-addon'><label class='checkbox-inline'><input type='checkbox' name="switchDate[<?php echo $i?>]" id="switchDate<?php echo $i?>" data-key="<?php echo $i;?>" onclick='switchDateList(<?php echo $i?>);'> <?php echo $lang->todo->periods['future'];?></label></span>
          </div>
        </td>
      </tr>  
      <?php endfor;?>
      <tfoot>
        <tr>
          <td></td>
          <td colspan='5'><?php echo html::submitButton() . ' ' . html::commonButton($lang->goback, 'btn', "data-dismiss='modal'");?></td>
        </tr>
      </tfoot>
    </table>
  </div>
</form>
<?php js::set('account', $app->user->account)?>
<script language='Javascript'>
var batchCreateNum = '<?php echo $config->todo->batchCreate;?>';
$(document).ready(function(){setBeginsAndEnds();});
function setBeginsAndEnds(i, beginOrEnd)
{
    if(typeof i == 'undefined')
    {
        for(j = 0; j < batchCreateNum; j++)
        {
            if(j != 0) $("[name=begins\\[" + j + '\\]]')[0].selectedIndex = $("[name=ends\\[" + (j - 1) + '\\]]')[0].selectedIndex;
            $("[name=ends\\[" + j + '\\]]')[0].selectedIndex = $("[name=begins\\[" + j + '\\]]')[0].selectedIndex + 3;
        }
    }
    else
    {
        if(beginOrEnd == 'begin')
        {
            $("[name=ends\\[" + i + '\\]]')[0].selectedIndex = $("[name=begins\\[" + i + '\\]]')[0].selectedIndex + 3;
        }
        for(j = i+1; j < batchCreateNum; j++)
        {
            $("[name=begins\\[" + j + '\\]]')[0].selectedIndex = $("[name=ends\\[" + (j - 1) + '\\]]')[0].selectedIndex;
            $("[name=ends\\[" + j + '\\]')[0].selectedIndex = $("[name=begins\\[" + j + '\\]]')[0].selectedIndex + 3;
        }
    }
}

</script>
<?php include '../../../sys/common/view/footer.modal.html.php';?>
