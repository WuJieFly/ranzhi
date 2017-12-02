<?php
/**
 * The control file of order module of RanZhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Tingting Dai <daitingting@xirangit.com>
 * @package     order
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
class order extends control
{
    /** 
     * The index page, locate to browse.
     * 
     * @access public
     * @return void
     */
    public function index()
    {   
        $this->locate(inlink('browse'));
    }   

    /**
     * Browse order.
     * 
     * @param string $orderBy     the order by
     * @param int    $recTotal 
     * @param int    $recPerPage 
     * @param int    $pageID 
     * @access public
     * @return void
     */
    public function browse($mode = 'all', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {   
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $orders = $this->order->getList($mode, '', $owner = 'all', $orderBy, $pager);

        /* Set pre and next condition. */
        $this->session->set('orderList', $this->app->getURI(true));

        /* Set allowed edit order ID list. */
        $this->app->user->canEditOrderIdList = ',' . implode(',', $this->order->getOrdersSawByMe('edit', array_keys($orders))) . ',';

        /* Build search form. */
        $traders = '';
        if($this->session->orderForm)
        {
            foreach($this->session->orderForm as $formKey => $formValue)
            {
                if(strpos($formKey, 'field') !== false and $formValue == 'o.customer')
                {
                    $fieldNO  = substr($formKey, 5);
                    $traderID = $this->session->orderForm["value{$fieldNO}"];
                    $traders .= $traderID . ',';
                }
            }
        }
        $this->loadModel('search', 'sys');
        $this->config->order->search['actionURL'] = inlink('browse', 'mode=bysearch');
        $this->config->order->search['params']['o.customer']['values'] = $this->loadModel('customer')->getPairs('client', $emptyOption = true, $orderBy = 'id_desc', $limit = $this->config->customerLimit, $traders);
        $this->config->order->search['params']['o.product']['values']  = array('' => '') + $this->loadModel('product')->getPairs();
        $this->search->setSearchParams($this->config->order->search);

        $this->view->title        = $this->lang->order->browse;
        $this->view->orders       = $orders;
        $this->view->customers    = $this->loadModel('customer')->getList('client');
        $this->view->users        = $this->loadModel('user', 'sys')->getPairs();
        $this->view->pager        = $pager;
        $this->view->mode         = $mode;
        $this->view->orderBy      = $orderBy;
        $this->view->currencySign = $this->loadModel('common', 'sys')->getCurrencySign();
        $this->view->currencyList = $this->common->getCurrencyList();
        if($orders) $this->view->totalAmount = $this->order->countAmount($orders);
        $this->display();
    }

    /**
     * Create an order.
     * 
     * @param  int    $customer
     * @access public
     * @return viod
     */
    public function create($customer = 0)
    {
        if($_POST)
        {
            $return = $this->order->create();
            $this->send($return);
        }

        unset($this->lang->order->menu);
        $products = $this->loadModel('product')->getPairs($status = 'normal');
        $this->view->products     = array( 0 => '') + $products;
        $this->view->customers    = $this->loadModel('customer')->getPairs('client', $emptyOption = true, $orderBy = 'id_desc', $limit = $this->config->customerLimit);
        $this->view->title        = $this->lang->order->create;
        $this->view->currencyList = $this->loadModel('common', 'sys')->getCurrencyList();
        $this->view->customer     = $customer;

        $this->display();
    }

    /**
     * Edit an order.
     * 
     * @param  int    $orderID 
     * @param  bool   $comment
     * @access public
     * @return void
     */
    public function edit($orderID, $comment = false)
    {
        $order = $this->order->getByID($orderID);
        $this->loadModel('common', 'sys')->checkPrivByCustomer(empty($order)? '0' : $order->customer, 'edit');

        if($_POST)
        {
            $changes = array();
            if($comment == false)
            {
                $changes = $this->order->update($orderID);
                if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
            }

            if($this->post->comment != '' or !empty($changes))
            {
                $action        = $this->post->comment == '' ? 'Edited' : 'Commented';
                $orderActionID = $this->loadModel('action')->create('order', $orderID, $action, $this->post->comment);
                $this->action->logHistory($orderActionID, $changes);

                $customerActionID = $this->loadModel('action')->create('customer', $order->customer, 'editOrder', $this->post->comment, html::a($this->createLink('order', 'view', "orderID=$order->id"), $order->id));
                $this->action->logHistory($customerActionID, $changes);
            }
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('view', "orderID=$orderID")));
        }

        $this->view->title        = $this->lang->order->edit;
        $this->view->order        = $order;
        $this->view->products     = $this->loadModel('product')->getPairs();
        $this->view->customers    = $this->loadModel('customer')->getPairs('client', $emptyOption = true, $orderBy = 'id_desc', $limit = $this->config->customerLimit, $order->customer);
        $this->view->users        = $this->loadModel('user')->getPairs('nodeleted,noforbidden,noclosed');
        $this->view->currencyList = $this->loadModel('common', 'sys')->getCurrencyList();

        $this->display();
    }

    /**
     * View an order.
     * 
     * @param  int $orderID 
     * @access public
     * @return void
     */
    public function view($orderID)
    {
        $order = $this->order->getByID($orderID);
        $this->loadModel('common', 'sys')->checkPrivByCustomer(empty($order) ? '0' : $order->customer);

        /* Set allowed edit order ID list. */
        $this->app->user->canEditOrderIdList = ',' . implode(',', $this->order->getOrdersSawByMe('edit', (array)$orderID)) . ',';

        $this->app->loadLang('resume', 'crm');
        $this->app->loadLang('contract', 'crm');

        $uri = $this->app->getURI(true);
        $this->session->set('customerList', $uri);
        $this->session->set('productList',  $uri);
        $this->session->set('contactList',  $uri);
        if(!$this->session->contractList) $this->session->set('contractList', $uri);

        $this->view->order        = $order;
        $this->view->title        = $this->lang->order->view;
        $this->view->customer     = $this->loadModel('customer')->getByID($order->customer);
        $this->view->contract     = $this->order->getContract($orderID);
        $this->view->users        = $this->loadModel('user')->getPairs();
        $this->view->currencyList = $this->loadModel('common', 'sys')->getCurrencyList();
        $this->view->currencySign = $this->common->getCurrencySign();
        $this->view->preAndNext   = $this->common->getPreAndNextObject('order', $orderID);
    
        $this->display();
    }
    
    /**
     * Close an order.
     * 
     * @param  int    $orderID 
     * @access public
     * @return void
     */
    public function close($orderID) 
    {
        $order = $this->order->getByID($orderID);
        $this->loadModel('common', 'sys')->checkPrivByCustomer(empty($order)? '0' : $order->customer, 'edit');

        if(!empty($_POST))
        {
            $this->order->close($orderID);
            $this->loadModel('customer')->updateEditedDate($order->customer);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->loadModel('action')->create('order', $orderID, 'Closed', $this->post->closedNote, $this->lang->order->closedReasonList[$this->post->closedReason]);
            $this->loadModel('action')->create('customer', $order->customer, 'closeOrder', $this->lang->order->closedReason . $this->lang->order->closedReasonList[$this->post->closedReason] . '<br />' . $this->post->closedNote, html::a($this->createLink('order', 'view', "orderID=$orderID"), $orderID));

            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->server->http_referer));
        }

        $this->view->title   = $this->lang->order->close;
        $this->view->orderID = $orderID;
        $this->display();
    }

    /**
     * Activate an order.
     * 
     * @param  int    $orderID 
     * @access public
     * @return void
     */
    public function activate($orderID) 
    {
        $order = $this->order->getByID($orderID); 
        $this->loadModel('common', 'sys')->checkPrivByCustomer(empty($order)? '0' : $order->customer, 'edit');

        if(!empty($_POST))
        {
            $this->order->activate($orderID);
            $this->loadModel('customer')->updateEditedDate($order->customer);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
            $this->loadModel('action')->create('order', $orderID, 'Activated', $this->post->comment);
            $this->loadModel('action')->create('customer', $order->customer, 'activateOrder', $this->post->comment, html::a($this->createLink('order', 'view', "orderID=$orderID"), $orderID));
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->server->http_referer));
        }

        $this->view->title   = $this->lang->order->activate;
        $this->view->orderID = $orderID;
        $this->view->users   = $this->loadModel('user')->getPairs('nodeleted,noforbidden,noclosed');
        $this->display();
    }

    /**
     * Get contact of an customer.
     *
     * @param  int    $order
     * @param  string $orderBy     the order by
     * @param  int    $recTotal 
     * @param  int    $recPerPage 
     * @param  int    $pageID 
     * @access public
     * @return void
     */
    public function contact($order, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $order = $this->order->getByID($order);
        $contacts = $this->loadModel('contact', 'crm')->getList($order->customer, $relation = 'client', $mode = '', $status = 'normal', $origin = '', $orderBy, $pager);

        $this->view->title    = $this->lang->order->contact;
        $this->view->contacts = $contacts;
        $this->view->pager    = $pager;
        $this->view->orderBy  = $orderBy;

        $this->display();
    }

    /**
     * Assign an order function.
     *
     * @param  int    $orderID
     * @param  null   $table  
     * @access public
     * @return void
     */
    public function assign($orderID, $table = null)
    {
        $order   = $this->order->getByID($orderID);
        $members = $this->loadModel('user')->getPairs('noclosed,nodeleted,noforbidden');
        $this->loadModel('common', 'sys')->checkPrivByCustomer(empty($order)? '0' : $order->customer, 'edit');

        if($_POST)
        {
            $this->order->assign($orderID);
            $this->loadModel('customer')->updateEditedDate($order->customer);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            if($this->post->assignedTo)
            {
                $actionID = $this->loadModel('action')->create('order', $orderID, 'Assigned', $this->post->comment, $this->post->assignedTo);
                $this->sendmail($orderID, $actionID);

                $this->loadModel('action')->create('customer', $order->customer, 'assignOrder',  $this->lang->order->assignedTo . $members[$this->post->assignedTo] . '<br />' . $this->post->comment, html::a($this->createLink('order', 'view', "orderID=$orderID"), $orderID));
            }


            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->server->http_referer));
        }

        $this->view->title   = $this->lang->order->assignedTo;
        $this->view->orderID = $orderID;
        $this->view->order   = $order;
        $this->view->members = $members;
        $this->display();
    }

    /**
     * delete 
     * 
     * @param  int    $orderID 
     * @access public
     * @return void
     */
    public function delete($orderID)
    {
        $this->order->delete(TABLE_ORDER, $orderID);
        if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
        $this->send(array('result' => 'success'));
    }

    /**
     * Send email.
     * 
     * @param  int    $orderID 
     * @param  int    $actionID 
     * @access public
     * @return void
     */
    public function sendmail($orderID, $actionID)
    {
        /* Reset $this->output. */
        $this->clear();

        /* Set toList and ccList. */
        $order  = $this->order->getByIdList($orderID);
        $users  = $this->loadModel('user')->getPairs();
        $order  = $order[$orderID];
        $toList = $order->assignedTo;

        /* send notice if user is online and return failed accounts. */
        $toList = $this->loadModel('action')->sendNotice($actionID, $toList);

        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        /* Create the email content. */
        $this->view->order    = $order;
        $this->view->action   = $action;
        $this->view->users    = $users;
        $this->view->contract = $this->order->getContract($orderID);

        $mailContent = $this->parse($this->moduleName, 'sendmail');

        /* Send emails. */
        $this->loadModel('mail')->send($toList, 'ORDER#' . $order->id . ' ' . $order->title, $mailContent);
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
            $orderLang   = $this->lang->order;
            $orderConfig = $this->config->order;

            /* Create field lists. */
            $fields = explode(',', $orderConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($orderLang->$fieldName) ? $orderLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            $orders = array();
            if($mode == 'all')
            {
                $orderQueryCondition = $this->session->orderQueryCondition;
                if(strpos($orderQueryCondition, 'limit') !== false) $orderQueryCondition = substr($orderQueryCondition, 0, strpos($orderQueryCondition, 'limit'));
                $stmt = $this->dbh->query($orderQueryCondition);
                while($row = $stmt->fetch()) $orders[$row->id] = $row;
            }

            if($mode == 'thisPage')
            {
                $stmt = $this->dbh->query($this->session->orderQueryCondition);
                while($row = $stmt->fetch()) $orders[$row->id] = $row;
            }

            /* Get users, products and projects. */
            $users    = $this->loadModel('user')->getPairs();
            $products = $this->loadModel('product')->getPairs();

            foreach($orders as $order)
            {
                if(isset($order->products)) continue;
                $order->products = array();
                $productList = explode(',', $order->product);
                foreach($productList as $product) if(isset($products[$product])) $order->products[] = $products[$product];
            }

            foreach($orders as $order)
            {
                /* fill some field with useful value. */
                if(isset($orderLang->statusList[$order->status]))             $order->status       = $orderLang->statusList[$order->status];
                if(isset($orderLang->closedReasonList[$order->closedReason])) $order->closedReason = $orderLang->closedReasonList[$order->closedReason];
                if(isset($this->lang->currencyList[$order->currency]))        $order->currency     = $this->lang->currencyList[$order->currency];

                if(isset($users[$order->createdBy]))   $order->createdBy   = $users[$order->createdBy];
                if(isset($users[$order->editedBy]))    $order->editedBy    = $users[$order->editedBy];
                if(isset($users[$order->assignedTo]))  $order->assignedTo  = $users[$order->assignedTo];
                if(isset($users[$order->assignedBy]))  $order->assignedBy  = $users[$order->assignedBy];
                if(isset($users[$order->signedBy]))    $order->signedBy    = $users[$order->signedBy];
                if(isset($users[$order->activatedBy])) $order->activatedBy = $users[$order->activatedBy];
                if(isset($users[$order->contactedBy])) $order->contactedBy = $users[$order->contactedBy];
                if(isset($users[$order->closedBy]))    $order->closedBy    = $users[$order->closedBy]; 

                $order->createdDate   = formatTime($order->createdDate, DT_DATE1);
                $order->editedDate    = formatTime($order->editedDate, DT_DATE1);
                $order->assignedDate  = formatTime($order->assignedDate, DT_DATE1);
                $order->signedDate    = formatTime($order->signedDate, DT_DATE1);
                $order->activatedDate = formatTime($order->activatedDate, DT_DATE1);
                $order->contactedDate = formatTime($order->contactedDate, DT_DATE1);
                $order->nextDate      = formatTime($order->contactedDate, DT_DATE1);
                $order->closedDate    = formatTime($order->closedDate, DT_DATE1);

                $order->customer = $order->customerName;
                if(!empty($order->products)) $order->product = join("; \n", $order->products);
            }

            $this->post->set('fields', $fields);
            $this->post->set('rows', $orders);
            $this->post->set('kind', 'order');
            $this->fetch('file', 'export2CSV' , $_POST);
        }

        $this->display();
    }

    /**
     * ajax get orders this week need conect. 
     * 
     * @param  string $account   not used.
     * @param  string $id 
     * @param  string $type      select|json|board
     * @access public
     * @return void
     */
    public function ajaxGetTodoList($account = '', $id = '', $type = 'select')
    {
        $this->app->loadClass('date', $static = true);
        $customerIdList = $this->loadModel('customer')->getCustomersSawByMe();
        $products       = $this->loadModel('product')->getPairs();
        $thisWeek       = date::getThisWeek();
        $orders         = array();
        if($account == '') $account = $this->app->user->account;

        $sql = $this->dao->select('o.id, o.product, o.createdDate, c.name as customerName, t.id as todo')->from(TABLE_ORDER)->alias('o')
            ->leftJoin(TABLE_CUSTOMER)->alias('c')->on("o.customer=c.id")
            ->leftJoin(TABLE_TODO)->alias('t')->on("t.type='order' and o.id=t.idvalue")
            ->where('o.deleted')->eq(0)
            ->andWhere('o.assignedTo')->eq($account)
            ->andWhere('o.nextDate')->between($thisWeek['begin'], $thisWeek['end'])
            ->andWhere('o.customer')->in($customerIdList)
            ->orderBy('o.id_desc');
        $stmt = $sql->query();
        while($order = $stmt->fetch())
        {    
            if($order->todo) continue;
            $order->products = array();
            $productList = explode(',', $order->product);
            foreach($productList as $product) if(isset($products[$product])) $order->products[] = $products[$product];
            $productName  = count($order->products) > 1 ? current($order->products) . $this->lang->etc : current($order->products);
            $order->title = sprintf($this->lang->order->titleLBL, $order->customerName, $productName, date('Y-m-d', strtotime($order->createdDate))); 
            $orders[$order->id] = $order->title;
        } 

        if($type == 'select')
        {
            if($id) die(html::select("idvalues[$id]", $orders, '', 'class="form-control"'));
            die(html::select('idvalue', $orders, '', 'class=form-control'));
        }
        if($type == 'board')
        {
            die($this->loadModel('todo', 'sys')->buildBoardList($orders, 'order'));
        }
        die(json_encode($orders));
    }
}
