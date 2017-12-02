<?php
/**
 * The trade block view file of block module of RanZhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     block
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
?>
<table class='table table-data table-hover block-contract table-fixed'>
<?php $appid = ($this->get->app == 'sys' and isset($_GET['entry'])) ? "class='app-btn' data-id='{$this->get->entry}'" : ''?>
<div style="overflow:auto;" class='table-wrapper'>
  <table id='barChart' class='table table-condensed table-hover table-striped table-bordered table-chart' data-chart='bar' data-target='#myBarChar
t' data-animation='false'>
  <thead>
    <tr class='text-center'>
      <th><?php echo $lang->trade->month;?></th>
      <th class='chart-label-in'><i class='chart-color-dot-in icon-circle' style='color: green;'></i> <?php echo $lang->trade->in;?></th>
      <th class='chart-label-out'><i class='chart-color-dot-out icon-circle' style="color: red;"></i> <?php echo $lang->trade->out;?></th>
      <th class='chart-label-profit'><?php echo $lang->trade->profit . '/' . $lang->trade->loss;?></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($annualChartDatas as $currency => $annualChartData):?>
  <?php foreach($annualChartData as $month => $monthChartData):?>
  <tr>
    <td class='w-50px text-center'><?php echo $month;?></td>
    <td class='w-100px text-center'><?php echo $currencySign[$currency] . $monthChartData['in'];?></td>
    <td class='w-100px text-center'><?php echo $currencySign[$currency] . $monthChartData['out'];?></td>
    <td class='w-100px text-center'><?php echo  $currencySign[$currency] . $monthChartData['profit'];?></td>
  </tr>
  <?php endforeach;?>
  <?php endforeach;?>
  </tbody>
  </table>
</div>
</table>
