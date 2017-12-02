<?php
/**
 * The control file of upgrade module of RanZhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     upgrade
 * @version     $Id: control.php 4227 2016-10-25 08:27:56Z liugang $
 * @link        http://www.ranzhico.com
 */
class upgrade extends control
{
    /**
     * Construct, check the user can upgrade or not.
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * The index page.
     * 
     * @access public
     * @return void
     */
    public function index()
    {
        if(version_compare($this->config->installedVersion, '1.3.beta', '>'))
        {
            if(version_compare($this->config->installedVersion, '2.0', '<')) $this->locate(inlink('upgradeLicense'));
            if(!$this->upgrade->removeOldTodoFile() and version_compare($this->config->installedVersion, '3.0', '<'))
            {
                $this->view->type     = 'todoFolder';
                $this->view->todoPath = $this->app->getBasePath() . "app/oa/todo/";
                die($this->display());
            }
            $this->locate(inlink('backup'));
        }

        $this->view->type = 'appFolder';
        $this->display();
    }

    /**
     * The backup page.
     * 
     * @access public
     * @return void
     */
    public function backup()
    {
        $this->view->title = $this->lang->upgrade->backup;
        $this->view->db    = $this->config->db;
        $this->display();
    }

    public function upgradeLicense()
    {
        if($this->get->agree == true) $this->locate(inlink('backup'));

        $this->view->license = file_get_contents($this->app->getBasePath() . 'doc/LICENSE');
        $this->display();
    }

    /**
     * Select the version of old ranzhi.
     * 
     * @access public
     * @return void
     */
    public function selectVersion()
    {
        $version = str_replace(array(' ', '.'), array('', '_'), $this->config->installedVersion);
        $version = strtolower($version);

        $this->view->title   = $this->lang->upgrade->common . $this->lang->colon . $this->lang->upgrade->selectVersion;
        $this->view->version = $version;
        $this->display();
    }

    /**
     * Confirm the version.
     * 
     * @access public
     * @return void
     */
    public function confirm()
    {
        $confirmContent = $this->upgrade->getConfirm($this->post->fromVersion);
        if(empty($confirmContent)) $this->locate(inlink('execute', "fromVersion={$this->post->fromVersion}"));

        $this->view->title       = $this->lang->upgrade->confirm;
        $this->view->confirm     = $confirmContent;
        $this->view->fromVersion = $this->post->fromVersion;

        $this->display();
    }

    /**
     * Execute the upgrading.
     * 
     * @param  string  $fromVersion
     * @access public
     * @return void
     */
    public function execute($fromVersion)
    {
        $fromVersion = isset($_POST['fromVersion']) ? $this->post->fromVersion : $fromVersion;
        $result = $this->upgrade->execute($fromVersion);

        $this->view->title = $this->lang->upgrade->result;

        if(!empty($result))
        {
            $result[] = $this->lang->upgrade->afterDeleted; 

            $this->view->result = 'fail';
            $this->view->errors  = $result;
        }
        else
        {
            if(!$this->upgrade->isError())
            {
                $this->view->result = 'success';
            }
            else
            {
                $this->view->result = 'fail';
                $this->view->errors = $this->upgrade->getError();
            }
        }
        $this->display();
    }
}
