<?php
/**
 * The detail view file of lieu module of RanZhi.
 *
 * @copyright   Copyright 2009-2017 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Gang Liu <liugang@cnezsoft.com>
 * @package     lieu
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
?>
<?php include '../../../sys/common/view/header.modal.html.php';?>
<?php include '../../../sys/common/view/kindeditor.html.php';?>
<table class='table table-bordered'>
  <tr>
    <th><?php echo $lang->lieu->status;?></th>
    <td class='lieu-<?php echo $lieu->status;?>'><?php echo zget($lang->lieu->statusList, $lieu->status);?></td>
    <th><?php echo $lang->lieu->hours;?></th>
    <td><?php echo $lieu->hours . $lang->lieu->hoursTip;?></td>
  </tr>
  <tr>
    <th><?php echo $lang->lieu->begin;?></th>
    <td><?php echo formatTime($lieu->begin . ' ' . $lieu->start, DT_DATETIME2);?></td>
    <th><?php echo $lang->lieu->end;?></th>
    <td><?php echo formatTime($lieu->end . ' ' . $lieu->finish, DT_DATETIME2);?></td>
  </tr>
  <tr>
    <th class='text-middle'><?php echo $lang->lieu->overtime;?></th>
    <td colspan='3'>
      <?php foreach(explode(',', trim($lieu->overtime, ',')) as $overtime):?>
      <?php if(!$overtime) continue;?>
      <?php echo zget($overtimePairs, $overtime) . '</br>';?>
      <?php endforeach;?>
    </td>
  </tr>
  <tr>
    <th><?php echo $lang->lieu->desc;?></th>
    <td colspan='3'><?php echo $lieu->desc;?></td>
  </tr>
  <tr>
    <th><?php echo $lang->lieu->createdBy;?></th>
    <td><?php echo zget($users, $lieu->createdBy);?></td>
    <th><?php echo $lang->lieu->reviewedBy;?></th>
    <td><?php echo zget($users, $lieu->reviewedBy);?></td>
  </tr>
  <tr>
    <th><?php echo $lang->lieu->createdDate;?></th>
    <td><?php echo formatTime($lieu->createdDate);?></td>
    <th><?php echo $lang->lieu->reviewedDate;?></th>
    <td><?php echo formatTime($lieu->reviewedDate);?></td>
  </tr>
</table>
<?php echo $this->fetch('action', 'history', "objectType=lieu&objectID=$lieu->id");?>
<div class='page-actions'>
  <?php 
  if($type == 'personal')
  {
      $switchLabel = $lieu->status == 'wait' ? $lang->lieu->cancel : $lang->lieu->commit;
      if(strpos(',wait,draft,', ",$lieu->status,") !== false)
      {
          commonModel::printLink('oa.lieu', 'switchstatus', "id={$lieu->id}", $switchLabel, "class='btn'");
      }
      if(strpos(',wait,draft,reject,', ",$lieu->status,") !== false)
      {
          echo "<div class='btn-group'>";
          commonModel::printLink('oa.lieu', 'edit',   "id={$lieu->id}", $lang->edit,   "class='btn loadInModal'");
          commonModel::printLink('oa.lieu', 'delete', "id={$lieu->id}", $lang->delete, "class='btn deleteLieu'");
          echo '</div>';
      }
  }
  elseif($lieu->status == 'wait')
  {
      commonModel::printLink('oa.lieu', 'review', "id={$lieu->id}status=pass",   $lang->lieu->status['pass'],   "class='btn reviewPass'");
      commonModel::printLink('oa.lieu', 'review', "id={$lieu->id}status=reject", $lang->lieu->status['reject'], "class='btn loadInModal'");
  }
  echo html::a('###', $lang->goback, "class='btn' data-dismiss='modal'");
  ?>
</div>
<?php include '../../../sys/common/view/footer.modal.html.php';?>
