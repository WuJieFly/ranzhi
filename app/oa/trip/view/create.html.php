<?php
/**
 * The create view file of trip module of Ranzhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      chujilu <chujilu@cnezsoft.com>
 * @package     trip 
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
?>
<?php include '../../../sys/common/view/header.modal.html.php';?>
<?php include '../../../sys/common/view/datepicker.html.php';?>
<?php include '../../../sys/common/view/chosen.html.php';?>
<div class='panel-body'>
  <form id='ajaxForm' method='post' action="<?php echo $this->createLink("oa.$type", 'create')?>">
    <table class='table table-form table-condensed'>
      <tr>
        <th class='w-90px'><?php echo $lang->$type->name?></th>
        <td><?php echo html::input('name', '', "class='form-control'")?></td>
        <td></td>
      </tr>
      <tr>
        <th><?php echo $lang->$type->customer;?></th>
        <td><?php echo html::select('customers[]', $customerList, $customers, "class='form-contorl chosen' multiple data-no_results_text='" . $lang->searchMore . "'")?></td>
        <td></td>
      </tr>
      <tr>
        <th><?php echo $lang->$type->begin?></th>
        <td>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->$type->date?></span>
            <?php echo html::input('begin', date('Y-m-d'), "class='form-control form-date'")?>
            <span class='input-group-addon fix-border'><?php echo $lang->$type->time?></span>
            <?php echo html::input('start', $config->attend->signInLimit, "class='form-control form-time'")?>
          </div>
        </td>
        <td></td>
      </tr>
      <tr>
        <th><?php echo $lang->$type->end?></th>
        <td>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->$type->date?></span>
            <?php echo html::input('end', $type == 'egress' ? date('Y-m-d') : '', "class='form-control form-date'")?>
            <span class='input-group-addon fix-border'><?php echo $lang->$type->time?></span>
            <?php echo html::input('finish', $config->attend->signOutLimit, "class='form-control form-time'")?>
          </div>
        </td>
        <td></td>
      </tr>
      <?php if($type == 'trip'):?>
      <tr>
        <th><?php echo $lang->$type->from?></th>
        <td>
          <?php echo html::input('from', '', "class='form-control'")?>
        </td>
        <td></td>
      </tr>
      <?php endif;?>
      <tr>
        <th><?php echo $lang->$type->to?></th>
        <td>
          <?php echo html::input('to', '', "class='form-control'")?>
        </td>
        <td></td>
      </tr>
      <tr>
        <th><?php echo $lang->$type->desc?></th>
        <td><?php echo html::textarea('desc', '', "class='form-control'")?></td>
        <td></td>
      </tr> 
      <tr><th></th><td clospan='2'><?php echo html::hidden('type', $type) . html::submitButton();?></td></tr>
    </table>
  </form>
</div>
<script>
<?php helper::import('../js/searchcustomer.js');?>
</script>
<?php include '../../../sys/common/view/footer.modal.html.php';?>
