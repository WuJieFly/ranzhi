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
<?php include '../../common/view/chosen.html.php';?>
<form method='post' id='ajaxForm' action='<?php echo $this->createLink('customer', 'merge', "customerID=$customerID")?>'>
  <table class='table table-form'>
    <tr>
      <th class='w-60px'><?php echo $lang->customer->common;?></th>
      <td><?php echo html::select('customer', $customers, '', "class='form-control chosen'");?></td>
    </tr>
    <tr>
      <th></th>
      <td>
        <div class='alert alert-primary'><?php echo $lang->customer->mergeTip;?></div>
      </td>
    </tr>
    <tr>
      <th></th>
      <td><?php echo html::submitButton();?></td>
    </tr>
  </table>
</form>
<?php include '../../common/view/footer.modal.html.php';?>
