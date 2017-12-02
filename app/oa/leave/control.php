<?php
/**
 * The control file of leave of Ranzhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      chujilu <chujilu@cnezsoft.com>
 * @package     leave
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
class leave extends control
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
     * personal's leave. 
     * 
     * @param  string $date 
     * @access public
     * @return void
     */
    public function personal($date = '', $orderBy = 'id_desc')
    {
        die($this->fetch('leave', 'browse', "type=personal&date=$date&orderBy=$orderBy", 'oa'));
    }

    /**
     * Department's leave. 
     * 
     * @param  string $date 
     * @access public
     * @return void
     */
    public function browseReview($date = '', $orderBy = 'id_desc')
    {
        die($this->fetch('leave', 'browse', "type=browseReview&date=$date&orderBy=$orderBy", 'oa'));
    }

    /**
     * Company's leave. 
     * 
     * @param  string $date 
     * @access public
     * @return void
     */
    public function company($date = '', $orderBy = 'id_desc')
    {
        die($this->fetch('leave', 'browse', "type=company&date=$date&orderBy=$orderBy", 'oa'));
    }

    /**
     * browse 
     * 
     * @param  string $type 
     * @param  string $date 
     * @access public
     * @return void
     */
    public function browse($type = 'personal', $date = '', $orderBy = 'id_desc')
    {
        /* If type is browseReview, display all leaves wait to review. */
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
            $monthList    = $this->leave->getAllMonth($type);
            $yearList     = array_keys($monthList);

            $this->view->currentYear  = $currentYear;
            $this->view->currentMonth = $currentMonth;
            $this->view->monthList    = $monthList;
            $this->view->yearList     = $yearList;
        }

        $leaveList   = array();
        $deptList    = $this->loadModel('tree')->getPairs(0, 'dept');
        $deptList[0] = '/';

        if($type == 'personal')
        {
            $leaveList = $this->leave->getList($type, $currentYear, $currentMonth, $this->app->user->account, '', '', $orderBy);
        }
        elseif($type == 'browseReview')
        {
            if($this->app->user->admin == 'super')
            {
                $leaveList = $this->leave->getList($type, $currentYear, $currentMonth, '', '', '', $orderBy);
            }
            else
            {
                $reviewedBy = $this->leave->getReviewedBy();
                if($reviewedBy)
                { 
                    if($reviewedBy == $this->app->user->account)
                    {
                        $leaveList = $this->leave->getList($type, $currentYear, $currentMonth, '', array_keys($deptList), '', $orderBy);
                    }
                }
                else
                {
                    $depts = $this->loadModel('tree')->getDeptManagedByMe($this->app->user->account);
                    if(empty($depts))
                    {
                        $leaveList = array();
                    }
                    else
                    {
                        $leaveList = $this->leave->getList($type, $currentYear, $currentMonth, '', array_keys($depts), '', $orderBy);
                    }
                }
            }
        }
        elseif($type == 'company')
        {
            $leaveList = $this->leave->getList($type, $currentYear, $currentMonth, '', '', '', $orderBy);
        }

        $this->view->title     = $this->lang->leave->browse;
        $this->view->type      = $type;
        $this->view->deptList  = $deptList;
        $this->view->users     = $this->loadModel('user')->getPairs();
        $this->view->leaveList = $leaveList;
        $this->view->date      = $date;
        $this->view->orderBy   = $orderBy;
        $this->display();
    }

    /**
     * View detail of a leave. 
     * 
     * @param  int    $leaveID 
     * @param  string $type 
     * @access public
     * @return void
     */
    public function view($leaveID, $type = '')
    {
        $this->view->title = $this->lang->leave->view;
        $this->view->users = $this->loadModel('user', 'sys')->getPairs();
        $this->view->leave = $this->leave->getByID($leaveID);
        $this->view->type  = $type;
        $this->display();
    }

    /**
     * review 
     * 
     * @param  int    $id 
     * @param  string $status 
     * @param  string $mode
     * @access public
     * @return void
     */
    public function review($id, $status, $mode = '')
    {
        /* Check privilage. */
        $canReview = false;
        if($this->app->user->admin == 'super') 
        {
            $canReview = true;
        }
        else
        {
            $reviewedBy = $this->leave->getReviewedBy();
            if($reviewedBy)
            { 
                if($reviewedBy == $this->app->user->account) $canReview = true;
            }
            else
            {
                $leave       = $this->leave->getById($id);
                $createdUser = $this->loadModel('user')->getByAccount($leave->createdBy);
                $dept        = $this->loadModel('tree')->getByID($createdUser->dept);

                if($dept && $this->app->user->account == trim($dept->moderators, ',')) $canReview = true; 
            }
        }

        if($status == 'pass')
        {
            if(!$canReview) $this->send(array('result' => 'fail', 'message' => $this->lang->leave->denied));

            if($mode == 'back')
            {
                $changes = $this->leave->reviewBackDate($id, $status);
                if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

                if($changes)
                {
                    $actionID = $this->loadModel('action')->create('leave', $id, 'reviewed', '', $this->lang->leave->statusList[$status]);
                    $this->action->logHistory($actionID, $changes);
                    $this->sendmail($id, $actionID);
                }
            }
            else
            {
                $this->leave->review($id, $status);
                if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
                
                $actionID = $this->loadModel('action')->create('leave', $id, 'reviewed', '', $this->lang->leave->statusList[$status]);
                $this->sendmail($id, $actionID);
            }
            $this->send(array('result' => 'success'));
        }
        if($status == 'reject')
        {
            if($_POST)
            {

                if(!$canReview) $this->send(array('result' => 'fail', 'message' => $this->lang->leave->denied));

                if(!$this->post->comment) $this->send(array('result' => 'fail', 'message' => array('comment' => sprintf($this->lang->error->notempty, $this->lang->leave->rejectReason))));

                if($mode == 'back')
                {
                    $changes = $this->leave->reviewBackDate($id, $status);
                    if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

                    if($changes)
                    {
                        $actionID = $this->loadModel('action')->create('leave', $id, 'reviewed', '', $this->lang->leave->statusList[$status]);
                        $this->action->logHistory($actionID, $changes);
                        $this->sendmail($id, $actionID);
                    }
                }
                else
                {
                    $this->leave->review($id, $status);
                    if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
                
                    $actionID = $this->loadModel('action')->create('leave', $id, 'reviewed', $this->post->comment, $this->lang->leave->statusList[$status]);
                    $this->sendmail($id, $actionID);
                }
                $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
            }

            $this->view->title = $this->lang->leave->review;
            $this->view->id    = $id;
            $this->view->mode  = $mode;
            $this->display();
        }
    }

    /**
     * Batch review leaves. 
     * 
     * @param  string $status
     * @access public
     * @return void
     */
    public function batchReview($status = 'pass')
    {
        if(!$this->post->leaveIDList) $this->send(array('result' => 'fail', 'message' => $this->lang->leave->nodata));

        /* Check privilage. */
        $canReview   = false;
        $leaveList   = array();
        $leaveIDList = $this->post->leaveIDList;
        if($this->app->user->admin == 'super')
        {
            $canReview = true;
        }
        else
        {
            $reviewedBy = $this->leave->getReviewedBy();
            if($reviewedBy)
            { 
                if($reviewedBy == $this->app->user->account) $canReview = true;
            }
            else
            {
                $leaveList = $this->dao->select('t1.*')->from(TABLE_LEAVE)->alias('t1')
                    ->leftJoin(TABLE_USER)->alias('t2')->on('t1.createdBy=t2.account')
                    ->leftJoin(TABLE_CATEGORY)->alias('t3')->on('t2.dept=t3.id')
                    ->where('t1.id')->in($leaveIDList)
                    ->andWhere('t3.type')->eq('dept')
                    ->andWhere('t3.moderators')->eq(",{$this->app->user->account},")
                    ->fetchAll('id');
                if($leaveList) $canReview = true;
            }
        }

        if($status == 'pass')
        {
            if(!$canReview) $this->send(array('result' => 'fail', 'message' => $this->lang->leave->denied));

            if(!$leaveList) $leaveList = $this->leave->getByIdList($leaveIDList);
            foreach($leaveList as $id => $leave)
            {
                if($leave->status == 'pass' and $leave->backDate != '0000-00-00 00:00:00' and $leave->backDate != "$leave->end $leave->finish")
                {
                    $changes = $this->leave->reviewBackDate($id, $status);
                    if(dao::isError()) continue;

                    if($changes)
                    {
                        $actionID = $this->loadModel('action')->create('leave', $id, 'reviewed', '', $this->lang->leave->statusList[$status]);
                        $this->action->logHistory($actionID, $changes);
                        $this->sendmail($id, $actionID);
                    }
                }
                else
                {
                    $this->leave->review($id, $status);
                    if(dao::isError()) continue;
                    
                    $actionID = $this->loadModel('action')->create('leave', $id, 'reviewed', '', $this->lang->leave->statusList[$status]);
                    $this->sendmail($id, $actionID);
                }
            }
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->send(array('result' => 'success', 'message' => $this->lang->leave->reviewSuccess, 'locate' => 'reload'));
        }
    }

    /**
     * create leave.
     * 
     * @param  string $date
     * @access public
     * @return void
     */
    public function create($date = '')
    {
        if($_POST)
        {
            $result = $this->leave->create();
            if(is_array($result)) $this->send($result);

            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $leaveID  = $result;
            $actionID = $this->loadModel('action')->create('leave', $leaveID, 'created');
            $this->sendmail($leaveID, $actionID);

            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('personal')));
        }

        if($date)
        {
            $date  = date('Y-m-d', strtotime($date));
            $leave = $this->leave->getByDate($date, $this->app->user->account);
            if($leave && strpos(',wait,draft,', $leave->status) !== false) $this->locate(inlink('edit', "id=$leave->id"));
        }

        $this->view->title = $this->lang->leave->create;
        $this->view->date  = $date;
        $this->display();
    }

    /**
     * Edit leave.
     * 
     * @param  int    $id 
     * @access public
     * @return void
     */
    public function edit($id)
    {
        $leave = $this->leave->getById($id);

        /* check privilage. */
        if($this->app->user->admin != 'super' && $leave->createdBy != $this->app->user->account)
        {
            $reviewedBy = $this->leave->getReviewedBy();
            if(!$reviewedBy)
            {
                $createdUser = $this->loadModel('user')->getByAccount($leave->createdBy);
                $dept        = $this->loadModel('tree')->getByID($createdUser->dept);
                $reviewedBy  = empty($dept) ? '' : trim($dept->moderators, ',');
            }

            if($this->app->user->account != $reviewedBy) 
            {
                $locate     = helper::safe64Encode(helper::createLink('oa.leave', 'browse'));
                $noticeLink = helper::createLink('notice', 'index', "type=accessLimited&locate={$locate}");
                die(js::locate($noticeLink));
            }
        }

        if($_POST)
        {
            $result = $this->leave->update($id);
            if(!empty($result['fail']) && $result['result'] == 'fail') $this->send($result);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
            if($result)
            {
                $actionID = $this->loadModel('action')->create('leave', $id, 'edited');
                $this->action->logHistory($actionID, $result);
            }
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $this->view->title = $this->lang->leave->edit;
        $this->view->leave = $leave;
        $this->display();
    }

    /**
     * Back to report.
     * 
     * @param  int    $leaveID 
     * @access public
     * @return void
     */
    public function back($leaveID = 0)
    {
        $leave = $this->leave->getByID($leaveID);
        if($leave->createdBy != $this->app->user->account) 
        {
            $locate     = helper::safe64Encode(helper::createLink('oa.leave', 'personal'));
            $noticeLink = helper::createLink('notice', 'index', "type=accessLimited&locate={$locate}");
            die(js::locate($noticeLink));
        }

        if($_POST)
        {
            if($this->post->backDate < ($leave->begin . ' ' . $leave->start)) $this->send(array('result' => 'fail', 'message' => array('backDate' => $this->lang->leave->wrongBackDate)));

            $changes = $this->leave->back($leaveID);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
            
            if($changes)
            {
                $actionID = $this->loadModel('action')->create('leave', $leaveID, 'reported');
                $this->action->logHistory($actionID, $changes);
                $this->sendmail($leaveID, $actionID);
            }
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $this->view->title = $this->lang->leave->back;
        $this->view->leave = $leave;
        $this->display();
    }

    /**
     * Delete leave.
     * 
     * @param  int    $id 
     * @access public
     * @return void
     */
    public function delete($id)
    {
        $leave = $this->leave->getByID($id);
        if($leave->createdBy != $this->app->user->account) $this->send(array('result' => 'fail', 'message' => $this->lang->leave->denied));

        $this->leave->delete($id);
        if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
        $this->send(array('result' => 'success'));
    }

    /**
     * Send email.
     * 
     * @param  int    $leaveID 
     * @param  int    $actionID 
     * @access public
     * @return void
     */
    public function sendmail($leaveID, $actionID)
    {
        /* Reset $this->output. */
        $this->clear();

        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        /* Set toList and ccList. */
        $leave  = $this->leave->getById($leaveID);
        $users  = $this->loadModel('user')->getPairs();
        $toList = '';
        if($action->action == 'reviewed')
        {
            $toList = $leave->createdBy;
            $subject = "{$this->lang->leave->common}{$this->lang->leave->statusList[$leave->status]}#{$leave->id} " . zget($users, $leave->createdBy);
        }
        elseif(strpos(',created,revoked,commited,reported,', ",$action->action,") !== false)
        {
            $reviewedBy = $this->leave->getReviewedBy();
            if($reviewedBy)
            {
                $toList = $reviewedBy; 
            }
            else
            {
               $dept   = $this->loadModel('tree')->getByID($this->app->user->dept);
               $toList = isset($dept->moderators) ? trim($dept->moderators, ',') : '';
            }

            $subject = "{$this->lang->leave->common}#{$leave->id} " . zget($users, $leave->createdBy);
        }

        /* send notice if user is online and return failed accounts. */
        $toList = $this->loadModel('action')->sendNotice($actionID, $toList);

        /* Create the email content. */
        $this->view->leave  = $leave;
        $this->view->action = $action;
        $this->view->users  = $users;

        $mailContent = $this->parse($this->moduleName, 'sendmail');

        /* Send emails. */
        $this->loadModel('mail')->send($toList, $subject, $mailContent);
        if($this->mail->isError()) trigger_error(join("\n", $this->mail->getError()));
    }

    /**
     * Cancel a leave.
     * 
     * @param  int    $leaveID 
     * @access public
     * @return void
     */
    public function switchStatus($leaveID)
    {
        $leave = $this->leave->getByID($leaveID);
        if(!$leave) return false;
        if($leave->createdBy != $this->app->user->account) $this->send(array('result' => 'fail', 'message' => $this->lang->leave->denied));

        $dates = range(strtotime($leave->begin), strtotime($leave->end), 60*60*24);

        if($leave->status == 'wait')
        {
            $this->dao->update(TABLE_LEAVE)->set('status')->eq('draft')->where('id')->eq($leaveID)->exec();

            $actionID = $this->loadModel('action')->create('leave', $leaveID, 'revoked');
            $this->sendmail($leaveID, $actionID);

            $this->loadModel('attend', 'oa')->batchUpdate($dates, $leave->createdBy);
        }

        if($leave->status == 'draft')
        {
            $this->dao->update(TABLE_LEAVE)->set('status')->eq('wait')->where('id')->eq($leaveID)->exec();

            $actionID = $this->loadModel('action')->create('leave', $leaveID, 'commited');
            $this->sendmail($leaveID, $actionID);

            $this->loadModel('attend', 'oa')->batchUpdate($dates, $leave->createdBy, '', 'leave');
        }

        if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
        $this->send(array('result' => 'success'));
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
                $userDepts[$user->account] = zget($deptList, $user->dept, ' ');
            }

            /* Create field lists. */
            $fields = explode(',', $this->config->leave->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($this->lang->leave->$fieldName) ? $this->lang->leave->$fieldName : $fieldName;
                unset($fields[$key]);
            }
            $fields['dept'] = $this->lang->user->dept;

            $leaves = array();
            if($mode == 'all')
            {
                $leaveQueryCondition = $this->session->leaveQueryCondition;
                if(strpos($leaveQueryCondition, 'limit') !== false) $leaveQueryCondition = substr($leaveQueryCondition, 0, strpos($leaveQueryCondition, 'limit'));
                $stmt = $this->dbh->query($leaveQueryCondition);
                while($row = $stmt->fetch()) $leaves[$row->id] = $row;
            }
            if($mode == 'thisPage')
            {
                $stmt = $this->dbh->query($this->session->leaveQueryCondition);
                while($row = $stmt->fetch()) $leaves[$row->id] = $row;
            }

            foreach($leaves as $leave)
            {
                $leave->createdBy  = zget($userPairs, $leave->createdBy);
                $leave->dept       = zget($userDepts, $leave->createdBy);
                $leave->type       = zget($this->lang->leave->typeList, $leave->type);
                $leave->begin      = $leave->begin . ' ' . $leave->start;
                $leave->end        = $leave->end   . ' ' . $leave->finish;
                $leave->desc       = htmlspecialchars_decode($leave->desc);
                $leave->desc       = str_replace("<br />", "\n", $leave->desc);
                $leave->desc       = str_replace('"', '""', $leave->desc);
                $leave->status     = zget($this->lang->leave->statusList, $leave->status);
                $leave->reviewedBy = zget($userPairs, $leave->reviewedBy);
            }

            $this->post->set('fields', $fields);
            $this->post->set('rows', $leaves);
            $this->post->set('kind', 'leave');
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
            $this->loadModel('setting')->setItem('system.oa.leave..reviewedBy', $this->post->reviewedBy);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess));
        }

        if($module)
        {
            $this->lang->menuGroups->leave = $module;
            $this->lang->leave->menu       = $this->lang->$module->menu;
        }

        $this->view->title      = $this->lang->leave->setReviewer;
        $this->view->users      = $this->loadModel('user', 'sys')->getPairs('noclosed,noforbidden,nodeleted');
        $this->view->reviewedBy = $this->leave->getReviewedBy();
        $this->display();
    }
}
