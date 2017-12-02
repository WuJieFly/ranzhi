<?php
/**
 * The browse view file of overtime module of Ranzhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Tingting Dai <daitingting@xirangit.com>
 * @package     overtime
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../../sys/common/view/treeview.html.php';?>
<?php js::set('confirmReview', $lang->overtime->confirmReview)?>
<div id='menuActions'>
  <?php commonModel::printLink('oa.overtime', 'export', "mode=all&orderBy={$orderBy}", $lang->exportIcon . $lang->export, "class='btn btn-primary iframe' data-width='700'");?>
  <?php commonModel::printLink('oa.overtime', 'create', "", "<i class='icon icon-plus'></i> {$lang->overtime->create}", "data-toggle='modal' class='btn btn-primary'")?>
</div>
<?php if($type != 'browseReview'):?>
<div class='with-side'>
  <div class='side'>
    <div class='panel panel-sm'>
      <div class='panel-body'>
        <ul class='tree' data-collapsed='true'>
          <?php foreach($yearList as $year):?>
          <li class='<?php echo $year == $currentYear ? 'active' : ''?>'>
            <?php commonModel::printLink('oa.overtime', $type, "date=$year", $year);?>
            <ul>
              <?php foreach($monthList[$year] as $month):?>
              <li class='<?php echo ($year == $currentYear and $month == $currentMonth) ? 'active' : ''?>'>
                <?php commonModel::printLink('oa.overtime', $type, "date=$year$month", $year . $month);?>
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
<?php $batchReview = $type == 'browseReview' && commonModel::hasPriv('overtime', 'batchReview');?>
    <div class='panel'>
      <?php if($batchReview):?>
      <form id='ajaxForm' method='post' action='<?php echo inlink('batchReview', 'status=pass');?>'>
      <?php endif;?>
      <table class='table table-data table-hover text-center table-fixed tablesorter' id='overtimeTable'>
        <thead>
          <tr class='text-center'>
            <?php $vars = "&date={$date}&orderBy=%s";?>
            <th class='w-80px'><?php commonModel::printOrderLink('id', $orderBy, $vars, $lang->overtime->id);?></th>
            <th class='w-80px'><?php commonModel::printOrderLink('createdBy', $orderBy, $vars, $lang->overtime->createdBy);?></th>
            <th class='w-80px visible-lg'><?php echo $lang->user->dept;?></th>
            <th class='w-80px'><?php commonModel::printOrderLink('type', $orderBy, $vars, $lang->overtime->type);?></th>
            <th class='w-150px'><?php commonModel::printOrderLink('begin', $orderBy, $vars, $lang->overtime->begin);?></th>
            <th class='w-150px'><?php commonModel::printOrderLink('end', $orderBy, $vars, $lang->overtime->end);?></th>
            <th class='w-50px visible-lg'><?php commonModel::printOrderLink('hours', $orderBy, $vars, $lang->overtime->hours);?></th>
            <th><?php echo $lang->overtime->desc;?></th>
            <th class='w-80px'><?php commonModel::printOrderLink('status', $orderBy, $vars, $lang->overtime->status);?></th>
            <?php if($type != 'browseReview'):?>
            <th class='w-80px'><?php commonModel::printOrderLink('reviewedBy', $orderBy, $vars, $lang->overtime->reviewedBy);?></th>
            <?php endif;?>
            <?php if($type == 'personal'):?>
            <th class='w-130px'><?php echo $lang->actions;?></th>
            <?php else:?>
            <th class='w-100px'><?php echo $lang->actions;?></th>
            <?php endif;?>
          </tr>
        </thead>
        <?php foreach($overtimeList as $overtime):?>
        <tr>
          <td class='idTD'>
            <?php if($batchReview):?>
            <label class='checkbox-inline'><input type='checkbox' name='overtimeIDList[]' value='<?php echo $overtime->id;?>'/> <?php echo $overtime->id;?></label>
            <?php else:?>
            <?php echo $overtime->id;?>
            <?php endif;?>
          </td>
          <td><?php echo zget($users, $overtime->createdBy);?></td>
          <td class='visible-lg'><?php echo zget($deptList, $overtime->dept);?></td>
          <td><?php echo zget($this->lang->overtime->typeList, $overtime->type);?></td>
          <td><?php echo formatTime($overtime->begin . ' ' . $overtime->start, DT_DATETIME2);?></td>
          <td><?php echo formatTime($overtime->end . ' ' . $overtime->finish, DT_DATETIME2);?></td>
          <td class='visible-lg'><?php echo $overtime->hours == 0 ? '' : $overtime->hours;?></td>
          <td title='<?php echo $overtime->desc?>'><?php echo $overtime->desc;?></td>
          <td class='overtime-<?php echo $overtime->status?>'><?php echo zget($this->lang->overtime->statusList, $overtime->status);?></td>
          <?php if($type != 'browseReview'):?>
          <td><?php echo zget($users, $overtime->reviewedBy);?></td>
          <?php endif;?>
          <td class='actionTD text-left'>
            <?php 
            commonModel::printLink('oa.overtime', 'view', "id=$overtime->id&type=$type", $lang->detail, "data-toggle='modal'");
            if($type == 'personal')
            {
                $switchLabel = $overtime->status == 'wait' ? $lang->overtime->cancel : $lang->overtime->commit;
                if(strpos(',wait,draft,', ",$overtime->status,") !== false)
                {
                    commonModel::printLink('oa.overtime', 'switchstatus', "id=$overtime->id", $switchLabel, "class='reload'");
                }
                else
                {
                    echo html::a('###', $switchLabel,  "disabled='disabled'");
                }
                if(strpos(',wait,draft,reject,', ",$overtime->status,") !== false)
                {
                    commonModel::printLink('oa.overtime', 'edit',   "id=$overtime->id", $lang->edit,   "data-toggle='modal'");
                    commonModel::printLink('oa.overtime', 'delete', "id=$overtime->id", $lang->delete, "class='deleter'");
                }
                else
                {
                    echo html::a('###', $lang->edit,   "disabled='disabled'");
                    echo html::a('###', $lang->delete, "disabled='disabled'");
                }
            }
            else
            {
                if($overtime->status == 'wait')
                {
                    commonModel::printLink('oa.overtime', 'review', "id=$overtime->id&status=pass",   $lang->overtime->statusList['pass'],   "class='reviewPass'");
                    commonModel::printLink('oa.overtime', 'review', "id=$overtime->id&status=reject", $lang->overtime->statusList['reject'], "data-toggle='modal'");
                }
                else
                {
                    echo html::a('###', $lang->overtime->statusList['pass'],   "disabled='disabled'");
                    echo html::a('###', $lang->overtime->statusList['reject'], "disabled='disabled'");
                }
            }
            ?>
          </td>
        </tr>
        <?php endforeach;?>
      </table>
      <?php if($overtimeList && $batchReview):?>
      <div class='table-footer'>
        <div class='pull-left'>
          <?php echo html::selectButton();?>
          <?php echo html::a('javascript:;', $lang->overtime->batchPass, "class='btn btn-primary batchPass'");?> 
        </div>
      </div>
      <?php endif;?>
    </div>
<?php if($type != 'browseReview'):?>
  </div>
</div>
<?php endif;?>
<?php include '../../common/view/footer.html.php';?>
