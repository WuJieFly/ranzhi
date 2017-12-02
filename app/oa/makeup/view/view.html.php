<?php
/**
 * The view file of makeup module of RanZhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Tingting Dai <daitingting@xirangit.com>
 * @package     makeup
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
?>
<?php include '../../../sys/common/view/header.modal.html.php';?>
<?php include '../../../sys/common/view/kindeditor.html.php';?>
<?php js::set('confirmReview', $lang->makeup->confirmReview)?>
<table class='table table-bordered'>
  <tr>
    <th><?php echo $lang->makeup->status;?></th>
    <td class='makeup-<?php echo $makeup->status;?>'><?php echo $lang->makeup->statusList[$makeup->status];?></td>
    <th><?php echo $lang->makeup->hours?></th>
    <td><?php echo $makeup->hours . $lang->makeup->hoursTip;?></td>
  </tr> 
  <tr>
    <th><?php echo $lang->makeup->begin?></th>
    <td><?php echo formatTime($makeup->begin . ' ' . $makeup->start, DT_DATETIME2);?></td>
    <th><?php echo $lang->makeup->end?></th>
    <td><?php echo formatTime($makeup->end . ' ' . $makeup->finish, DT_DATETIME2);?></td>
  </tr>
  <tr>
    <th class='text-middle'><?php echo $lang->makeup->leave;?></th>
    <td colspan='3'>
      <?php foreach(explode(',', trim($makeup->leave, ',')) as $leave):?>
      <?php if(!$leave) continue;?>
      <?php echo zget($leavePairs, $leave) . '</br>';?>
      <?php endforeach;?>
    </td>
  </tr>
  <tr>
    <th><?php echo $lang->makeup->desc?></th>
    <td colspan='3'><?php echo $makeup->desc;?></td>
  </tr> 
  <?php if($makeup->status == 'reject' and $makeup->rejectReason):?>
  <tr>
    <th><?php echo $lang->makeup->rejectReason;?></th>
    <td colspan='3'><?php echo $makeup->rejectReason;?></td>
  </tr>
  <?php endif;?>
  <tr>
    <th><?php echo $lang->makeup->createdBy;?></th>
    <td><?php echo zget($users, $makeup->createdBy);?></td>
    <th><?php echo $lang->makeup->reviewedBy;?></th>
    <td><?php echo zget($users, $makeup->reviewedBy);?></td>
  </tr> 
  <tr>
    <th><?php echo $lang->makeup->createdDate;?></th>
    <td><?php echo formatTime($makeup->createdDate);?></td>
    <th><?php echo $lang->makeup->reviewedDate;?></th>
    <td><?php echo formatTime($makeup->reviewedDate);?></td>
  </tr> 
</table>
<?php echo $this->fetch('action', 'history', "objectType=makeup&objectID=$makeup->id");?>
<div class='page-actions'>
  <?php
  if($type == 'personal')
  {
      $switchLabel = $makeup->status == 'wait' ? $lang->makeup->cancel : $lang->makeup->commit;
      if(strpos(',wait,draft,', ",$makeup->status,") !== false) 
      {
          commonModel::printLink('oa.makeup', 'switchstatus', "id=$makeup->id", $switchLabel, "class='switch-status btn'");
      }
      if(strpos(',wait,draft,reject,', ",$makeup->status,") !== false) 
      {
          echo "<div class='btn-group'>";
          commonModel::printLink('oa.makeup', 'edit',   "id=$makeup->id", $lang->edit,   "class='btn loadInModal'");
          commonModel::printLink('oa.makeup', 'delete', "id=$makeup->id", $lang->delete, "class='btn deleteMakeup'");
          echo '</div>';
      }
  }
  elseif($makeup->status == 'wait')
  {
      echo "<div class='btn-group'>";
      commonModel::printLink('oa.makeup', 'review', "id=$makeup->id&status=pass",   $lang->makeup->statusList['pass'],   "class='btn reviewPass'");
      commonModel::printLink('oa.makeup', 'review', "id=$makeup->id&status=reject", $lang->makeup->statusList['reject'], "class='btn loadInModal'");
      echo '</div>';
  }
  echo html::a('#', $lang->goback, "class='btn' data-dismiss='modal'");
  ?>
</div>
<?php include '../../../sys/common/view/footer.modal.html.php';?>
