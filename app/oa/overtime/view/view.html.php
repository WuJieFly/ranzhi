<?php
/**
 * The view file of overtime module of RanZhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Tingting Dai <daitingting@xirangit.com>
 * @package     overtime
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
?>
<?php include '../../../sys/common/view/header.modal.html.php';?>
<?php include '../../../sys/common/view/kindeditor.html.php';?>
<?php js::set('confirmReview', $lang->overtime->confirmReview)?>
<table class='table table-bordered'>
  <tr>
    <th><?php echo $lang->overtime->status;?></th>
    <td class='overtime-<?php echo $overtime->status;?>'><?php echo $lang->overtime->statusList[$overtime->status];?></td>
    <th><?php echo $lang->overtime->type?></th>
    <td><?php echo zget($lang->overtime->typeList, $overtime->type);?></td>
  </tr> 
  <tr>
    <th><?php echo $lang->overtime->begin?></th>
    <td><?php echo formatTime($overtime->begin . ' ' . $overtime->start, DT_DATETIME2);?></td>
    <th><?php echo $lang->overtime->end?></th>
    <td><?php echo formatTime($overtime->end . ' ' . $overtime->finish, DT_DATETIME2);?></td>
  </tr>
  <tr>
    <th><?php echo $lang->overtime->hours?></th>
    <td colspan='3'><?php echo $overtime->hours . $lang->overtime->hoursTip;?></td>
  </tr>
  <tr>
    <th><?php echo $lang->overtime->desc?></th>
    <td colspan='3'><?php echo $overtime->desc;?></td>
  </tr> 
  <?php if($overtime->status == 'reject' and $overtime->rejectReason):?>
  <tr>
    <th><?php echo $lang->overtime->rejectReason;?></th>
    <td colspan='3'><?php echo $overtime->rejectReason;?></td>
  </tr>
  <?php endif;?>
  <tr>
    <th><?php echo $lang->overtime->createdBy;?></th>
    <td><?php echo zget($users, $overtime->createdBy);?></td>
    <th><?php echo $lang->overtime->reviewedBy;?></th>
    <td><?php echo zget($users, $overtime->reviewedBy);?></td>
  </tr> 
  <tr>
    <th><?php echo $lang->overtime->createdDate;?></th>
    <td><?php echo formatTime($overtime->createdDate);?></td>
    <th><?php echo $lang->overtime->reviewedDate;?></th>
    <td><?php echo formatTime($overtime->reviewedDate);?></td>
  </tr> 
</table>
<?php echo $this->fetch('action', 'history', "objectType=overtime&objectID=$overtime->id");?>
<div class='page-actions'>
  <?php
  if($type == 'personal')
  {
      $switchLabel = $overtime->status == 'wait' ? $lang->overtime->cancel : $lang->overtime->commit;
      if(strpos(',wait,draft,', ",$overtime->status,") !== false)
      {
          commonModel::printLink('oa.overtime', 'switchstatus', "id=$overtime->id", $switchLabel, "class='switch-status btn'");
      }
      if(strpos(',wait,draft,reject,', ",$overtime->status,") !== false)
      {
          echo "<div class='btn-group'>";
          commonModel::printLink('oa.overtime', 'edit',   "id=$overtime->id", $lang->edit,   "class='btn loadInModal'");
          commonModel::printLink('oa.overtime', 'delete', "id=$overtime->id", $lang->delete, "class='btn deleteOvertime'");
          echo '</div>';
      }
  }
  elseif($overtime->status == 'wait')
  {
      commonModel::printLink('oa.overtime', 'review', "id=$overtime->id&status=pass",   $lang->overtime->statusList['pass'],   "class='btn reviewPass'");
      commonModel::printLink('oa.overtime', 'review', "id=$overtime->id&status=reject", $lang->overtime->statusList['reject'], "class='btn loadInModal'");
  }
  echo html::a('#', $lang->goback, "class='btn' data-dismiss='modal'");
  ?>
</div>
<?php include '../../../sys/common/view/footer.modal.html.php';?>
