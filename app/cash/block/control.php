<?php
/**
 * The control file of block module of RanZhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Tingting Dai <daitingting@xirangit.com>
 * @package     block
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
class block extends control
{
    /**
     * Block Index Page.
     * 
     * @access public
     * @return void
     */
    public function index()
    {
        $lang = $this->get->lang;
        $this->app->setClientLang($lang);
        $this->app->loadLang('common', 'cash');
        $this->app->loadLang('block');

        $mode = strtolower($this->get->mode);
        if($mode == 'getblocklist')
        {   
            echo $this->block->getAvailableBlocks();
        }   
        elseif($mode == 'getblockform')
        {   
            $code = strtolower($this->get->blockid);
            $func = 'get' . ucfirst($code) . 'Params';
            echo $this->block->$func();
        }   
        elseif($mode == 'getblockdata')
        {   
            $code = strtolower($this->get->blockid);
            $func = 'print' . ucfirst($code) . 'Block';
            $this->$func();
        }
    }

    /**
     * Block Admin Page.
     * 
     * @param  int    $index 
     * @param  string $blockID 
     * @access public
     * @return void
     */
    public function admin($index = 0, $blockID = '')
    {
        $this->app->loadLang('block', 'sys');
        $title = $index == 0 ? $this->lang->block->createBlock : $this->lang->block->editBlock;

        if(!$index) $index = $this->block->getLastKey('cash') + 1;

        if($_POST)
        {
            $this->block->save($index, 'system', 'cash');
            if(dao::isError())  $this->send(array('result' => 'fail', 'message' => dao::geterror())); 
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->server->http_referer));
        }

        $block   = $this->block->getBlock($index, 'cash');
        $blockID = $blockID ? $blockID : ($block ? $block->block : '');

        $blocks = json_decode($this->block->getAvailableBlocks(), true);
        $this->view->blocks  = array_merge(array(''), $blocks);

        $this->view->title   = $title;
        $this->view->params  = $blockID ? json_decode($this->block->{'get' . ucfirst($blockID) . 'Params'}(), true) : array();
        $this->view->blockID = $blockID;
        $this->view->block   = $block;
        $this->view->index   = $index;
        $this->display();
    }

    /**
     * Sort block. 
     * 
     * @param  string    $oldOrder 
     * @param  string    $newOrder 
     * @access public
     * @return void
     */
    public function sort($oldOrder, $newOrder)
    {
        $this->locate($this->createLink('sys.block', 'sort', "oldOrder=$oldOrder&newOrder=$newOrder&app=cash"));
    }

    /**
     * Resize block 
     * 
     * @param  int    $id 
     * @param  string $type 
     * @param  string $data 
     * @access public
     * @return void
     */
    public function resize($id, $type, $data)
    {
        $this->locate($this->createLink('sys.block', 'resize', "id=$id&type=$type&data=$data"));
    }

    /**
     * Delete block. 
     * 
     * @param  int    $index 
     * @access public
     * @return void
     */
    public function delete($index)
    {
        $this->locate($this->createLink('sys.block', 'delete', "index=$index&app=cash"));
    }

    /**
     * Print depositor block.
     * 
     * @access public
     * @return void
     */
    public function printDepositorBlock()
    {
        $this->app->loadLang('depositor', 'cash');

        $this->processParams();

        $this->view->depositors = $this->dao->select('*')->from(TABLE_DEPOSITOR)->where('type')->ne('cash')->andWhere('status')->eq('normal')->fetchAll('id');
        $this->display();
    }

    /**
     * Print trade block.
     * 
     * @access public
     * @return void
     */
    public function printTradeBlock()
    {
        $this->app->loadLang('trade', 'cash');

        $this->processParams();

        /* Do not get trades which user has no privilege to browse their categories. */
        $denyCategories  = array();
        $outCategories   = $this->dao->select('*')->from(TABLE_CATEGORY)->where('type')->eq('out')->fetchAll('id');
        $allowCategories = $this->loadModel('tree')->process($outCategories);
        $denyCategories  = array_diff(array_keys($outCategories), array_keys($allowCategories));

        $rights = $this->app->user->rights;
        $expensePriv = (isset($rights['tradebrowse']['out']) or $this->app->user->admin == 'super') ? true : false; 

        $this->params->type = !empty($this->params->type) ? $this->params->type : 'all';
        $this->view->trades = $this->dao->select('*')->from(TABLE_TRADE)
            ->where('1=1')
            ->beginIF($this->params->type != 'all')->andWhere('type')->eq($this->params->type)->fi()
            ->beginIF(!empty($denyCategories))->andWhere('category')->notin($denyCategories)
            ->beginIF(!$expensePriv)->andWhere('type')->ne('out')->fi()
            ->orderBy($this->params->orderBy)
            ->limit($this->params->num)
            ->fetchAll('id');

        $this->view->currencySign  = $this->loadModel('common', 'sys')->getCurrencySign();
        $this->view->depositorList = $this->loadModel('depositor', 'cash')->getPairs();
        $this->display();
    }

    /**
     * Print base facts block.
     * 
     * @access public
     * @return void
     */
    public function printBasefactsBlock()
    {
        $this->processParams();
        $this->loadModel('trade');

        $currentYear  = date('Y');

        $currencySign  = $this->loadModel('common', 'sys')->getCurrencySign();
        $annualChartDatas = array();
        foreach($currencySign as $currency => $sign)
        {
            $trades = $this->trade->getByYear($currentYear, $currency); 
            foreach($trades as $month => $monthTrades)
            {
                $annualChartDatas[$currency][$month]['in']  = 0;
                $annualChartDatas[$currency][$month]['out'] = 0;
                foreach($monthTrades as $trade)
                {
                    if($trade->type == 'in')  $annualChartDatas[$currency][$month]['in']  += $trade->money;
                    if($trade->type == 'out') $annualChartDatas[$currency][$month]['out'] += $trade->money;
                }
                $annualChartDatas[$currency][$month]['profit'] = $annualChartDatas[$currency][$month]['in'] - $annualChartDatas[$currency][$month]['out'];
            }
        }

        krsort($annualChartDatas, SORT_STRING);

        $this->view->currencySign      = $currencySign;
        $this->view->annualChartDatas  = $annualChartDatas;
        $this->display();
    }

    /**
     * Print provider block.
     * 
     * @access public
     * @return void
     */
    public function printProviderBlock()
    {
        $this->app->loadLang('provider', 'cash');

        $this->session->set('providerList', $this->createLink('cash.dashboard', 'index'));
        if($this->get->app == 'sys') $this->session->set('providerList', 'javascript:$.openEntry("home")');

        $this->processParams();

        $this->view->providers = $this->dao->select('*')->from(TABLE_CUSTOMER)
            ->where('deleted')->eq(0)
            ->andWhere('relation')->eq('provider')
            ->orderBy($this->params->orderBy)
            ->limit($this->params->num)
            ->fetchAll('id');

        $this->view->areas      = $this->loadModel('tree')->getOptionMenu('area');
        $this->view->industries = $this->tree->getOptionMenu('industry');
        $this->display();
    }

    /**
     * Print report block.
     * 
     * @access public
     * @return void
     */
    public function printReportBlock()
    {
        $this->processParams();

        $currentYear  = date('Y');
        $currentMonth = date('m');

        $datas = $this->loadModel('trade', 'cash')->getChartData($this->params->type, $currentYear, $currentMonth, $this->params->groupBy, $this->params->currency); 
        $datas = $this->loadModel('report', 'sys')->computePercent($datas);

        $this->view->datas        = $datas;
        $this->view->type         = $this->params->type;
        $this->view->groupBy      = $this->params->groupBy;
        $this->view->currentYear  = $currentYear;
        $this->view->currentMonth = $currentMonth;
        $this->display();
    }

    /**
     * Process params.
     * 
     * @access public
     * @return void
     */
    public function processParams()
    {
        $params = $this->get->param;
        $this->params = json_decode(base64_decode($params));

        $this->view->sso  = base64_decode($this->get->sso);
        $this->view->code = strtolower($this->get->blockid);
    }
}
