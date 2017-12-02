<?php
/**
 * The control file of overtime of Ranzhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Tingting Dai <daitingting@xirangit.com>
 * @package     overtime
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
class overtime extends control
{
    /**
     * index 
     * 
     * @access public
     * @return void
     */
    public function index()
    {
        $this->locate(inlink('personal'));
    }

    /**
     * Personal's overtime. 
     * 
     * @param  string  $date 
     * @param  string  $orderBy 
     * @access public
     * @return void
     */
    public function personal($date = '', $orderBy = 'id_desc')
    {
        die($this->fetch('overtime', 'browse', "type=personal&date=$date&orderBy=$orderBy", 'oa'));
    }

    /**
     * Department's overtime. 
     * 
     * @param  string   $date 
     * @param  string   $orderBy 
     * @access public
     * @return void
     */
    public function browseReview($date = '', $orderBy = 'id_desc')
    {
        die($this->fetch('overtime', 'browse', "type=browseReview&date=$date&orderBy=$orderBy", 'oa'));
    }

    /**
     * Company's overtime. 
     * 
     * @param  string   $date 
     * @param  string   $orderBy 
     * @access public
     * @return void
     */
    public function company($date = '', $orderBy = 'id_desc')
    {
        die($this->fetch('overtime', 'browse', "type=company&date=$date&orderBy=$orderBy", 'oa'));
    }

    /**
     * Browse overtime.
     * 
     * @param  string   $type 
     * @param  string   $date 
     * @param  string   $orderBy 
     * @access public
     * @return void
     */
    public function browse($type = 'personal', $date = '', $orderBy = 'id_desc')
    {
        /* If type is browseReview, display all overtimes wait to review. */
        if($type == 'browseReview')
        {
            $date         = '';
            $currentYear  = '';
            $currentMonth = '';
        }
        else
        {
            if($date == '' or (strlen($date) != 6 and strlen($date) != 4)) $date = date("Ym");
            $currentYear  = substr($date, 0, 4);
            $currentMonth = strlen($date) == 6 ? substr($date, 4, 2) : '';
            $monthList    = $this->overtime->getAllMonth($type);
            $yearList     = array_keys($monthList);

            $this->view->currentYear  = $currentYear;
            $this->view->currentMonth = $currentMonth;
            $this->view->monthList    = $monthList;
            $this->view->yearList     = $yearList;
        }

        $overtimeList = array();
        $deptList     = $this->loadModel('tree')->getPairs(0, 'dept');
        $deptList[0]  = '';

        if($type == 'personal')
        {
            $overtimeList = $this->overtime->getList($type, $currentYear, $currentMonth, $this->app->user->account, '', '', $orderBy);
        }
        elseif($type == 'browseReview')
        {
            if($this->app->user->admin == 'super')
            {
                $overtimeList = $this->overtime->getList($type, $currentYear, $currentMonth, '', '', '', $orderBy);
            }
            else
            {
                $reviewedBy = $this->overtime->getReviewedBy();
                if($reviewedBy)
                { 
                    if($reviewedBy == $this->app->user->account)
                    {
                        $overtimeList = $this->overtime->getList($type, $currentYear, $currentMonth, '', array_keys($deptList), '', $orderBy);
                    }
                }
                else
                {
                    $depts = $this->loadModel('tree')->getDeptManagedByMe($this->app->user->account);
                    if(empty($depts))
                    {
                        $overtimeList = array();
                    }
                    else
                    {
                        $overtimeList = $this->overtime->getList($type, $currentYear, $currentMonth, '', array_keys($depts), '', $orderBy);
                    }
                }
            }
        }
        elseif($type == 'company')
        {
            $overtimeList = $this->overtime->getList($type, $currentYear, $currentMonth, '', '', '', $orderBy);
        }

        $this->session->set('overtimeList', $this->app->getURI(true));

        $this->view->title        = $this->lang->overtime->browse;
        $this->view->type         = $type;
        $this->view->deptList     = $deptList;
        $this->view->users        = $this->loadModel('user')->getPairs();
        $this->view->overtimeList = $overtimeList;
        $this->view->date         = $date;
        $this->view->orderBy      = $orderBy;
        $this->display();
    }

    /**
     * Review an overtime.
     * 
     * @param  int      $id 
     * @param  string   $status 
     * @access public
     * @return void
     */
    public function review($id, $status)
    {
        /* Check privilage. */
        $canReview = false;
        if($this->app->user->admin == 'super')
        {
            $canReview = true;
        }
        else
        {
            $reviewedBy = $this->overtime->getReviewedBy();
            if($reviewedBy)
            { 
                if($reviewedBy == $this->app->user->account) $canReview = true;
            }
            else
            {
                $overtime    = $this->overtime->getById($id);
                $createdUser = $this->loadModel('user')->getByAccount($overtime->createdBy);
                $dept        = $this->loadModel('tree')->getById($createdUser->dept);
                if($dept && $this->app->user->account == trim($dept->moderators, ',')) $canReview = true; 
            }
        }

        if($status == 'pass')
        {
            if(!$canReview) $this->send(array('result' => 'fail', 'message' => $this->lang->overtime->denied));

            $this->overtime->review($id, $status);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $actionID = $this->loadModel('action')->create('overtime', $id, 'reviewed', '', $this->lang->overtime->statusList[$status]);
            $this->sendmail($id, $actionID);

            $this->send(array('result' => 'success'));
        }

        if($status == 'reject')
        {
            if($_POST)
            {
                if(!$canReview) $this->send(array('result' => 'fail', 'message' => $this->lang->overtime->denied));

                if(!$this->post->comment) $this->send(array('result' => 'fail', 'message' => array('comment' => sprintf($this->lang->error->notempty, $this->lang->overtime->rejectReason))));

                $this->overtime->review($id, $status);
                if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

                $actionID = $this->loadModel('action')->create('overtime', $id, 'reviewed', $this->post->comment, $this->lang->overtime->statusList[$status]);
                $this->sendmail($id, $actionID);
                
                $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
            }

            $this->view->title = $this->lang->overtime->review;
            $this->view->id    = $id;
            $this->display();
        }
    }

    /**
     * Batch review overtimes. 
     * 
     * @param  string $status
     * @access public
     * @return void
     */
    public function batchReview($status = 'pass')
    {
        if(!$this->post->overtimeIDList) $this->send(array('result' => 'fail', 'message' => $this->lang->overtime->nodata));

        /* Check privilage. */
        $canReview      = false;
        $overtimeIDList = $this->post->overtimeIDList;
        if($this->app->user->admin == 'super')
        {
            $canReview = true;
        }
        else
        {
            $reviewedBy = $this->overtime->getReviewedBy();
            if($reviewedBy)
            { 
                if($reviewedBy == $this->app->user->account) $canReview = true;
            }
            else
            {
                $overtimeIDList = $this->dao->select('t1.id')->from(TABLE_OVERTIME)->alias('t1')
                    ->leftJoin(TABLE_USER)->alias('t2')->on('t1.createdBy=t2.account')
                    ->leftJoin(TABLE_CATEGORY)->alias('t3')->on('t2.dept=t3.id')
                    ->where('t1.id')->in($overtimeIDList)
                    ->andWhere('t3.type')->eq('dept')
                    ->andWhere('t3.moderators')->eq(",{$this->app->user->account},")
                    ->fetchPairs();
                if($overtimeIDList) $canReview = true;
            }
        }

        if($status == 'pass')
        {
            if(!$canReview) $this->send(array('result' => 'fail', 'message' => $this->lang->overtime->denied));

            foreach($overtimeIDList as $id)
            {
                $this->overtime->review($id, $status);
                if(dao::isError()) continue;

                $actionID = $this->loadModel('action')->create('overtime', $id, 'reviewed', '', $this->lang->overtime->statusList[$status]);
                $this->sendmail($id, $actionID);
            }
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->send(array('result' => 'success', 'message' => $this->lang->overtime->reviewSuccess, 'locate' => 'reload'));
        }
    }

    /**
     * Create an overtime.
     * 
     * @param  string $date
     * @access public
     * @return void
     */
    public function create($date = '')
    {
        if($_POST)
        {
            $result = $this->overtime->create();
            if(is_array($result)) $this->send($result);

            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $overtimeID = $result;
            $actionID   = $this->loadModel('action')->create('overtime', $overtimeID, 'created');
            $this->sendmail($overtimeID, $actionID);
            
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('personal')));
        }

        if($date)
        {
            $date     = date('Y-m-d', strtotime($date));
            $overtime = $this->overtime->getByDate($date, $this->app->user->account);
            if($overtime && strpos(',wait,draft,', $overtime->status) !== false) $this->locate(inlink('edit', "id=$overtime->id"));
        }

        $this->view->title = $this->lang->overtime->create;
        $this->view->date  = $date;
        $this->display();
    }

    /**
     * Edit overtime.
     * 
     * @param  int    $id 
     * @access public
     * @return void
     */
    public function edit($id)
    {
        $overtime = $this->overtime->getById($id);
        /* check privilage. */
        if($overtime->createdBy != $this->app->user->account) 
        {
            $locate     = helper::safe64Encode(helper::createLink('oa.overtime', 'browse'));
            $noticeLink = helper::createLink('notice', 'index', "type=accessLimited&locate={$locate}");
            die(js::locate($noticeLink));
        }

        if($_POST)
        {
            $result = $this->overtime->update($id);
            if(is_array($result) && isset($result['result']) && $result['result'] == 'fail') $this->send($result);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
            if($result)
            {
                $actionID = $this->loadModel('action')->create('overtime', $id, 'edited');
                $this->action->logHistory($actionID, $result);
            }
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $this->view->title    = $this->lang->overtime->edit;
        $this->view->overtime = $overtime;
        $this->display();
    }

    /**
     * View overtime.
     * 
     * @param  int    $id 
     * @access public
     * @return void
     */
    public function view($id, $type = '')
    {
        $this->view->title    = $this->lang->overtime->view;
        $this->view->overtime = $this->overtime->getById($id);
        $this->view->users    = $this->loadModel('user')->getPairs();
        $this->view->type     = $type;
        $this->display();
    }

    /**
     * Delete overtime.
     * 
     * @param  int     $id 
     * @access public
     * @return void
     */
    public function delete($id)
    {
        $overtime = $this->overtime->getById($id);
        if($overtime->createdBy != $this->app->user->account) $this->send(array('result' => 'fail', 'message' => $this->lang->overtime->denied));

        $this->overtime->delete($id);
        if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
        $this->send(array('result' => 'success'));
    }

    /**
     * Switch status for a overtime.
     * 
     * @param  int    $overtimeID 
     * @access public
     * @return void
     */
    public function switchStatus($overtimeID)
    {
        $overtime = $this->overtime->getById($overtimeID);
        if(!$overtime) return false;
        if($overtime->createdBy != $this->app->user->account) $this->send(array('result' => 'fail', 'message' => $this->lang->overtime->denied));

        $dates = range(strtotime($overtime->begin), strtotime($overtime->end), 60 * 60 * 24);
 
        if($overtime->status == 'wait')
        {
            $this->dao->update(TABLE_OVERTIME)->set('status')->eq('draft')->where('id')->eq($overtimeID)->exec();

            $actionID = $this->loadModel('action')->create('overtime', $overtimeID, 'revoked');
            $this->sendmail($overtimeID, $actionID);

            $this->loadModel('attend', 'oa')->batchUpdate($dates, $overtime->createdBy);
        }

        if($overtime->status == 'draft')
        {
            $this->dao->update(TABLE_OVERTIME)->set('status')->eq('wait')->where('id')->eq($overtimeID)->exec();

            $actionID = $this->loadModel('action')->create('overtime', $overtimeID, 'commited');
            $this->sendmail($overtimeID, $actionID);

            $this->loadModel('attend', 'oa')->batchUpdate($dates, $overtime->createdBy, '', 'overtime');
        }

        if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
        $this->send(array('result' => 'success'));
    }

    /**
     * Send email.
     * 
     * @param  int    $overtimeID 
     * @param  int    $actionID 
     * @access public
     * @return void
     */
    public function sendmail($overtimeID, $actionID)
    {
        /* Reset $this->output. */
        $this->clear();

        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        /* Set toList and ccList. */
        $overtime = $this->overtime->getById($overtimeID);
        $users    = $this->loadModel('user')->getPairs();
        $toList   = '';
        if($action->action == 'reviewed')
        {
            $toList = $overtime->createdBy;
            $subject = "{$this->lang->overtime->common}{$this->lang->overtime->statusList[$overtime->status]}#{$overtime->id} " . zget($users, $overtime->createdBy);
        }
        if($action->action == 'created' or $action->action == 'revoked' or $action->action == 'commited')
        {
            $reviewedBy = $this->overtime->getReviewedBy();
            if($reviewedBy)
            {
                $toList = $reviewedBy; 
            }
            else
            {
               $dept = $this->loadModel('tree')->getById($this->app->user->dept);
               if($dept) $toList = trim($dept->moderators, ',');
            }

            $subject = "{$this->lang->overtime->common}#{$overtime->id} " . zget($users, $overtime->createdBy);
        }

        /* send notice if user is online and return failed accounts. */
        $toList = $this->loadModel('action')->sendNotice($actionID, $toList);

        /* Create the email content. */
        $this->view->overtime  = $overtime;
        $this->view->action = $action;
        $this->view->users  = $users;

        $mailContent = $this->parse($this->moduleName, 'sendmail');

        /* Send emails. */
        $this->loadModel('mail')->send($toList, $subject, $mailContent);
        if($this->mail->isError()) trigger_error(join("\n", $this->mail->getError()));
    }

    /**
     * get data to export.
     * 
     * @param  string $mode 
     * @param  string $orderBy 
     * @access public
     * @return void
     */
    public function export($mode = 'all', $orderBy = 'id_desc')
    { 
        if($_POST)
        {
            $deptList  = $this->loadModel('tree')->getPairs('', 'dept');
            $users     = $this->loadModel('user')->getList();
            $userPairs = array();
            $userDepts = array();
            foreach($users as $key => $user) 
            {
                $userPairs[$user->account] = $user->realname;
                $userDepts[$user->account] = zget($deptList, $user->dept, '');
            }

            /* Create field lists. */
            $fields = explode(',', $this->config->overtime->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($this->lang->overtime->$fieldName) ? $this->lang->overtime->$fieldName : $fieldName;
                unset($fields[$key]);
            }
            $fields['dept'] = $this->lang->user->dept;

            $overtimes = array();
            if($mode == 'all')
            {
                $overtimeQueryCondition = $this->session->overtimeQueryCondition;
                if(strpos($overtimeQueryCondition, 'limit') !== false) $overtimeQueryCondition = substr($overtimeQueryCondition, 0, strpos($overtimeQueryCondition, 'limit'));
                $stmt = $this->dbh->query($overtimeQueryCondition);
                while($row = $stmt->fetch()) $overtimes[$row->id] = $row;
            }
            if($mode == 'thisPage')
            {
                $stmt = $this->dbh->query($this->session->overtimeQueryCondition);
                while($row = $stmt->fetch()) $overtimes[$row->id] = $row;
            }

            foreach($overtimes as $overtime)
            {
                $overtime->createdBy  = zget($userPairs, $overtime->createdBy);
                $overtime->dept       = zget($userDepts, $overtime->createdBy);
                $overtime->type       = zget($this->lang->overtime->typeList, $overtime->type);
                $overtime->begin      = $overtime->begin . ' ' . $overtime->start;
                $overtime->end        = $overtime->end   . ' ' . $overtime->finish;
                $overtime->desc       = htmlspecialchars_decode($overtime->desc);
                $overtime->desc       = str_replace("<br />", "\n", $overtime->desc);
                $overtime->desc       = str_replace('"', '""', $overtime->desc);
                $overtime->status     = zget($this->lang->overtime->statusList, $overtime->status);
                $overtime->reviewedBy = zget($userPairs, $overtime->reviewedBy);
            }

            $this->post->set('fields', $fields);
            $this->post->set('rows', $overtimes);
            $this->post->set('kind', 'overtime');
            $this->fetch('file', 'export2CSV' , $_POST);
        }

        $this->display();
    }

    /**
     * Set reviewer. 
     * 
     * @param  string $module 
     * @access public
     * @return void
     */
    public function setReviewer($module = '')
    {
        if($_POST)
        {
            $this->loadModel('setting')->setItem('system.oa.overtime..reviewedBy', $this->post->reviewedBy);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess));
        }

        if($module)
        {
            $this->lang->menuGroups->overtime = $module;
            $this->lang->overtime->menu       = $this->lang->$module->menu;
        }

        $this->view->title      = $this->lang->overtime->setReviewer;
        $this->view->users      = $this->loadModel('user', 'sys')->getPairs('noclosed,noforbidden,nodeleted');
        $this->view->reviewedBy = $this->overtime->getReviewedBy();
        $this->display();
    }
}
