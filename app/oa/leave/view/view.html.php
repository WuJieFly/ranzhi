<?php
/**
 * The detail view file of leave module of RanZhi.
 *
 * @copyright   Copyright 2009-2017 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Gang Liu <liugang@cnezsoft.com>
 * @package     leave 
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
?>
<?php include '../../../sys/common/view/header.modal.html.php';?>
<?php include '../../../sys/common/view/kindeditor.html.php';?>
<table class='table table-bordered'>
  <tr>
    <th><?php echo $lang->leave->status;?></th>
    <?php $status = ($leave->status == 'pass' and $leave->backDate != '0000-00-00 00:00:00' and $leave->backDate != $leave->end . ' ' . $leave->finish) ? 'back' : $leave->status;?>
    <td class='leave-<?php echo $status;?>'><?php echo $lang->leave->statusList[$status];?></td>
    <th><?php echo $lang->leave->type;?></th>
    <td><?php echo zget($lang->leave->typeList, $leave->type);?></td>
  </tr>
  <tr>
    <th><?php echo $lang->leave->start;?></th>
    <td><?php echo formatTime($leave->begin . ' ' . $leave->start, DT_DATETIME2);?></td>
    <th><?php echo $lang->leave->finish;?></th>
    <td><?php echo formatTime($leave->end . ' ' . $leave->finish, DT_DATETIME2);?></td>
  </tr>
  <tr>
    <th><?php echo $lang->leave->hours;?></th>
    <td><?php echo $leave->hours . $lang->leave->hoursTip;?></td>
    <th><?php echo $lang->leave->backDate;?></th>
    <td><?php echo formatTime($leave->backDate, DT_DATETIME2);?></td>
  </tr>
  <tr>
    <th><?php echo $lang->leave->desc;?></th>
    <td colspan='3'><?php echo $leave->desc;?></td>
  </tr>
  <tr>
    <th><?php echo $lang->leave->createdBy;?></th>
    <td><?php echo zget($users, $leave->createdBy);?></th>
    <th><?php echo $lang->leave->reviewedBy;?></th>
    <td><?php echo zget($users, $leave->reviewedBy);?></th>
  </tr>
  <tr>
    <th><?php echo $lang->leave->createdDate;?></th>
    <td><?php echo formatTime($leave->createdDate);?></td>
    <th><?php echo $lang->leave->reviewedDate;?></th>
    <td><?php echo formatTime($leave->reviewedDate);?></td>
  </tr>
</table>
<?php echo $this->fetch('action', 'history', "objectType=leave&objectID=$leave->id");?>
<div class='page-actions'>
  <?php 
  if($type == 'personal')
  {
      if($leave->status == 'pass' and date('Y-m-d H:i:s') > "$leave->begin $leave->start" && date('Y-m-d H:i:s') < "$leave->end $leave->finish" && $leave->backDate != "$leave->end $leave->finish")
      {
          commonModel::printLink('oa.leave', 'back', "id={$leave->id}", $lang->leave->back, "class='btn loadInModal'");
      }
      $switchLabel = $leave->status == 'wait' ? $lang->leave->cancel : $lang->leave->commit;
      if(strpos(',wait,draft,', ",$leave->status,") !== false)
      {
          commonModel::printLink('oa.leave', 'switchstatus', "id={$leave->id}", $switchLabel, "class='btn'");
      }
      if(strpos(',wait,draft,reject,', ",$leave->status,") !== false)
      {
          echo "<div class='btn-group'>";
          commonModel::printLink('oa.leave', 'edit',   "id={$leave->id}", $lang->edit,   "class='btn loadInModal'");
          commonModel::printLink('oa.leave', 'delete', "id={$leave->id}", $lang->delete, "class='btn deleteLeave'");
          echo '</div>';
      }
  }
  else
  {
      if($leave->status == 'wait')
      {
          commonModel::printLink('oa.leave', 'edit',   "id={$leave->id}", $lang->edit, "class='btn loadInModal'");
          echo "<div class='btn-group'>";
          commonModel::printLink('oa.leave', 'review', "id={$leave->id}&status=pass",   $lang->leave->statusList['pass'],   "class='btn reviewPass'");
          commonModel::printLink('oa.leave', 'review', "id={$leave->id}&status=reject", $lang->leave->statusList['reject'], "class='btn loadInModal'");
          echo '</div>';
      }
      elseif($leave->status == 'pass' and $leave->backDate != '0000-00-00 00:00:00' and $leave->backDate != "$leave->end $leave->finish")
      {
          echo "<div class='btn-group'>";
          commonModel::printLink('oa.leave', 'review', "id={$leave->id}&status=pass&mode=back",   $lang->leave->statusList['pass'],   "class='btn reviewPass'");
          commonModel::printLink('oa.leave', 'review', "id={$leave->id}&status=reject&mode=back", $lang->leave->statusList['reject'], "class='btn loadInModal'");
          echo '</div>';
      }
  }
  echo html::a('###', $lang->goback, "class='btn' data-dismiss='modal'");
  ?>
</div>
<?php include '../../../sys/common/view/footer.modal.html.php';?>
