<?php 
/**
 * The create view file of provider module of RanZhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Tingting Dai <daitingting@xirangit.com>
 * @package     provider 
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
?>
<?php include $app->appRoot . 'common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php include '../../common/view/chosen.html.php';?>
<div class='panel'>
  <div class='panel-heading'>
    <strong><i class="icon-plus"></i> <?php echo $lang->provider->create;?></strong>
  </div>
  <div class='panel-body'>
    <form method='post' id='providerForm' class='form-condensed'>
      <table class='table table-form'>
        <tr>
          <th class='w-80px'><?php echo $lang->provider->name;?></th>
          <td class='w-p40'><?php echo html::input('name', '', "class='form-control'");?></td><td></td>
        </tr>
        <tr>
          <th><?php echo $lang->provider->category;?></th>
          <td><?php echo html::select('category', $categories, '', "class='form-control chosen'");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->provider->contact;?></th>
          <td><?php echo html::input('contact', '', "class='form-control'");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->provider->phone;?></th>
          <td><?php echo html::input('phone', '', "class='form-control'");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->provider->email;?></th>
          <td><?php echo html::input('email', '', "class='form-control'");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->provider->qq;?></th>
          <td><?php echo html::input('qq', '', "class='form-control'");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->provider->type;?></th>
          <td><?php echo html::select("type", $lang->provider->typeList, '', "class='form-control'");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->provider->size;?></th>
          <td><?php echo html::select('size', $lang->provider->sizeList, '', "class='form-control'");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->provider->industry;?></th>
          <td><?php echo html::select('industry', $industries, '', "class='form-control chosen'");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->provider->area;?></th>
          <td><?php echo html::select('area', $areas, '', "class='form-control chosen'");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->provider->weibo;?></th>
          <td><?php echo html::input('weibo', 'http://weibo.com/', "class='form-control'");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->provider->weixin;?></th>
          <td><?php echo html::input('weixin', '', "class='form-control'");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->provider->site;?></th>
          <td><?php echo html::input('site', 'http://', "class='form-control'");?></td><td></td>
        </tr>
        <tr>
          <th><?php echo $lang->provider->desc;?></th>
          <td colspan='2'><?php echo html::textarea('desc', '', "rows='2' class='form-control'");?></td>
        </tr>
        <tr>
          <th></th>
          <td colspan='2'>
            <?php echo html::submitButton() . '&nbsp;&nbsp;' . html::backButton();?>
            <div id='duplicateError' class='hide'></div>
          </td>
        </tr>
      </table>
    </form>
  </div>
</div>
<div class='errorMessage hide'>
  <div class='alert alert-danger alert-dismissable'>
    <button aria-hidden='true' data-dismiss='alert' class='close' type='button'>×</button>
    <button type='submit' class='btn btn-default' id='continueSubmit'><?php echo $lang->continueSave;?></button>
  </div>
</div>
<?php include $app->appRoot . 'common/view/footer.html.php';?>
