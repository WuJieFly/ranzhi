<?php
/**
 * The admin view of entry module of RanZhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     entry 
 * @version     $Id: admin.html.php 4227 2016-10-25 08:27:56Z liugang $
 * @link        http://www.ranzhico.com
 */
include '../../common/view/header.html.php';
?>
<div>
  <div class='main panel'>
    <div class='panel-heading'>
      <strong><i class='icon-building'></i> <?php echo $lang->entry->admin;?></strong>
      <div class='panel-actions pull-right'>
        <?php commonModel::printLink('entry', 'category', '', $lang->entry->setCategory, "class='btn btn-primary'");?>
        <?php echo html::a($this->inlink('create'), $lang->entry->create, "class='btn btn-primary'");?>
      </div>
    </div>
    <div class='row-table text-center'>
      <div class='col-table thead w-30px'></div>
      <div class='col-table thead w-200px'><?php echo $lang->entry->name;?></div>
      <div class='col-table thead w-80px'><?php echo $lang->entry->code;?></div>
      <div class='col-table thead w-240px'><?php echo $lang->entry->key;?></div>
      <div class='col-table thead'><?php echo $lang->entry->ip;?></div>
      <div class='col-table thead w-220px'><?php echo $lang->actions;?></div>
    </div>
    <ul id='entryList'>
      <?php foreach($entries as $entry):?>
	  <?php
	  if($entry->code == '')
	  {
          echo "<li data-entryid='{$entry->id}'><div class='text-left row-table'>";
          echo "<div class='col-table w-30px'><i class='icon-move sort-handler-1'></i></div>";
          echo '<div class="col-table">';
          $name = $entry->abbr ? $entry->abbr : $entry->name;
          $entryName = validater::checkCode(substr($name, 0, 1)) ? strtoupper(substr($name, 0, 1)) : substr($name, 0, 3);
          if(validater::checkCode(substr($name, 0, 1)) and validater::checkCode(substr($name, 1, 1)))   $entryName .= strtoupper(substr($name, 1, 1));
          if(validater::checkCode(substr($name, 0, 1)) and !validater::checkCode(substr($name, 1, 1)))  $entryName .= strtoupper(substr($name, 1, 3));
          if(!validater::checkCode(substr($name, 0, 1)) and validater::checkCode(substr($name, 3, 1)))  $entryName .= strtoupper(substr($name, 3, 1));
          if(!validater::checkCode(substr($name, 0, 1)) and !validater::checkCode(substr($name, 3, 1))) $entryName .= substr($name, 3, 3);
          echo "<i class='icon icon-default' style='background-color: hsl(" . ($entry->id * 47 % 360) . ", 100%, 40%)'><span>{$entryName}</span></i>";
          echo $entry->name;
          echo "</div></div>";
          if(!empty($entry->children))
          {
              echo "<ul class='ulGrade2'>";
              foreach($entry->children as $childEntry) $this->entry->printInfo($childEntry, $entry->id);
              echo '</ul>';
          }
          echo '</li>';
	  }
	  else
      {
          $this->entry->printInfo($entry);
      }
	  ?>
      <?php endforeach;?>
    </ul>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
