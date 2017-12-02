<?php $url = $this->report->getSysURL();?>

<?php include '../../../sys/common/view/mail.header.html.php';?>
<tr>
  <td>
    <table cellpadding='0' cellspacing='0' style='width: 100%; border: none; border-collapse: collapse;'>
      <tr>
        <td style='padding: 10px; background-color: #F8FAFE; border: none; font-size: 14px; font-weight: 500; border-bottom: 1px solid #e5e5e5;'><?php echo date('Y-m-d') ?></td>
        <td style='width: 40px; text-align: right; background-color: #F8FAFE; border: none; vertical-align: top; padding: 10px; border-bottom: 1px solid #e5e5e5;'><?php echo html::a($url . $config->webRoot, $url . $config->webRoot, 'target="_blank"');?></td>
      </tr>
    </table>
  </td>
</tr>

<?php if(isset($mail->orders)):?>
<tr>
  <td style='padding: 10px; border: none;'>
    <h5 style='margin: 8px 0; font-size: 14px;'><?php echo rtrim(sprintf($lang->report->mailTitle->order,  count($mail->orders)), ',') ?></h5>
    <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px;'>
      <tr>
        <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $lang->report->idAB;?></th>
        <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $lang->report->orderTitle;?></th>
      </tr>
      <?php foreach($mail->orders as $order):?>
      <tr>
        <td style='padding: 5px; text-align: center; border: 1px solid #e5e5e5;'><?php echo $order->id;?></td>
        <td style='padding: 5px; border: 1px solid #e5e5e5;'>
        <?php
        $link = $this->createLink('crm.order', 'view', "orderID=$order->id");
        if($config->requestType == 'GET' and strpos($link, 'ztcli') !== false) $link = str_replace($this->server->php_self, $config->webRoot, $link);
        echo html::a($url . $link, $order->title);
        ?>
        </td>
      </tr>
      <?php endforeach;?>
    </table>
  </td>
</tr>
<?php endif;?>

<?php if(isset($mail->tasks)):?>
<tr>
  <td style='padding: 10px; border: none;'>
    <h5 style='margin: 8px 0; font-size: 14px;'><?php echo rtrim(sprintf($lang->report->mailTitle->task,  count($mail->tasks)), ',') ?></h5>
    <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px;'>
      <tr>
        <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $lang->report->idAB;?></th>
        <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $lang->report->taskName;?></th>
      </tr>
      <?php foreach($mail->tasks as $task):?>
      <tr>
        <td style='padding: 5px; text-align: center; border: 1px solid #e5e5e5;'><?php echo $task->id;?></td>
        <td style='padding: 5px; border: 1px solid #e5e5e5;'>
        <?php
        $link = $this->createLink('proj.task', 'view', "taskID=$task->id");
        if($config->requestType == 'GET' and strpos($link, 'ztcli') !== false) $link = str_replace($this->server->php_self, $config->webRoot, $link);
        echo html::a($url . $link, $task->name);
        ?>
        </td>
      </tr>
      <?php endforeach;?>
    </table>
  </td>
</tr>
<?php endif;?>

<?php if(isset($mail->todos)):?>
<tr>
  <td style='padding: 10px; border: none;'>
    <h5 style='margin: 8px 0; font-size: 14px;'><?php echo rtrim(sprintf($lang->report->mailTitle->todo,  count($mail->todos)), ',') ?></h5>
    <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px;'>
      <tr>
        <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $lang->report->idAB;?></th>
        <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $lang->report->todoName;?></th>
      </tr>
      <?php foreach($mail->todos as $todo):?>
      <tr>
        <td style='padding: 5px; text-align: center; border: 1px solid #e5e5e5;'><?php echo $todo->id;?></td>
        <td style='padding: 5px; border: 1px solid #e5e5e5;'>
        <?php
        $link = $this->createLink('sys.todo', 'view', "todoID=$todo->id");
        if($config->requestType == 'GET' and strpos($link, 'ztcli') !== false) $link = str_replace($this->server->php_self, $config->webRoot, $link);
        echo html::a($url . $link, $todo->name);
        ?>
        </td>
      </tr>
      <?php endforeach;?>
    </table>
  </td>
</tr>
<?php endif;?>

<?php if(isset($mail->customers)):?>
<tr>
  <td style='padding: 10px; border: none;'>
    <h5 style='margin: 8px 0; font-size: 14px;'><?php echo rtrim(sprintf($lang->report->mailTitle->customer,  count($mail->customers)), ',') ?></h5>
    <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px;'>
      <tr>
        <th style='width: 50px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $lang->report->idAB;?></th>
        <th style='border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;'><?php echo $lang->report->customerName;?></th>
      </tr>
      <?php foreach($mail->customers as $customer):?>
      <tr>
        <td style='padding: 5px; text-align: center; border: 1px solid #e5e5e5;'><?php echo $customer->id;?></td>
        <td style='padding: 5px; border: 1px solid #e5e5e5;'>
        <?php
        $link = $this->createLink('crm.customer', 'view', "customerID=$customer->id");
        if($config->requestType == 'GET' and strpos($link, 'ztcli') !== false) $link = str_replace($this->server->php_self, $config->webRoot, $link);
        echo html::a($url . $link, $customer->name);
        ?>
        </td>
      </tr>
      <?php endforeach;?>
    </table>
  </td>
</tr>
<?php endif;?>

<?php include '../../../sys/common/view/mail.footer.html.php';?>
