<?php
/**
 * The save view file of mail module of RanZhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     mail 
 * @version     $Id: save.html.php 4029 2016-08-26 06:50:41Z liugang $
 * @link        http://www.ranzhico.com
 */
?>
<?php include '../../common/view/header.html.php';?>
<div class='panel'>
  <div class='panel-heading'><strong><i class='icon-envelope'></i> <?php echo $lang->mail->common;?> <i class='icon-arrow-right'></i> <?php echo $lang->mail->save; ?></strong></div>
  <div class='panel-body'>
    <div class='alert alert-success'>
      <i class='icon-ok-sign'></i>
      <div class='content'><?php echo $lang->mail->successSaved; ?></div>
    </div>
    <div><?php if($this->post->turnon and $mailExist) echo html::linkButton($lang->mail->test, inlink('test')); ?></div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
