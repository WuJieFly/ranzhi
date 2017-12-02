<?php
/**
 * The assignTo view file of customer module of RanZhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Tingting Dai <daitingting@xirangit.com>
 * @package     customer
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
?>
<?php include '../../common/view/header.modal.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php include '../../common/view/chosen.html.php';?>
<form method='post' id='ajaxForm' action='<?php echo $this->createLink('customer', 'assign', "customerID=$customerID")?>'>
  <table class='table table-form'>
    <tr>
      <th class='w-50px'><?php echo $lang->customer->assignedTo;?></th>
      <td>
        <div class='input-group'>
          <?php echo html::select('assignedTo', $members, $customer->assignedTo, "class='form-control chosen'")?>
          <div class='input-group-addon'>
            <label class='checkbox-inline'><input type='checkbox' id='public' name='public' value='1' <?php if($customer->public) echo 'checked';?>> <?php echo $lang->customer->public;?></label>
          </div>
        </div>
      </td>
    </tr>
    <tr>
      <th><?php echo $lang->comment?></th>
      <td><?php echo html::textarea('note')?></td>
    </tr>
    <tr>
      <th></th>
      <td><?php echo html::submitButton();?></td>
    </tr>
  </table>
</form>
<?php include '../../common/view/footer.modal.html.php';?>
