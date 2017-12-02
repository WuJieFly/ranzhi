<?php
/**
 * The test view file of mail module of RanZhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     mail 
 * @version     $Id: test.html.php 4029 2016-08-26 06:50:41Z liugang $
 * @link        http://www.ranzhico.com
 */
?>
<?php include '../../common/view/header.html.php';?>
<div class='panel'>
  <div class='panel-heading'><strong><i class='icon-envelope'></i> <?php echo $lang->mail->common;?> <i class='icon-arrow-right'></i> <?php echo $lang->mail->test; ?></strong></div>
  <div class='panel-body'>
    <form method='post' id='ajaxForm'>
      <div class='form-group'><label for='to' class='col-sm-12'><?php echo $lang->mail->inputFromEmail; ?></label></div>
      <div class='form-group'>
        <div class='col-xs-10 col-sm-6 col-md-3'><?php echo html::select('to', $users, $app->user->email, 'class="form-control"'); ?></div>
        <div class='col-xs-2 col-sm-6 col-md-3'><?php echo html::submitButton($lang->mail->test) . html::linkButton($lang->mail->edit, inLink('edit')); ?></div>
      </div>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
