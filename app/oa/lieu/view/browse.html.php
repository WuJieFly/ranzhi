<?php
/**
 * The browse view file of lieu module of Ranzhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Tingting Dai <daitingting@xirangit.com>
 * @package     lieu
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../../sys/common/view/treeview.html.php';?>
<?php js::set('confirmReview', $lang->lieu->confirmReview)?>
<div id='menuActions'>
  <?php commonModel::printLink('oa.lieu', 'create', "", "<i class='icon icon-plus'></i> {$lang->lieu->create}", "data-toggle='modal' class='btn btn-primary'")?>
</div>
<?php if($type != 'browseReview'):?>
<div class='with-side'>
  <div class='side'>
    <div class='panel panel-sm'>
      <div class='panel-body'>
        <ul class='tree' data-collapsed='true'>
          <?php foreach($yearList as $year):?>
          <li class='<?php echo $year == $currentYear ? 'active' : ''?>'>
            <?php commonModel::printLink('oa.lieu', $type, "date=$year", $year);?>
            <ul>
              <?php foreach($monthList[$year] as $month):?>
              <li class='<?php echo ($year == $currentYear and $month == $currentMonth) ? 'active' : ''?>'>
                <?php commonModel::printLink('oa.lieu', $type, "date=$year$month", $year . $month);?>
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
<?php $batchReview = $type == 'browseReview' && commonModel::hasPriv('lieu', 'batchReview');?>
    <div class='panel'>
      <?php if($batchReview):?>
      <form id='ajaxForm' method='post' action='<?php echo inlink('batchReview', 'status=pass');?>'>
      <?php endif;?>
      <table class='table table-data table-hover text-center table-fixed tablesorter' id='lieuTable'>
        <thead>
          <tr class='text-center'>
            <?php $vars = "&date={$date}&orderBy=%s";?>
            <th class='w-50px'><?php commonModel::printOrderLink('id', $orderBy, $vars, $lang->lieu->id);?></th>
            <th class='w-70px'><?php commonModel::printOrderLink('createdBy', $orderBy, $vars, $lang->lieu->createdBy);?></th>
            <th class='w-70px visible-lg'><?php echo $lang->user->dept;?></th>
            <th class='w-130px'><?php commonModel::printOrderLink('begin', $orderBy, $vars, $lang->lieu->begin);?></th>
            <th class='w-130px'><?php commonModel::printOrderLink('end', $orderBy, $vars, $lang->lieu->end);?></th>
            <th class='w-60px'><?php commonModel::printOrderLink('hours', $orderBy, $vars, $lang->lieu->hours);?></th>
            <th><?php echo $lang->lieu->desc;?></th>
            <th class='w-70px'><?php commonModel::printOrderLink('status', $orderBy, $vars, $lang->lieu->status);?></th>
            <?php if($type != 'browseReview'):?>
            <th class='w-80px'><?php commonModel::printOrderLink('reviewedBy', $orderBy, $vars, $lang->lieu->reviewedBy);?></th>
            <?php endif;?>
            <?php if($type == 'personal'):?>
            <th class='w-130px'><?php echo $lang->actions;?></th>
            <?php else:?>
            <th class='w-100px'><?php echo $lang->actions;?></th>
            <?php endif;?>
          </tr>
        </thead>
        <?php foreach($lieuList as $lieu):?>
        <tr>
          <td class='idTD'>
            <?php if($batchReview):?>
            <label class='checkbox-inline'><input type='checkbox' name='lieuIDList[]' value='<?php echo $lieu->id;?>'/> <?php echo $lieu->id;?></label>
            <?php else:?>
            <?php echo $lieu->id;?>
            <?php endif;?>
          </td>
          <td><?php echo zget($users, $lieu->createdBy);?></td>
          <td class='visible-lg'><?php echo zget($deptList, $lieu->dept, '');?></td>
          <td><?php echo formatTime($lieu->begin . ' ' . $lieu->start, DT_DATETIME2);?></td>
          <td><?php echo formatTime($lieu->end . ' ' . $lieu->finish, DT_DATETIME2);?></td>
          <td><?php echo $lieu->hours;?></td>
          <td title='<?php echo $lieu->desc;?>'><?php echo $lieu->desc;?></td>
          <td class='lieu-<?php echo $lieu->status?>'><?php echo zget($this->lang->lieu->statusList, $lieu->status);?></td>
          <?php if($type != 'browseReview'):?>
          <td><?php echo zget($users, $lieu->reviewedBy);?></td>
          <?php endif;?>
          <td class='actionTD text-left'>
            <?php
            commonModel::printLink('oa.lieu', 'view', "id={$lieu->id}&type=$type", $lang->lieu->view, "data-toggle='modal'");
            if($type == 'personal')
            {
                $switchLabel = $lieu->status == 'wait' ? $lang->lieu->cancel : $lang->lieu->commit;
                if(strpos(',wait,draft,', ",$lieu->status,") !== false)
                {
                    commonModel::printLink('oa.lieu', 'switchstatus', "id={$lieu->id}", $switchLabel, "class='reload'");
                }
                else
                {
                    echo html::a('###', $switchLabel,  "disabled='disabled'");
                }
                if(strpos(',wait,draft,reject,', ",$lieu->status,") !== false)
                {
                    commonModel::printLink('oa.lieu', 'edit',   "id={$lieu->id}", $lang->edit,   "data-toggle='modal'");
                    commonModel::printLink('oa.lieu', 'delete', "id={$lieu->id}", $lang->delete, "class='deleter'");
                }
                else
                {
                    echo html::a('###', $lang->edit,   "disabled='disabled'");
                    echo html::a('###', $lang->delete, "disabled='disabled'");
                }
            }
            else
            {
                if($lieu->status == 'wait')
                {
                    commonModel::printLink('oa.lieu', 'review', "id={$lieu->id}&status=pass",   $lang->lieu->statusList['pass'],   "class='reviewPass'");
                    commonModel::printLink('oa.lieu', 'review', "id={$lieu->id}&status=reject", $lang->lieu->statusList['reject'], "data-toggle='modal'");
                }
                else
                {
                    echo html::a('###', $lang->lieu->statusList['pass'],   "disabled='diasbled'");
                    echo html::a('###', $lang->lieu->statusList['reject'], "disabled='diasbled'");
                }
            }
            ?>
          </td>
        </tr>
        <?php endforeach;?>
      </table>
      <?php if($lieuList && $batchReview):?>
      <div class='table-footer'>
        <div class='pull-left'>
          <?php echo html::selectButton();?>
          <?php echo html::a('javascript:;', $lang->lieu->batchPass, "class='btn btn-primary batchPass'");?> 
        </div>
      </div>
      <?php endif;?>
    </div>
<?php if($type != 'browseReview'):?>
  </div>
</div>
<?php endif;?>
<?php include '../../common/view/footer.html.php';?>
