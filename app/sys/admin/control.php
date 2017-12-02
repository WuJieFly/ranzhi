<?php
/**
 * The control file of admin module of RanZhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     admin 
 * @version     $Id: control.php 4029 2016-08-26 06:50:41Z liugang $
 * @link        http://www.ranzhico.com
 */
class admin extends control
{
    /**
     * The index page of admin panel, print the sites.
     * 
     * @access public
     * @return void
     */
    public function index()
    {
        $this->lang->menuGroups->admin = 'system';
        $this->lang->admin->menu       = $this->lang->system->menu;
        $this->lang->admin->menuOrder  = $this->lang->system->menuOrder;

        $this->display();
    }
}
