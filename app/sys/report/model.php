<?php
/**
 * The model file of report module of RanZhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     report
 * @version     $Id: model.php 4726 2013-05-03 05:51:27Z chencongzhi520@gmail.com $
 * @link        http://www.ranzhico.com
 */
?>
<?php
class reportModel extends model
{
    /**
     * Create single JSON. 
     * 
     * @param  array    $sets 
     * @param  array    $dateList 
     * @access public
     * @return string
     */
    public function createSingleJSON($sets, $dateList)
    {
        $data = '[';
        foreach($dateList as $i => $date)
        {
            $date = date('Y-m-d', strtotime($date));
            if(isset($sets[$date]))$data .= "[$i, {$sets[$date]->value}],";
        }
        $data = rtrim($data, ',');
        $data .= ']';
        return $data;
    }

    /**
     * Compute percent of every item.
     * 
     * @param  array    $datas 
     * @access public
     * @return array
     */
    public function computePercent($datas)
    {
        $sum = 0;
        foreach($datas as $data) $sum += $data->value;
        foreach($datas as $data) $data->percent = $sum == 0 ? 1 : round($data->value / $sum, 2);
        return $datas;
    }

    /**
     * Compute sum of datas.
     * 
     * @param  array    $datas 
     * @access public
     * @return array
     */
    public function computeSum($datas)
    {
        $sum = 0;
        foreach($datas as $data) $sum += $data->value;
        return $sum;
    }

    /**
     * Get System URL.
     * 
     * @access public
     * @return void
     */
    public function getSysURL()
    {
        /* Ger URL when run in shell. */
        if(PHP_SAPI == 'cli')
        {
            $url = parse_url(trim($this->server->argv[1]));
            $port = (empty($url['port']) or $url['port'] == 80) ? '' : $url['port'];
            $host = empty($port) ? $url['host'] : $url['host'] . ':' . $port;
            return $url['scheme'] . '://' . $host;
        }
        else
        {
            return commonModel::getSysURL();
        }
    }

    /**
     * getChartData 
     * 
     * @param  string $module 
     * @param  string $chart 
     * @param  string $tableName 
     * @param  string $groupBy 
     * @param  string $currency '' 
     * @access public
     * @return void
     */
    public function getChartData($module, $chart, $tableName, $groupBy, $currency = '')
    {
        list($groupBy, $field, $func) = explode('|', $groupBy);
        if(empty($field)) $field = $groupBy;
        if(empty($func))  $func = 'count';

        /* process lang list. */
        if(isset($this->config->report->{$module}->listName[$chart]))
        {
            if($chart == 'productLine' or $chart == 'productLineA') $this->app->loadLang('product');

            $this->app->loadLang($module);
            $listName = $this->config->report->{$module}->listName[$chart];

            /* Set list. */
            if($listName == 'USERS')      $list = $this->loadModel('user')->getPairs();
            if($listName == 'AREA')       $list = $this->loadModel('tree')->getOptionMenu('area', 0, true);
            if($listName == 'INDUSTRY')   $list = $this->loadModel('tree')->getOptionMenu('industry', 0, true);
            if($listName == 'PRODUCTS')   $list = $this->loadModel('product')->getPairs();
            if($listName == 'CUSTOMERS')  $list = $this->loadModel('customer')->getPairs();
            if($listName == 'DEPOSITORS') $list = $this->loadModel('depositor')->getPairs();
            if(!isset($list))
            {
                if($chart == 'productLine' or $chart == 'productLineA')
                {
                    $list = $this->lang->product->lineList;
                }
                else
                {
                    $list = $this->lang->{$module}->{$listName};
                }
            }
        }

        if(strpos($groupBy, '_multi') !== false and isset($list))
        {
            $datas   = array();
            $groupBy = str_replace('_multi', '', $groupBy);
            $field   = str_replace('_multi', '', $field);

            if(strpos($chart, 'productLine') !== false)
            {
                if($chart == 'productLine') $field = 'product';

                foreach($list as $key => $value)
                {
                    if($key == 'default') continue;
                    $productList = $this->dao->select('id')->from(TABLE_PRODUCT)->where('line')->eq($key)->fetchAll('id');

                    $data = new stdclass();
                    $data->name  = $key;
                    $data->value = 0; 
                    foreach($productList as $id => $product)
                    {
                        $count = $this->dao->select("$func($field) as value")->from($tableName)
                            ->where('deleted')->eq('0')
                            ->andWhere('product')->like("%,$id,%")
                            ->beginIf($currency != '')->andWhere('currency')->eq($currency)->fi()
                            ->fetch('value');

                        $data->value += $count; 
                    }

                    if($data->value != 0) $datas[$key] = $data;
                }
            }
            else
            {
                foreach($list as $key => $value)
                {
                    $count = $this->dao->select("$func($field) as value")->from($tableName)
                        ->where('deleted')->eq('0')
                        ->andWhere($groupBy)->like("%,$key,%")
                        ->beginIf($currency != '')->andWhere('currency')->eq($currency)->fi()
                        ->fetch('value');

                    $data = new stdclass();
                    $data->name  = $key;
                    $data->value = $count; 
                    if($count != 0) $datas[$key] = $data;
                }
            }
        }
        else
        {
            if($module == 'customer' && $tableName == TABLE_CUSTOMER)
            {
                $relation       = 'client';
                $customerIdList = $this->session->customerIdList;

                if($this->session->customerQuery == false) $this->session->set('customerQuery', ' 1 = 1');
                $customerQuery = $this->loadModel('search', 'sys')->replaceDynamic($this->session->customerQuery);
            }
            else
            {
                $relation       = '';
                $customerIdList = '';
                $customerQuery  = '';
            }
            $datas = $this->dao->select("$groupBy as name, $func($field) as value")->from($tableName)
                ->where('deleted')->eq('0')
                ->beginIF($currency != '')->andWhere('currency')->eq($currency)->fi()
                ->beginIF($relation)->andWhere('relation')->eq($relation)->fi()
                ->beginIF($customerIdList)->andWhere('id')->in($customerIdList)->fi()
                ->beginIF($customerQuery)->andWhere($customerQuery)->fi()
                ->groupBy($groupBy)
                ->orderBy('value_desc')
                ->fetchAll('name');
        }

        /* Add names. */
        if(isset($this->config->report->{$module}->listName[$chart]))
        {
            foreach($datas as $name => $data) $data->name = isset($list[$name]) ? $list[$name] : $this->lang->report->undefined;
        }

        $temp = array();
        foreach($datas as $key => $data) $temp[$key] = $data->value;
        arsort($temp);
        foreach($datas as $key => $data) $temp[$key] = $data;

        return $temp;
    }

    /**
     * Get user tasks.
     * 
     * @access public
     * @return array
     */
    public function getUserTasks()
    {
        $tasks = $this->dao->select('t1.id, t1.name, t2.account as user')->from(TABLE_TASK)->alias('t1')
            ->leftJoin(TABLE_USER)->alias('t2')->on('t1.assignedTo = t2.account')
            ->leftJoin(TABLE_PROJECT)->alias('t3')->on('t1.project = t3.id')
            ->where('t1.assignedTo')->ne('')
            ->andWhere('t1.deleted')->eq(0)
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('t3.deleted')->eq(0)
            ->andWhere('t1.status')->in('wait, doing')
            ->andWhere('t3.status')->ne('suspended')
            ->fetchGroup('user');

        return $tasks;
    }

    /**
     * Get user todos.
     * 
     * @access public
     * @return array
     */
    public function getUserTodos()
    {
        $stmt = $this->dao->select('*')->from(TABLE_TODO)->where('status')->eq('wait')->orWhere('status')->eq('doing')->query();

        /* Get todos and task, customer, order pairs. */
        $todos = array();
        while($todo = $stmt->fetch())
        {
			if($todo->type == 'task')     $taskTodoPairs[$todo->id]     = $todo->idvalue;
			if($todo->type == 'customer') $customerTodoPairs[$todo->id] = $todo->idvalue; 
			if($todo->type == 'order')    $orderTodoPairs[$todo->id]    = $todo->idvalue;

            $todos[$todo->id] = $todo;
        }

        /* Query by pairs. */
        $tasks     = $this->dao->select('id,name')->from(TABLE_TASK)->where('id')->in($taskTodoPairs)->fetchPairs('id', 'name');
        $customers = $this->dao->select('id,name')->from(TABLE_CUSTOMER)->where('id')->in($customerTodoPairs)->fetchPairs('id', 'name');
        $orders    = $this->dao->select('o.id, c.name, o.createdDate')->from(TABLE_ORDER)->alias('o')
            ->leftJoin(TABLE_CUSTOMER)->alias('c')->on('o.customer=c.id')
            ->where('o.deleted')->eq(0)
            ->andWhere('c.deleted')->eq(0)
            ->andWhere('o.id')->in($orderTodoPairs)
            ->fetchAll('id'); 

        /* Set todo name and group them. */
        $userTodos = array();
        foreach($todos as $todoID => $todo)
        {
			if($todo->type == 'task')     $todo->name = $tasks[$todo->idvalue];
			if($todo->type == 'customer') $todo->name = $customers[$todo->idvalue]; 
			if($todo->type == 'order') 
			{
				$order = $orders[$todo->idvalue];
				$todo->name = $order->name . '|' . substr($order->createdDate, 0, 10);
			}

            $user = $todo->assignedTo ? $todo->assignedTo : $todo->account;
            $userTodos[$user][] = $todo;
        }
        return $userTodos;
    }

    /**
     * Get user orders.
     * 
     * @access public
     * @return array
     */
    public function getUserOrders()
    {
        $this->app->loadLang('order', 'crm');
        $today = helper::today();

		/* Get all orders. */
        $orders = $this->dao->select('o.*, c.name as customerName, c.level as level')->from(TABLE_ORDER)->alias('o')
            ->leftJoin(TABLE_CUSTOMER)->alias('c')->on("o.customer=c.id")
            ->where('o.deleted')->eq(0)
            ->andWhere('c.deleted')->eq(0)
            ->andWhere("((o.nextDate != '0000-00-00' and o.nextDate < '{$today}' and o.status != 'closed') or o.nextDate = '$today')")
            ->orderBy("o.id")->fetchAll('id');

        /* Process order product. */
        $products = $this->loadModel('product')->getPairs();
        foreach($orders as $orderID => $order)
        {
            if(empty($order->assignedTo))
            {
                unset($orders[$orderID]);
                continue;
            }

            $order->products = array();
            $productList = explode(',', $order->product);
            foreach($productList as $product) if(isset($products[$product])) $order->products[] = $products[$product];
        }

        /* Set order title and group them. */
        $userOrders = array();
        foreach($orders as $order)
        {
            $productName  = count($order->products) > 1 ? current($order->products) . $this->lang->etc : current($order->products);
            $order->title = sprintf($this->lang->order->titleLBL, $order->customerName, $productName, date('Y-m-d', strtotime($order->createdDate))); 

            $userOrders[$order->assignedTo][] = $order;
        }

        return $userOrders;
    }

    /**
     * Get user customers.
     * 
     * @access public
     * @return array
     */
    public function getUserCustomers()
    {
        return $this->dao->select('*')->from(TABLE_CUSTOMER)
            ->where('deleted')->eq(0)
            ->andWhere('relation')->ne('provider')
            ->andWhere('nextDate')->le(helper::today())->fi()
			->andWhere('nextDate')->ne('0000-00-00')
            ->orderBy('id')
            ->fetchGroup('assignedTo');
    }

    /**
     * Get user contract count.
     * 
     * @access public
     * @return array
     */
    public function getUserContractCount()
    {
        return $this->dao->select('signedBy, count(*) as count')->from(TABLE_CONTRACT)
            ->where('deleted')->eq(0)
            ->andWhere('signedBy')->ne('')
            ->andWhere('status')->eq('normal')
            ->groupBy('signedBy')
            ->fetchPairs('signedBy', 'count');
    }
}

/**
 * Sort summary. 
 * 
 * @param  array    $pre 
 * @param  array    $next 
 * @access public
 * @return int
 */
function sortSummary($pre, $next)
{
    if($pre['validRate'] == $next['validRate']) return 0;
    return $pre['validRate'] > $next['validRate'] ? -1 : 1;
}
