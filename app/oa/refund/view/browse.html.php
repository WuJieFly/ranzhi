<?php 
/**
 * The browse view file of order module of RanZhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Tingting Dai <daitingting@xirangit.com>
 * @package     order 
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php js::set('mode', $type);?>
<?php js::set('createTradeTip', $lang->refund->createTradeTip);?>
<li id='bysearchTab'><?php echo html::a('#', "<i class='icon-search icon'></i>" . $lang->search->common)?></li>
<?php $secondReviewerClass = empty($this->config->refund->secondReviewer) ? 'hidden' : '';?>
<div id='menuActions'>
  <?php if(commonModel::hasPriv('refund', 'export')):?>
  <div class='btn-group'>
    <button data-toggle='dropdown' class='btn btn-primary dropdown-toggle' type='button'><?php echo $lang->exportIcon . $lang->export;?> <span class='caret'></span></button>
    <ul id='exportActionMenu' class='dropdown-menu'>
      <li><?php commonModel::printLink('refund', 'export', "mode=all&orderBy={$orderBy}", $lang->exportAll, "class='iframe' data-width='700'");?></li>
      <li><?php commonModel::printLink('refund', 'export', "mode=thisPage&orderBy={$orderBy}", $lang->exportThisPage, "class='iframe' data-width='700'");?></li>
    </ul>
  </div>
  <?php endif;?>
  <?php commonModel::printLink('refund', 'create', '', '<i class="icon-plus"></i> ' . $lang->refund->create, 'class="btn btn-primary"');?>
</div>
<?php if($mode != 'todo'):?>
<div class='with-side'>
  <div class='side'>
    <div class='panel panel-sm'>
      <div class='panel-body'>
        <ul class='tree' data-collapsed='true'>
          <?php foreach($yearList as $year):?>
          <li class='<?php echo $year == $currentYear ? 'active' : ''?>'>
            <?php commonModel::printLink('refund', $mode, "date=$year", $year);?>
            <ul>
              <?php foreach($monthList[$year] as $month):?>
              <li class='<?php echo ($year == $currentYear and $month == $currentMonth) ? 'active' : ''?>'>
                <?php commonModel::printLink('refund', $mode, "date=$year$month", $year . $month);?>
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
    <div class='panel'>
      <table class='table table-hover table-striped tablesorter table-data table-fixed text-center' id='refundTable'>
        <thead>
          <tr class='text-center'>
            <?php $vars = "date=$date&type=&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}";?>
            <th class='w-50px'><?php commonModel::printOrderLink('id', $orderBy, $vars, $lang->refund->id);?></th>
            <th class='w-80px text-left visible-lg'><?php commonModel::printOrderLink('dept', $orderBy, $vars, $lang->refund->dept);?></th>
            <th><?php commonModel::printOrderLink('name', $orderBy, $vars, $lang->refund->name);?></th>
            <th class='w-120px'><?php commonModel::printOrderLink('category', $orderBy, $vars, $lang->refund->category);?></th>
            <th class='w-100px text-right'><?php commonModel::printOrderLink('money', $orderBy, $vars, $lang->refund->money);?></th>
            <th class='w-80px'><?php commonModel::printOrderLink('status', $orderBy, $vars, $lang->refund->status);?></th>
            <th class='w-80px'><?php commonModel::printOrderLink('createdBy', $orderBy, $vars, $lang->refund->createdBy);?></th>
            <th class='w-80px'><?php commonModel::printOrderLink('createdDate', $orderBy, $vars, $lang->refund->createdDate);?></th>
            <th class='w-80px'><?php commonModel::printOrderLink('firstReviewer', $orderBy, $vars, $lang->refund->reviewer);?></th>
            <th class='w-80px'><?php commonModel::printOrderLink('refundBy', $orderBy, $vars, $lang->refund->refundBy);?></th>
            <th class='w-80px'><?php commonModel::printOrderLink('refundDate', $orderBy, $vars, $lang->refund->refundDate);?></th>
            <?php if($mode == 'personal'):?>
            <th class='w-130px'><?php echo $lang->actions;?></th>
            <?php elseif($mode == 'todo'):?>
            <th class='w-80px'><?php echo $lang->actions;?></th>
            <?php else:?>
            <th class='w-40px'><?php echo $lang->actions;?></th>
            <?php endif;?>
          </tr>
        </thead>
        <?php foreach($refunds as $refund):?>
        <tr data-url='<?php echo $this->createLink('refund', 'view', "refundID={$refund->id}&mode={$mode}");?>'>
          <td><?php echo $refund->id;?></td>
          <td class='text-left visible-lg'><?php echo zget($deptList, $refund->dept);?></td>
          <td class='text-left' title='<?php echo $refund->name;?>'><?php echo $refund->name?></td>
          <td class='text-left' title='<?php echo zget($categories, $refund->category);?>'><?php echo zget($categories, $refund->category, ' ');?></td>
          <td class='text-right'><?php echo zget($currencySign, $refund->currency) . $refund->money?></td>
          <td class='refund-<?php echo $refund->status?>'><?php echo zget($lang->refund->statusList, $refund->status)?></td>
          <td><?php echo zget($userPairs, $refund->createdBy);?></td>
          <td><?php echo formatTime($refund->createdDate, DT_DATE1);?></td>
          <td><?php echo zget($userPairs, $refund->firstReviewer) . ' ' . zget($userPairs, $refund->secondReviewer);?></td>
          <td><?php echo zget($userPairs, $refund->refundBy);?></td>
          <td><?php echo formatTime($refund->refundDate, DT_DATE1)?></td>
          <td class='text-left'>
            <?php 
            echo html::a($this->createLink('refund', 'view', "refundID={$refund->id}&mode={$mode}"), $lang->detail);
            if($mode == 'personal')
            {
                $switchLabel = $refund->status == 'wait' ? $lang->refund->cancel : $lang->refund->commit;
                if($this->app->user->admin == 'super' or $refund->createdBy == $this->app->user->account)
                {
                    if(strpos(',wait,draft,', ",$refund->status,") !== false)
                    {
                        echo html::a($this->createLink('refund', 'switchstatus', "id=$refund->id"), $switchLabel, "class='reload'");
                    }
                    else
                    {
                        echo html::a('javascript:;', $switchLabel, "class='disabled'");
                    }
                    if(strpos(',wait,draft,reject,', ",$refund->status,") !== false)
                    {
                        echo html::a($this->createLink('refund', 'edit',   "refundID={$refund->id}"), $lang->edit);
                        echo html::a($this->createLink('refund', 'delete', "refundID={$refund->id}"), $lang->delete, "class='deleter'");
                    }
                    else
                    {
                        echo html::a('javascript:;', $lang->edit,   "class='disabled'");
                        echo html::a('javascript:;', $lang->delete, "class='disabled'");
                    }
                }
                else
                {
                    echo html::a('javascript:;', $switchLabel,  "class='disabled'");
                    echo html::a('javascript:;', $lang->edit,   "class='disabled'");
                    echo html::a('javascript:;', $lang->delete, "class='disabled'");
                }
            }
            if($mode == 'todo') echo html::a($this->createLink('refund', 'reimburse', "refundID={$refund->id}"), $lang->refund->common, "class='refund'");
            ?>
          </td>
        </tr>
        <?php endforeach;?>
      </table>
      <div class='table-footer'>
        <?php $totalMoney = $this->refund->total($refunds);?>
        <?php if($totalMoney):?>
        <div class='pull-left text-danger'><?php echo $lang->refund->total . $totalMoney;?></div>
        <?php endif;?>
        <?php $pager->show();?>
      </div>
    </div>
<?php if($mode != 'todo'):?>
  </div>
</div>
<?php endif;?>
<?php include '../../common/view/footer.html.php';?>
