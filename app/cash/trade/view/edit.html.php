<?php 
/**
 * The edit view file of trade module of RanZhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Tingting Dai <daitingting@xirangit.com>
 * @package     trade 
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../../sys/common/view/datepicker.html.php';?>
<?php include '../../../sys/common/view/chosen.html.php';?>
<?php js::set('modeType', $mode);?>
<?php js::set('contract', $trade->contract);?>
<?php js::set('mainCurrency', $config->setting->mainCurrency);?>
<ul id='menuTitle'>
  <li><?php commonModel::printLink('trade', 'browse', "mode=$mode", $lang->trade->browse);?></li>
  <li class='divider angle'></li>
</ul>
<div class='panel'>
  <div class='panel-heading'>
    <strong><i class="icon-edit"></i> <?php echo $lang->trade->edit;?></strong>
  </div>
  <div class='panel-body'>
    <form method='post' id='ajaxForm'>
      <table class='table table-form w-p60'>
        <tr>
          <th class='w-100px'><?php echo $lang->trade->depositor;?></th>
          <td><?php echo html::select('depositor', $depositorList, $trade->depositor, "class='form-control'");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->trade->product;?></th>
          <td><?php echo html::select('product', array('') + $productList, $trade->product, "class='form-control chosen'");?></td>
        </tr>
        <?php if($trade->type == 'in'):?>
        <tr class='income'>
          <th><?php echo $lang->trade->category;?></th>
          <td><?php echo html::select('category', array('') + $categories, $trade->category, "class='form-control chosen'");?></td>
        </tr>
        <?php endif;?>
        <?php if($trade->type == 'out'):?>
        <tr class='expense'>
          <th><?php echo $lang->trade->category;?></th>
          <td>
            <div class='input-group'>
              <?php echo html::select('category', array('') + $categories, $trade->category, "class='form-control chosen'");?>
              <div class='input-group-addon'><div style='padding-right: 20px;'><?php echo html::checkbox('objectType', $lang->trade->objectTypeList, $objectType);?></div></div>
            </div>
          </td>
        </tr>
        <tr class='hide'>
          <th><?php echo $lang->trade->order;?></th>
          <td>
            <select class='form-control chosen' id='order' name='order'>
              <option value=''></option>
              <?php foreach($orderList as $id => $order):?>
              <option <?php if($id == $trade->order) echo " selected='selected' ";?> value="<?php echo $id?>" data-customer="<?php echo $order->customer?>" data-amount="<?php echo $order->real;?>"><?php echo $order->name;?></option>
              <?php endforeach;?>
            </select>
          </td>
        </tr>
        <tr class='hide'>
          <th><?php echo $lang->trade->contract;?></th>
          <td>
            <select class='form-control chosen' id='contract' name='contract'>
              <option value=''></option>
              <?php foreach($contractList as $id => $contract):?>
              <option <?php if($id == $trade->contract) echo " selected='selected' ";?> value="<?php echo $id?>" data-customer="<?php echo $contract->customer?>" data-amount="<?php echo $contract->amount;?>"><?php echo $contract->name;?></option>
              <?php endforeach;?>
            </select>
          </td>
        </tr>
        <tr class='customerTR hide'>
          <th><?php echo $lang->trade->customer;?></th>
          <td><?php echo html::select('customer', $customerList, $trade->trader, "class='form-control chosen' onchange='getContract(this.value) data-no_results_text='" . $lang->searchMore . "''");?></td>
        </tr>
        <tr class='allCustomerTR hide'>
          <th><?php echo $lang->trade->customer;?></th>
          <td><?php echo html::select('allCustomer', ($traderList+ $customerList), $trade->trader, "class='form-control chosen' onchange='getContract(this.value)' data-no_results_text='" . $lang->searchMore . "'");?></td>
        </tr>
        <tr class='traderTR'>
          <th><?php echo $lang->trade->trader;?></th>
          <td>
            <div class='input-group'>
              <?php  echo html::select('trader', $traderList, $trade->trader, "class='form-control chosen' data-no_results_text='" . $lang->searchMore . "'");?>
              <?php  echo html::input('traderName', '', "class='form-control' style='display:none'");?>
              <div class='input-group-addon'><?php echo html::checkbox('createTrader', array( 1 => $lang->trade->newTrader));?></div>
            </div>
          </td>
        </tr>
        <tr class='customer-depositor hide'>
          <th><?php echo $lang->customer->depositor;?></th>
          <td><?php echo html::input('customerDepositor', '', "class='form-control' disabled='disabled'");?></td>
        </tr>
        <?php endif;?>
        <?php if($trade->type == 'in'):?>
        <tr>
          <th><?php echo $lang->trade->customer;?></th>
          <td><?php echo html::select('trader', $customerList, $trade->trader, "class='form-control chosen' onchange='getContract(this.value)' data-no_results_text='" . $lang->searchMore . "'");?></td>
        </tr>
        <tr class='customer-depositor hide'>
          <th><?php echo $lang->customer->depositor;?></th>
          <td><?php echo html::input('customerDepositor', '', "class='form-control' disabled='disabled'");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->trade->contract;?></th>
          <td class='contractTD'><?php echo html::select('contract', $tradeContract, $trade->contract, "class='form-control'");?></td>
        </tr>
        <?php endif;?>
        <?php if($trade->type == 'invest'):?>
        <tr class='trader'>
          <th><?php echo $lang->trade->trader;?></th>
          <td>
            <div class='input-group'>
              <?php echo html::select('trader', ($traderList + $customerList), $trade->trader, "class='form-control chosen' data-no_results_text='" . $lang->searchMore . "'");?>
              <?php echo html::input('traderName', '', "class='form-control' style='display:none'");?>
              <div class='input-group-addon'><?php echo html::checkbox('createTrader', array( 1 => $lang->trade->newTrader));?></div>
            </div>
          </td>
        </tr>
        <?php endif;?>
        <?php if($trade->type == 'redeem'):?>
        <tr class='investList'>
          <th><?php echo $lang->trade->invest;?></th>
          <td>
            <div class="required required-wrapper"></div>
            <?php echo html::select('investID', $investList, $trade->investID, "class='form-control chosen'");?>
          </td>
        </tr>
        <?php endif;?>
        <?php if($trade->type == 'invest'):?>
        <?php if(!empty($redeemPairs)):?>
        <tr>
          <th><?php echo $lang->trade->redeem;?></th>
          <td><?php echo html::select('redeems[]', $redeemPairs, $trade->redeems, "class='form-control chosen' multiple");?></td>
        </tr>
        <?php endif;?>
        <?php if(!empty($tradePairs)):?>
        <tr>
          <th><?php echo $lang->trade->in;?></th>
          <td><?php echo html::select('profits[]', $tradePairs, $trade->profits, "class='form-control chosen' multiple");?></td>
        </tr>
        <?php endif;?>
        <?php endif;?>
        <?php if($trade->type == 'loan'):?>
        <tr class='trader'>
          <th><?php echo $lang->trade->trader;?></th>
          <td>
            <div class='input-group'>
              <?php echo html::select('trader', ($traderList + $customerList), $trade->trader, "class='form-control chosen' data-no_results_text='" . $lang->searchMore . "'");?>
              <?php echo html::input('traderName', '', "class='form-control' style='display:none'");?>
              <div class='input-group-addon'><?php echo html::checkbox('createTrader', array( 1 => $lang->trade->newTrader));?></div>
            </div>
          </td>
        </tr>
        <?php endif;?>
        <?php if($trade->type == 'repay'):?>
        <tr class='loanList'>
          <th><?php echo $lang->trade->loan;?></th>
          <td>
            <div class="required required-wrapper"></div>
            <?php echo html::select('loanID', $loanList, $trade->loanID, "class='form-control'");?>
          </td>
        </tr>
        <?php endif;?>
        <?php if($trade->type == 'in' or $trade->type == 'out'):?>
        <tr>
          <th><?php echo $lang->trade->money;?></th>
          <td>
            <div class='input-group'>
              <?php echo html::input('money', $trade->money, "class='form-control'");?>
              <span class='input-group-addon fix-border'><?php echo $lang->trade->currency;?></span>
              <?php echo html::select('currencyLabel', $lang->currencyList, '', "class='form-control' readonly");?>
              <?php echo html::hidden('currency');?>
              <span class='input-group-addon fix-border exchangeRate'><?php echo $lang->trade->exchangeRate;?></span>
              <?php echo html::input('exchangeRate', $trade->exchangeRate, "class='form-control exchangeRate'");?> 
            </div>
          </td>
        </tr>
        <?php else:?>
        <tr>
          <th><?php echo $lang->trade->money;?></th>
          <td><?php echo html::input('money', $trade->money, "class='form-control'");?></td>
        </tr>
        <?php endif;?>
        <?php if($trade->type == 'repay'):?>
        <tr>
          <th><?php echo $lang->trade->interest;?></th>
          <td><?php echo html::input('interest', $interest->money, "class='form-control'");?></td>
        </tr>
        <?php endif;?>
        <?php if($trade->type == 'redeem'):?>
        <tr class='category'>
          <th><?php echo $lang->trade->in;?></th>
          <td>
            <div class='input-group'>
              <?php echo html::select('investCategory', $investCategoryList, !empty($investTrade->category) ? $investTrade->category : '', "class='form-control'");?>
              <span class="input-group-addon fix-border fix-padding"></span>
              <?php echo html::input('investMoney', !empty($investTrade->money) ? $investTrade->money : '', "class='form-control'");?>
            </div>
          </td>
        </tr>
        <?php endif;?>
        <tr>
          <th><?php echo $lang->trade->dept;?></th>
          <td><?php echo html::select('dept', $deptList, $trade->dept, "class='form-control chosen'");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->trade->handlers;?></th>
          <td><?php echo html::select('handlers[]', $users, $trade->handlers, "class='form-control chosen' multiple");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->trade->date;?></th>
          <td><?php echo html::input('date', $trade->date, "class='form-control form-date'");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->trade->desc;?></th>
          <td><?php echo html::textarea('desc', $trade->desc, "class='form-control' rows='3'");?></td>
        </tr>
        <?php if(commonModel::hasPriv('file', 'upload')):?>
        <tr>
          <th><?php echo $lang->trade->uploadFile;?></th>
          <td><?php echo $this->fetch('file', 'buildForm');?></td>
        </tr>
        <?php endif;?>
        <tr>
          <th></th>
          <td><?php echo html::submitButton();?></td>
        </tr>
      </table>
    </form>
  </div>
</div>
<script>
<?php helper::import('../js/searchcustomer.js');?>
</script>
<?php include '../../common/view/footer.html.php';?>
