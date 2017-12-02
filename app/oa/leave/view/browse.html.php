<?php
/**
 * The browse view file of leave module of Ranzhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      chujilu <chujilu@cnezsoft.com>
 * @package     leave
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../../sys/common/view/treeview.html.php';?>
<?php js::set('confirmReview', $lang->leave->confirmReview)?>
<div id='menuActions'>
  <?php commonModel::printLink('oa.leave', 'export', "mode=all&orderBy={$orderBy}", $lang->exportIcon . $lang->export, "class='btn btn-primary iframe' data-width='700'");?>
  <?php commonModel::printLink('oa.leave', 'create', '', "<i class='icon icon-plus'></i> {$lang->leave->create}", "data-toggle='modal' class='btn btn-primary'")?>
</div>
<?php if($type != 'browseReview'):?>
<div class='with-side'>
  <div class='side'>
    <div class='panel panel-sm'>
      <div class='panel-body'>
        <ul class='tree' data-collapsed='true'>
          <?php foreach($yearList as $year):?>
          <li class='<?php echo $year == $currentYear ? 'active' : ''?>'>
            <?php commonModel::printLink('oa.leave', $type, "date=$year", $year);?>
            <ul>
              <?php foreach($monthList[$year] as $month):?>
              <li class='<?php echo ($year == $currentYear and $month == $currentMonth) ? 'active' : ''?>'>
                <?php commonModel::printLink('oa.leave', $type, "date=$year$month", $year . $month);?>
              </li>
              <?php endforeach;?>
            </ul>
          </li>
          <?php endforeach;?>
        </ul>
      </div>
    </div>
  </div>
  <div class='main'>
<?php endif;?>
<?php $batchReview = $type == 'browseReview' && commonModel::hasPriv('leave', 'batchReview');?>
    <div class='panel'>
      <?php if($batchReview):?>
      <form id='ajaxForm' method='post' action='<?php echo inlink('batchReview', 'status=pass');?>'>
      <?php endif;?>
      <table class='table table-data table-hover text-center table-fixed tablesorter' id='leaveTable'>
        <thead>
          <tr class='text-center'>
            <?php $vars = "&date={$date}&orderBy=%s";?>
            <th class='w-50px'><?php commonModel::printOrderLink('id', $orderBy, $vars, $lang->leave->id);?></th>
            <th class='w-70px'><?php commonModel::printOrderLink('createdBy', $orderBy, $vars, $lang->leave->createdBy);?></th>
            <th class='w-70px visible-lg'><?php echo $lang->user->dept;?></th>
            <th class='w-60px'><?php commonModel::printOrderLink('type', $orderBy, $vars, $lang->leave->type);?></th>
            <th class='w-140px'><?php commonModel::printOrderLink('begin', $orderBy, $vars, $lang->leave->start);?></th>
            <th class='w-140px'><?php commonModel::printOrderLink('end', $orderBy, $vars, $lang->leave->finish);?></th>
            <th class='w-140px'><?php commonModel::printOrderLink('backDate', $orderBy, $vars, $lang->leave->backDate);?></th>
            <th class='w-60px visible-lg'><?php commonModel::printOrderLink('hours', $orderBy, $vars, $lang->leave->hours);?></th>
            <th><?php echo $lang->leave->desc;?></th>
            <th class='w-70px'><?php commonModel::printOrderLink('status', $orderBy, $vars, $lang->leave->status);?></th>
            <?php if($type != 'browseReview'):?>
            <th class='w-80px'><?php commonModel::printOrderLink('reviewedBy', $orderBy, $vars, $lang->leave->reviewedBy);?></th>
            <?php endif;?>
            <?php if($type == 'personal'):?>
            <th class='w-160px'><?php echo $lang->actions;?></th>
            <?php else:?>
            <th class='w-130px'><?php echo $lang->actions;?></th>
            <?php endif;?>
          </tr>
        </thead>
        <?php foreach($leaveList as $leave):?>
        <tr>
          <td class='idTD'>
            <?php if($batchReview):?>
            <label class='checkbox-inline'><input type='checkbox' name='leaveIDList[]' value='<?php echo $leave->id;?>'/> <?php echo $leave->id;?></label>
            <?php else:?>
            <?php echo $leave->id;?>
            <?php endif;?>
          </td>
          <td><?php echo zget($users, $leave->createdBy);?></td>
          <td class='visible-lg'><?php echo zget($deptList, $leave->dept);?></td>
          <td><?php echo zget($this->lang->leave->typeList, $leave->type);?></td>
          <td><?php echo formatTime($leave->begin . ' ' . $leave->start, DT_DATETIME2);?></td>
          <td><?php echo formatTime($leave->end . ' ' . $leave->finish, DT_DATETIME2);?></td>
          <td><?php echo formatTime($leave->backDate, DT_DATETIME2);?></td>
          <td class='visible-lg'><?php echo $leave->hours == 0 ? '' : $leave->hours;?></td>
          <td title='<?php echo $leave->desc;?>'><?php echo $leave->desc;?></td>
          <?php $status = ($leave->status == 'pass' and $leave->backDate != '0000-00-00 00:00:00' and $leave->backDate != $leave->end . ' ' . $leave->finish) ? 'back' : $leave->status;?>
          <td class='leave-<?php echo $status?>'><?php echo zget($this->lang->leave->statusList, $status);?></td>
          <?php if($type != 'browseReview'):?>
          <td><?php echo zget($users, $leave->reviewedBy);?></td>
          <?php endif;?>
          <td class='actionTD text-left'>
            <?php 
            commonModel::printLink('oa.leave', 'view', "id={$leave->id}&type=$type", $lang->detail, "data-toggle='modal'");
            if($type == 'personal')
            { 
                if($leave->status == 'pass' and date('Y-m-d H:i:s') > "$leave->begin $leave->start" && date('Y-m-d H:i:s') < "$leave->end $leave->finish" && $leave->backDate != "$leave->end $leave->finish") 
                {
                    commonModel::printLink('oa.leave', 'back', "id={$leave->id}", $lang->leave->back, "data-toggle='modal'");
                }
                else
                {
                    echo html::a('###', $lang->leave->back, "disabled='disabled'");
                }
                $switchLabel = $leave->status == 'wait' ? $lang->leave->cancel : $lang->leave->commit;
                if(strpos(',wait,draft,', ",$leave->status,") !== false)
                {
                    commonModel::printLink('oa.leave', 'switchstatus', "id={$leave->id}", $switchLabel, "class='reload'");
                }
                else
                {
                    echo html::a('###', $switchLabel,  "disabled='disabled'");
                }
                if(strpos(',wait,draft,reject,', ",$leave->status,") !== false)
                {
                    commonModel::printLink('oa.leave', 'edit',   "id={$leave->id}", $lang->edit,   "data-toggle='modal'");
                    commonModel::printLink('oa.leave', 'delete', "id={$leave->id}", $lang->delete, "class='deleter'");
                }
                else
                {
                    echo html::a('###', $lang->edit,   "disabled='disabled'");
                    echo html::a('###', $lang->delete, "disabled='disabled'");
                }
            }
            else
            {
                if($leave->status == 'wait')
                {
                    commonModel::printLink('oa.leave', 'edit',   "id={$leave->id}",               $lang->edit,                        "data-toggle='modal'");
                    commonModel::printLink('oa.leave', 'review', "id={$leave->id}&status=pass",   $lang->leave->statusList['pass'],   "class='reviewPass'");
                    commonModel::printLink('oa.leave', 'review', "id={$leave->id}&status=reject", $lang->leave->statusList['reject'], "data-toggle='modal'");
                }
                elseif($leave->status == 'pass' and $leave->backDate != '0000-00-00 00:00:00' and $leave->backDate != "$leave->end $leave->finish")
                {
                    echo html::a('###', $lang->edit, "disabled='disabled'");
                    commonModel::printLink('oa.leave', 'review', "id={$leave->id}&status=pass&mode=back",   $lang->leave->statusList['pass'],   "class='reviewPass'");
                    commonModel::printLink('oa.leave', 'review', "id={$leave->id}&status=reject&mode=back", $lang->leave->statusList['reject'], "data-toggle='modal'");
                }
                else
                {
                    echo html::a('###', $lang->edit,                        "disabled='disabled'");
                    echo html::a('###', $lang->leave->statusList['pass'],   "disabled='disabled'");
                    echo html::a('###', $lang->leave->statusList['reject'], "disabled='disabled'");
                }
            }
            ?>
          </td>
        </tr>
        <?php endforeach;?>
      </table>
      <?php if($leaveList && $batchReview):?>
      <div class='table-footer'>
        <div class='pull-left'>
          <?php echo html::selectButton();?>
          <?php echo html::a('javascript:;', $lang->leave->batchPass, "class='btn btn-primary batchPass'");?> 
        </div>
      </div>
      <?php endif;?>
    </div>
<?php if($type != 'browseReview'):?>
  </div>
</div>
<?php endif;?>
<?php include '../../common/view/footer.html.php';?>
