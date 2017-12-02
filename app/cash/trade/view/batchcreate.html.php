<?php
/**
 * The batch create view of trade module of RanZhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Tingting Dai <daitingting@xirangit.com>
 * @package     trade
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
?>
<?php include $app->getModuleRoot() . 'common/view/header.html.php';?>
<?php include '../../../sys/common/view/datepicker.html.php';?>
<?php include '../../../sys/common/view/chosen.html.php';?>
<?php js::set('modeType', 'all');?>
<form id='ajaxForm' method='post'>
  <div class='panel'>
    <div class='panel-heading'><strong><?php echo $lang->trade->batchCreate;?></strong></div>
    <table class='table table-hover'>
      <thead>
        <tr class='text-center'>
          <th class='w-160px required'><?php echo $lang->trade->depositor;?></th>
          <th class='w-100px'><?php echo $lang->trade->type;?></th> 
          <th class='w-160px'><?php echo $lang->trade->category;?></th> 
          <?php if($requireTrader):?>
          <th class='w-260px required'><?php echo $lang->trade->trader;?></th> 
          <?php else:?>
          <th class='w-260px'><?php echo $lang->trade->trader;?></th> 
          <?php endif;?>
          <th class='w-100px required'><?php echo $lang->trade->money;?></th>
          <th class='w-80px'> <?php echo $lang->trade->dept;?></th>
          <th class='w-120px required'><?php echo $lang->trade->handlers;?></th>
          <th class='w-110px'><?php echo $lang->trade->product;?></th>
          <th class='w-110px'><?php echo $lang->trade->date;?></th>
          <th><?php echo $lang->trade->desc;?></th>
        </tr>
      </thead>
      <tbody>
        <?php
        $depositors['ditto']   = $lang->ditto;
        $incomeTypes['ditto']  = $lang->ditto;
        $expenseTypes['ditto'] = $lang->ditto;
        $deptList['ditto']     = $lang->ditto;
        $traderList['ditto']   = $lang->ditto;
        $customerList['ditto'] = $lang->ditto;
        $productList['ditto']  = $lang->ditto;
        ?>
        <?php for($i = 0; $i < $config->trade->batchCreateCount; $i++):?>
        <?php
        if($i == 0)
        {
            $depositor = $incomeType = $expenseType = $dept = $trader = $customer = $product = '';
        }
        else
        {
            $depositor = $incomeType = $expenseType = $dept = $trader = $customer = $product = 'ditto';
        }
        ?>
        <tr>
          <td><?php echo html::select("depositor[$i]", $depositors, $depositor, "class='form-control' id='depositor{$i}'");?></td>
          <td><?php echo html::select("type[$i]", $lang->trade->typeList, 'out', "class='form-control type' id='type{$i}'");?></td>
          <td>
            <?php echo html::select("category[$i]", $incomeTypes, $incomeType, "class='form-control in chosen' style='display:none'");?>
            <?php echo html::select("category[$i]", $expenseTypes, $expenseType, "class='form-control out chosen' id='category{$i}'");?>
          </td>
          <td>
            <div class='input-group out'>
              <?php echo html::select("outtrader[$i]", $traderList, $trader, "class='form-control chosen' id='trader{$i}' data-no_results_text='" . $lang->searchMore . "'");?>
              <?php  echo html::input("traderName[$i]", '', "class='form-control' id='traderName{$i}' style='display:none'");?>
              <span class='input-group-addon'>
                <label class="checkbox-inline">
                  <input type="checkbox" name="createTrader[<?php echo $i;?>]" value="1"> <?php echo $lang->trade->newTrader;?>
                </label>
              </span>
            </div>
            <div class='in'><?php echo html::select("intrader[$i]", $customerList, $customer, "class='form-control in chosen' id='trader{$i}' style='display:none' data-no_results_text='" . $lang->searchMore . "'");?></div>
          </td>
          <td><?php echo html::input("money[$i]", '', "class='form-control'");?></td>
          <td><?php echo html::select("dept[$i]", $deptList, $dept, "class='form-control chosen'");?></td>
          <td><?php echo html::select("handlers[$i][]", $users, '', "class='form-control chosen' id='handlers{$i}' multiple");?></td>
          <td><?php echo html::select("product[$i]", $productList, $product, "class='form-control chosen' id='product{$i}'");?></td>
          <td><?php echo html::input("date[$i]", date('Y-m-d'), "class='form-control form-date' id='date{$i}'");?></td>
          <td><?php echo html::textarea("desc[$i]", '', "rows='1' class='form-control'");?></td>
        </tr>
        <?php endfor;?>
      </tbody>
      <tr><td colspan='10' class='text-center'><?php echo html::submitButton() . ' ' . html::backButton();?></td></tr>
    </table>
  </div>
</form>
<script>
<?php helper::import('../js/batchsearchcustomer.js');?>
</script>
<?php include $app->getModuleRoot() . 'common/view/footer.html.php';?>
