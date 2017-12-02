<?php
/**
 * The edit view file of leave module of Ranzhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      chujilu <chujilu@cnezsoft.com>
 * @package     leave
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
?>
<?php include '../../../sys/common/view/header.modal.html.php';?>
<?php include '../../../sys/common/view/datepicker.html.php';?>
<?php $this->app->loadModuleConfig('attend');?>
<?php js::set('signIn', $config->attend->signInLimit)?>
<?php js::set('signOut', $config->attend->signOutLimit)?>
<?php js::set('workingHours', $config->attend->workingHours)?>
<div class='panel-body'>
  <form id='ajaxForm' method='post' action="<?php echo $this->createLink('oa.leave', 'edit', "id=$leave->id")?>">
    <table class='table table-form table-condensed'>
      <?php if(!commonModel::hasPriv('oa.leave', 'browseReview')):?>
      <tr>
        <th><?php echo $lang->leave->status;?></th>
        <td class='text-warning'><?php echo $lang->leave->statusList[$leave->status];?></td>
        <td></td>
      </tr> 
      <?php endif;?>
      <tr>
        <th class='w-80px'><?php echo $lang->leave->type?></th>
        <td><?php echo html::radio('type', $lang->leave->typeList, $leave->type, "class=''")?></td>
        <td></td>
      </tr>
      <tr>
        <th><?php echo $lang->leave->begin?></th>
        <td>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->leave->date?></span>
            <?php echo html::input('begin', $leave->begin, "class='form-control form-date'")?>
            <span class='input-group-addon fix-border'><?php echo $lang->leave->time?></span>
            <?php echo html::input('start', $leave->start, "class='form-control form-time'")?>
          </div>
        </td>
        <td></td>
      </tr>
      <tr>
        <th><?php echo $lang->leave->end?></th>
        <td>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->leave->date?></span>
            <?php echo html::input('end', $leave->end, "class='form-control form-date'")?>
            <span class='input-group-addon fix-border'><?php echo $lang->leave->time?></span>
            <?php echo html::input('finish', $leave->finish, "class='form-control form-time'")?>
          </div>
        </td>
        <td></td>
      </tr>
      <tr>
        <th><?php echo $lang->leave->hours?></th>
        <td>
          <div class='input-group'>
            <?php echo html::input('hours', $leave->hours, "class='form-control'")?>
            <span class='input-group-addon'><?php echo $lang->leave->hoursTip?></span>
          </div>
        </td>
        <td></td>
      </tr>
      <tr>
        <th><?php echo $lang->leave->desc?></th>
        <td><?php echo html::textarea('desc', $leave->desc, "class='form-control'")?></td>
        <td></td>
      </tr> 
      <tr><th></th><td clospan='2'><?php echo html::submitButton();?></td></tr>
    </table>
  </form>
</div>
<?php include '../../../sys/common/view/footer.modal.html.php';?>
