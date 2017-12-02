<?php
/**
 * The order list file of customer module of RanZhi.
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
<table class='table table-bordered table-hover table-striped table-data'>
  <thead>
    <tr class='text-center'>
      <th class='w-50px'><?php echo $lang->order->id;?></th>
      <th><?php echo $lang->order->product;?></th>
      <th><?php echo $lang->order->plan;?></th>
      <th><?php echo $lang->order->assignedTo;?></th>
      <th class='w-60px'><?php echo $lang->order->status;?></th>
      <th class='w-90px'><?php echo $lang->order->contactedDate;?></th>
      <th class='w-90px'><?php echo $lang->order->nextDate;?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($orders as $order):?>
    <tr class='text-center'>
      <td><?php echo $order->id;?></td>
      <td><?php foreach($order->products as $product) echo $product . ' ';?></td>
      <td><?php echo $order->plan;?></td>
      <td><?php echo zget($users, $order->assignedTo);?></td>
      <td><?php echo $lang->order->statusList[$order->status];?></td>
      <td><?php echo formatTime($order->contactedDate, DT_DATE1);?></td>
      <td><?php echo formatTime($order->nextDate, DT_DATE1);?></td>
    </tr>
    <?php endforeach;?>
  </tbody>
</table>
<?php include '../../common/view/footer.modal.html.php';?>
