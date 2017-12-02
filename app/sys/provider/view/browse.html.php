<?php 
/**
 * The browse view file of provider module of RanZhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Tingting Dai <daitingting@xirangit.com>
 * @package     provider 
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
?>
<?php include $app->appRoot . 'common/view/header.html.php';?>
<div id='menuActions'>
  <?php commonModel::printLink('provider', 'create', '', '<i class="icon-plus"></i> ' . $lang->provider->create, 'class="btn btn-primary"');?>
</div>
<div class='with-side'>
  <div class='side panel'>
    <div class='panel-heading'>
      <strong><?php echo $lang->provider->category;?></strong>
    </div>
    <div class='panel-body'>
      <div id='treeMenuBox'><?php echo $treeMenu;?></div>
      <?php commonModel::printLink('tree', 'browse', 'type=provider', $lang->provider->setCategory, "class='btn btn-primary setting'");?>
    </div>
  </div>
  <div class='main panel'>
    <table class='table table-bordered table-hover table-striped tablesorter table-data table-fixedHeader'>
      <thead>
        <tr class='text-center'>
          <?php $vars = "mode=all&param=&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}";?>
          <th class='w-60px'> <?php commonModel::printOrderLink('id',   $orderBy, $vars, $lang->provider->id);?></th>
          <th>                <?php commonModel::printOrderLink('name', $orderBy, $vars, $lang->provider->name);?></th>
          <th class='w-110px'><?php commonModel::printOrderLink('size', $orderBy, $vars, $lang->provider->size);?></th>
          <th class='w-70px'> <?php commonModel::printOrderLink('type', $orderBy, $vars, $lang->provider->type);?></th>
          <th class='w-160px text-left'><?php commonModel::printOrderLink('area',     $orderBy, $vars, $lang->provider->area);?></th>
          <th class='w-150px text-left'><?php commonModel::printOrderLink('industry', $orderBy, $vars, $lang->provider->industry);?></th>
          <th class='w-100px'><?php commonModel::printOrderLink('createdDate', $orderBy, $vars, $lang->provider->createdDate);?></th>
          <th class='w-150px'><?php echo $lang->actions;?></th>
        </tr>
      </thead>
      <tbody>
        <?php $areas[0] = '';?>
        <?php $industries[0] = '';?>
        <?php foreach($providers as $provider):?>
        <tr class='text-center' data-url='<?php echo $this->createLink('provider', 'view', "providerID=$provider->id"); ?>'>
          <td><?php echo $provider->id;?></td>
          <td class='text-left'><?php echo $provider->name;?></td>
          <td><?php echo $lang->provider->sizeList[$provider->size];?></td>
          <td><?php echo $lang->provider->typeList[$provider->type];?></td>
          <td class='text-left'><?php echo zget($areas, $provider->area);?></td>
          <td class='text-left'><?php echo zget($industries, $provider->industry);?></td>
          <td><?php echo substr($provider->createdDate, 0, 10);?></td>
          <td class='actions'>
            <?php commonModel::printLink('action',   'createRecord', "objectType=provider&objectID=$provider->id&customer=$provider->id", $lang->customer->record, "data-toggle='modal' data-width='860'");?>
            <?php commonModel::printLink('provider', 'contact', "providerID=$provider->id", $lang->provider->contact, "data-toggle='modal'");?>
            <?php commonModel::printLink('provider', 'edit',    "providerID=$provider->id", $lang->edit);?>
            <?php commonModel::printLink('provider', 'delete',  "providerID=$provider->id", $lang->delete, "class='deleter'");?>
          </td>
        </tr>
        <?php endforeach;?>
      </tbody>
      <tfoot><tr><td colspan='8'><?php $pager->show();?></td></tr></tfoot>
    </table>
  </div>
</div>
<?php include $app->appRoot . 'common/view/footer.html.php';?>
