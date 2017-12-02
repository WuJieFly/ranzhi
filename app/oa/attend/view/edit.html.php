<?php
/**
 * The settings view file of attend module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      chujilu <chujilu@cnezsoft.com>
 * @package     attend
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
?>
<?php include '../../../sys/common/view/header.modal.html.php';?>
<?php include '../../../sys/common/view/datepicker.html.php';?>
<?php js::set('status', $attend->status);?>
<?php js::set('reason', $attend->reason);?>
<div class='panel-body'>
  <form id='ajaxForm' method='post' action='<?php echo $this->createLink('attend', 'edit', "date=$date")?>'>
    <table class='table table-form table-condensed'>
      <?php if(!empty($attend->reviewStatus)):?>
      <tr>
        <th><?php echo $lang->attend->reviewStatus?></th>
        <td><?php echo zget($lang->attend->reviewStatusList, $attend->reviewStatus) . " " . $attend->reviewedBy . " " . formatTime($attend->reviewedDate, DT_DATETIME2);?></td>
      </tr>
      <?php endif;?>
      <tr>
        <th class='w-80px'><?php echo $lang->attend->date?></th>
        <td><?php echo $attend->dayName;?></td>
      </tr> 
    </table>
    <table class='table table-form table-condensed editMode'>
      <?php if(strpos(',late,both,absent,leave,makeup,overtime,lieu,trip,egress,', ",$attend->status,") !== false):?>
      <tr id='trIn'>
        <th><?php echo $lang->attend->manualIn?></th>
        <td><?php echo html::input('manualIn', empty($attend->manualIn) ? $this->config->attend->signInLimit : $attend->manualIn, "class='form-control form-time'")?></td>
      </tr>
      <?php endif;?>
      <?php if(strpos(',early,both,absent,', ",$attend->status,") !== false or (strpos(',leave,makeup,overtime,lieu,trip,egress', ",$attend->status,") !== false && date('Y-m-d') > "$attend->date {$config->attend->signOutLimit}")):?>
      <tr id='trOut'>
        <th><?php echo $lang->attend->manualOut?></th>
        <td><?php echo html::input('manualOut', empty($attend->manualOut) ? $this->config->attend->signOutLimit : $attend->manualOut, "class='form-control form-time'")?></td>
      </tr> 
      <?php endif;?>
      <tr>
        <th class='w-80px'><?php echo $lang->attend->desc?></th>
        <td><?php echo html::textarea('desc', $attend->desc, "class='form-control'")?></td>
      </tr> 
      <tr><th></th><td><?php echo html::submitButton();?></td></tr>
    </table>
    <table class='table table-form table-condensed viewMode'>
      <?php if(strpos(',late,both,absent', $attend->status) !== false):?>
      <tr id='trIn'>
        <th><?php echo $lang->attend->manualIn?></th>
        <td><?php echo formatTime($attend->manualIn, DT_TIME2);?></td>
      </tr>
      <?php endif;?>
      <?php if(strpos(',early,both,absent', $attend->status) !== false):?>
      <tr id='trOut'>
        <th><?php echo $lang->attend->manualOut?></th>
        <td><?php echo formatTime($attend->manualOut, DT_TIME2);?></td>
      </tr> 
      <?php endif;?>
      <tr>
        <th class='w-80px'><?php echo $lang->attend->desc?></th>
        <td><?php echo $attend->desc;?></td>
      </tr> 
      <?php if($attend->reviewStatus != 'pass' or strpos('late,early,both', $attend->status) !== false):?>
      <tr><th></th><td clospan='3'><?php echo html::commonButton($lang->edit, "btn btn-primary edit");?></td></tr>
      <?php endif;?>
    </table>
    <?php if(!empty($attend->id)) echo $this->fetch('action', 'history', "objectType=attend&objectID=$attend->id");?>
  </form>
</div>
<?php include '../../../sys/common/view/footer.modal.html.php';?>
