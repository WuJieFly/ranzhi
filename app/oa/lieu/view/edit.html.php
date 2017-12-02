<?php
/**
 * The edit view file of lieu module of Ranzhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Tingting Dai <daitingting@xirangit.com>
 * @package     lieu
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
?>
<?php include '../../../sys/common/view/header.modal.html.php';?>
<?php include '../../../sys/common/view/datepicker.html.php';?>
<?php include '../../../sys/common/view/chosen.html.php';?>
<?php $this->app->loadModuleConfig('attend');?>
<?php js::set('signIn', $config->attend->signInLimit)?>
<?php js::set('signOut', $config->attend->signOutLimit)?>
<?php js::set('workingHours', $config->attend->workingHours)?>
<div class='panel-body'>
  <form id='ajaxForm' method='post' action="<?php echo $this->createLink('oa.lieu', 'edit', "id=$lieu->id")?>">
    <table class='table table-form table-condensed'>
      <tr>
        <th class='w-60px'><?php echo $lang->lieu->begin?></th>
        <td>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->lieu->date;?></span>
            <?php echo html::input('begin', $lieu->begin, "class='form-control form-date'")?>
            <span class='input-group-addon fix-border'><?php echo $lang->lieu->time;?></span>
            <?php echo html::input('start', $lieu->start, "class='form-control form-time'")?>
          </div>
        </td>
      </tr>
      <tr>
        <th><?php echo $lang->lieu->end?></th>
        <td>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->lieu->date;?></span>
            <?php echo html::input('end', $lieu->end, "class='form-control form-date'")?>
            <span class='input-group-addon fix-border'><?php echo $lang->lieu->time;?></span>
            <?php echo html::input('finish', $lieu->finish, "class='form-control form-time'")?>
          </div>
        </td>
      </tr>
      <tr>
        <th><?php echo $lang->lieu->hours;?></th>
        <td><?php echo html::input('hours', $lieu->hours, "class='form-control'")?></td>
      </tr>
      <tr>
        <th><?php echo $lang->lieu->overtime;?></th>
        <td><?php echo html::select('overtime[]', $overtimePairs, $lieu->overtime, "class='form-control chosen' multiple")?></td>
      </tr>
      <tr>
        <th><?php echo $lang->lieu->desc;?></th>
        <td><?php echo html::textarea('desc', $lieu->desc, "class='form-control'")?></td>
      </tr>
      <tr><th></th><td clospan='2'><?php echo html::submitButton();?></td></tr>
    </table>
  </form>
</div>
<?php include '../../../sys/common/view/footer.modal.html.php';?>
