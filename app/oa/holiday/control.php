<?php
/**
 * The control file of holiday of Ranzhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      chujilu <chujilu@cnezsoft.com>
 * @package     holiday
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
class holiday extends control
{
    /**
     * index 
     * 
     * @access public
     * @return void
     */
    public function index()
    {
        $this->locate(inlink('browse'));
    }

    /**
     * browse holidays.
     * 
     * @param  string $year 
     * @access public
     * @return void
     */
    public function browse($year = '')
    {
        if($year == '') $year = date('Y');
        $holidays = $this->holiday->getList($year);
        $yearList = $this->holiday->getYearPairs();

        $this->view->title       = $this->lang->holiday->browse;
        $this->view->holidays    = $holidays;
        $this->view->yearList    = $yearList;
        $this->view->currentYear = $year;
        $this->display();
    }

    /**
     * Create a holiday.
     * 
     * @access public
     * @return void
     */
    public function create()
    {
        if($_POST)
        {
            $holidayID = $this->holiday->create();
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
            $actionID = $this->loadModel('action')->create('holiday', $holidayID, 'created');
            $users = $this->loadModel('user')->getPairs('nodeleted,noforbidden,noclosed,noempty');
            $this->action->sendNotice($actionID, array_keys($users), true);
           $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $this->view->title = $this->lang->holiday->create;
        $this->display();
    }

    /**
     * Edit holiday.
     * 
     * @param  int    $id 
     * @access public
     * @return void
     */
    public function edit($id)
    {
        $holiday = $this->holiday->getById($id);
        if($_POST)
        {
            $this->holiday->update($id);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
           $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $this->view->title   = $this->lang->holiday->edit;
        $this->view->holiday = $holiday;
        $this->display();
    }

    /**
     * Delete holiday. 
     * 
     * @param  int    $id 
     * @access public
     * @return void
     */
    public function delete($id)
    {
        $result = $this->holiday->delete($id);
        if(!$result) $this->send(array('result' => 'fail', 'message' => dao::getError()));
        $this->send(array('result' => 'success'));
    }
}
