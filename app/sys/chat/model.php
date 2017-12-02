<?php
class chatModel extends model
{
    /**
     * Reset user status. 
     * 
     * @param  string $status 
     * @access public
     * @return bool
     */
    public function resetUserStatus($status = 'offline')    
    {
        $this->dao->update(TABLE_USER)->set('status')->eq($status)->exec();
        return !dao::isError();
    }

    /**
     * Create a system chat. 
     * 
     * @access public
     * @return bool
     */
    public function createSystemChat()
    {
        $chat = $this->dao->select('*')->from(TABLE_IM_CHAT)->where('type')->eq('system')->fetch();
        if(!$chat)
        {
            $id   = md5(time(). mt_rand());
            $chat = new stdclass();
            $chat->gid         = substr($id, 0, 8) . '-' . substr($id, 8, 4) . '-' . substr($id, 12, 4) . '-' . substr($id, 16, 4) . '-' . substr($id, 20, 12);
            $chat->name        = 'system group';
            $chat->type        = 'system';
            $chat->createdBy   = 'system';
            $chat->createdDate = helper::now();

            $this->dao->insert(TABLE_IM_CHAT)->data($chat)->exec();
        }
        return !dao::isError();
    }

    /**
     * Get signed time. 
     * 
     * @param  string $account 
     * @access public
     * @return string | int 
     */
    public function getSignedTime($account = '')
    {
        $this->app->loadModuleConfig('attend');
        if(strpos(',all,xuanxuan,', ",{$this->config->attend->signInClient},") === false) return '';

        $attend = $this->dao->select('*')->from(TABLE_ATTEND)->where('account')->eq($account)->andWhere('`date`')->eq(date('Y-m-d'))->fetch();
        if($attend) return strtotime("$attend->date $attend->signIn");
        
        return time(); 
    }

    /**
     * Get a user. 
     * 
     * @param  int    $userID 
     * @access public
     * @return object 
     */
    public function getUserByUserID($userID = 0)
    {
        $user = $this->dao->select('id, account, realname, avatar, role, dept, status, admin, gender, email, mobile, phone, site')->from(TABLE_USER)->where('id')->eq($userID)->fetch();
        if($user)
        {
            $user->id     = (int)$user->id;
            $user->dept   = (int)$user->dept;
            $user->avatar = !empty($user->avatar) ? commonModel::getSysURL() . $user->avatar : $user->avatar;
        }

        return $user;
    }
    
    /**
     * Get user list. 
     * 
     * @param  string $status
     * @param  array  $idList
     * @access public
     * @return array
     */
    public function getUserList($status = '', $idList = array(), $idAsKey= true)
    {
        $dao = $this->dao->select('id, account, realname, avatar, role, dept, status, admin, gender, email, mobile, phone, site')
            ->from(TABLE_USER)->where('deleted')->eq('0')
            ->beginIF($status && $status == 'online')->andWhere('status')->ne('offline')->fi()
            ->beginIF($status && $status != 'online')->andWhere('status')->eq($status)->fi()
            ->beginIF($idList)->andWhere('id')->in($idList)->fi();
        if($idAsKey) 
        {
            $users = $dao->fetchAll('id');
        }
        else
        {
            $users = $dao->fetchAll();
        }

        foreach($users as $user) 
        {
            $user->id     = (int)$user->id;
            $user->dept   = (int)$user->dept;
            $user->avatar = !empty($user->avatar) ? commonModel::getSysURL() . $user->avatar : $user->avatar;
        }

        return $users;
    }
    
    /**
     * Edit a user. 
     * 
     * @param  object $user 
     * @access public
     * @return object 
     */
    public function editUser($user = null)
    {
        if(empty($user->id)) return null;
        $this->dao->update(TABLE_USER)->data($user)->where('id')->eq($user->id)->exec();
        return $this->getUserByUserID($user->id);
    }

    /**
     * Get member list by gid.  
     * 
     * @param  string $gid 
     * @access public
     * @return array
     */
    public function getMemberListByGID($gid = '')
    {
        $chat = $this->getByGID($gid);
        if(!$chat) return array();

        if($chat->type == 'system')
        {
            $memberList = $this->dao->select('id')->from(TABLE_USER)->where('deleted')->eq('0')->fetchPairs();
        }
        else
        {
            $memberList = $this->dao->select('user as id')
                ->from(TABLE_IM_CHATUSER)
                ->where('quit')->eq('0000-00-00 00:00:00')
                ->beginIF($gid)->andWhere('cgid')->eq($gid)->fi()
                ->fetchPairs();
        }
        
        $members = array();
        foreach($memberList as $member) $members[] = (int)$member;

        return $members;
    }

    /**
     * Get message list. 
     * 
     * @param  array  $idList 
     * @access public
     * @return array 
     */
    public function getMessageList($idList = array(), $pager = null)
    {
        $messages = $this->dao->select('*')
            ->from(TABLE_IM_MESSAGE)
            ->where('1')
            ->beginIF($idList)->andWhere('id')->in($idList)->fi()
            ->orderBy('id_desc')
            ->page($pager)
            ->fetchAll();

        foreach($messages as $message) 
        {
            $message->id   = (int)$message->id;
            $message->user = (int)$message->user;
            $message->date = strtotime($message->date);
        }

        return $messages;
    }

    /**
     * Get message list by cgid.  
     * 
     * @param  string $cgid 
     * @access public
     * @return array
     */
    public function getMessageListByCGID($cgid = '', $pager = null)
    {
        $messages = $this->dao->select('*')->from(TABLE_IM_MESSAGE)
            ->where('cgid')->eq($cgid)
            ->orderBy('id_desc')
            ->page($pager)
            ->fetchAll();

        foreach($messages as $message)
        {
            $message->id   = (int)$message->id;
            $message->user = (int)$message->user;
            $message->date = strtotime($message->date);
        }

        return $messages;
    }

    /**
     * Get chat list. 
     * 
     * @param  bool   $public 
     * @access public
     * @return array 
     */
    public function getList($public = true)
    {
        $chats = $this->dao->select('*')->from(TABLE_IM_CHAT)->where('public')->eq($public)->fetchAll();

        foreach($chats as $chat) 
        {
            $chat->id             = (int)$chat->id;
            $chat->subject        = (int)$chat->subject;
            $chat->public         = (int)$chat->public;
            $chat->createdDate    = strtotime($chat->createdDate);
            $chat->editedDate     = $chat->editedDate == '0000-00-00 00:00:00' ? '' : strtotime($chat->editedDate);
            $chat->lastActiveTime = $chat->lastActiveTime == '0000-00-00 00:00:00' ? '' : strtotime($chat->lastActiveTime);
        }

        return $chats;
    }

    /**
     * Get chat list by userID.  
     * 
     * @param  int    $userID
     * @param  bool   $star
     * @access public
     * @return array
     */
    public function getListByUserID($userID = 0, $star = false)
    {
        $systemChat = $this->dao->select('*, 0 as star, 0 as hide, 0 as mute')
            ->from(TABLE_IM_CHAT)
            ->where('type')->eq('system')
            ->fetchAll();

        $chats = $this->dao->select('t1.*, t2.star, t2.hide, t2.mute')
            ->from(TABLE_IM_CHAT)->alias('t1')
            ->leftjoin(TABLE_IM_CHATUSER)->alias('t2')->on('t1.gid=t2.cgid')
            ->where('t2.quit')->eq('0000-00-00 00:00:00')
            ->andWhere('t2.user')->eq($userID)
            ->beginIF($star)->andWhere('t2.star')->eq($star)->fi()
            ->fetchAll();

        $chats = array_merge($systemChat, $chats);

        foreach($chats as $chat)
        {
            $chat->id             = (int)$chat->id;
            $chat->subject        = (int)$chat->subject;
            $chat->public         = (int)$chat->public;
            $chat->createdDate    = strtotime($chat->createdDate);
            $chat->editedDate     = $chat->editedDate == '0000-00-00 00:00:00' ? '' : strtotime($chat->editedDate);
            $chat->lastActiveTime = $chat->lastActiveTime == '0000-00-00 00:00:00' ? '' : strtotime($chat->lastActiveTime);
            $chat->star           = (int)$chat->star;
            $chat->hide           = (int)$chat->hide;
            $chat->mute           = (int)$chat->mute;
        }

        return $chats;
    }

    /**
     * Get a chat by gid.  
     * 
     * @param  string $gid 
     * @param  bool   $members
     * @access public
     * @return object 
     */
    public function getByGID($gid = '', $members = false)
    {
        $chat = $this->dao->select('*')->from(TABLE_IM_CHAT)->where('gid')->eq($gid)->fetch();
        if($chat)
        {
            $chat->id             = (int)$chat->id;
            $chat->subject        = (int)$chat->subject;
            $chat->public         = (int)$chat->public;
            $chat->createdDate    = strtotime($chat->createdDate);
            $chat->editedDate     = $chat->editedDate == '0000-00-00 00:00:00' ? '' : strtotime($chat->editedDate);
            $chat->lastActiveTime = $chat->lastActiveTime == '0000-00-00 00:00:00' ? '' : strtotime($chat->lastActiveTime);

            if($members) $chat->members = $this->getMemberListByGID($gid);
        }

        return $chat;
    }

    /**
     * Get offline messages. 
     * 
     * @param  int    $userID 
     * @access public
     * @return array 
     */
    public function getOfflineMessages($userID = 0)
    {
        $messages = array();
        $dataList = $this->dao->select('*')->from(TABLE_IM_USERMESSAGE)->where('user')->eq($userID)->orderBy('level, id')->fetchAll();
        foreach($dataList as $data)
        {
            $messages = array_merge($messages, json_decode($data->message));
        }
        if(!dao::isError()) $this->dao->delete()->from(TABLE_IM_USERMESSAGE)->where('user')->eq($userID)->exec();
        return $messages;
    }

    /**
     * Create a chat. 
     * 
     * @param  string $gid 
     * @param  string $name 
     * @param  string $type 
     * @param  array  $members 
     * @param  int    $subjectID 
     * @param  bool   $public
     * @param  int    $userID
     * @access public
     * @return object 
     */
    public function create($gid = '', $name = '', $type = '', $members = array(), $subjectID = 0, $public = false, $userID = 0)
    {
        $user = $this->getUserByUserID($userID);

        $chat = new stdclass();
        $chat->gid         = $gid;
        $chat->name        = $name;
        $chat->type        = $type;
        $chat->subject     = $subjectID;
        $chat->createdBy   = !empty($user->account) ? $user->account : '';
        $chat->createdDate = helper::now();

        if($public) $chat->public = 1;

        $this->dao->insert(TABLE_IM_CHAT)->data($chat)->exec();

        /* Add members to chat. */
        foreach($members as $member)
        {
            $this->joinChat($gid, $member);
        }

        return $this->getByGID($gid, true);
    }

    /**
     * Update a chat. 
     * 
     * @param  object $chat
     * @param  int    $userID
     * @access public
     * @return object
     */
    public function update($chat = null, $userID = 0)
    {
        if($chat)
        {
            $user = $this->getUserByUserID($userID);
            $chat->editedBy   = !empty($user->account) ? $user->account : '';
            $chat->editedDate = helper::now();
            $this->dao->update(TABLE_IM_CHAT)->data($chat)->where('gid')->eq($chat->gid)->batchCheck($this->config->chat->require->edit, 'notempty')->exec();
        }

        /* Return the changed chat. */
        return $this->getByGID($chat->gid, true);
    }

    /**
     * Set admins of a chat. 
     * 
     * @param  string $gid 
     * @param  array  $admins 
     * @param  bool   $isAdmin 
     * @access public
     * @return object
     */
    public function setAdmin($gid = '', $admins = array(), $isAdmin = true)
    {
        $chat = $this->getByGID($gid);
        $adminList = explode(',', $chat->admins);
        foreach($admins as $admin)
        {
            if($isAdmin)
            {
                $adminList[] = $admin;
            }
            else
            {
                $key = array_search($admin, $adminList);
                if($key) unset($adminList[$key]);
            }
        }
        $adminList = implode(',', $adminList);
        $this->dao->update(TABLE_IM_CHAT)->set('admins')->eq($adminList)->where('gid')->eq($gid)->exec();

        return $this->getByGID($gid, true);
    }

    /**
     * Star or cancel star a chat. 
     * 
     * @param  string $gid 
     * @param  bool   $star 
     * @param  int    $userID
     * @access public
     * @return object 
     */
    public function starChat($gid = '', $star = true, $userID = 0)
    {
        $this->dao->update(TABLE_IM_CHATUSER)
            ->set('star')->eq($star)
            ->where('cgid')->eq($gid)
            ->andWhere('user')->eq($userID)
            ->exec();

        return $this->getByGID($gid, true);
    }

    /**
     * Hide or display a chat. 
     * 
     * @param  string $gid 
     * @param  bool   $hide 
     * @param  int    $userID
     * @access public
     * @return bool 
     */
    public function hideChat($gid = '', $hide = true, $userID = 0)
    {
        $this->dao->update(TABLE_IM_CHATUSER)
            ->set('hide')->eq($hide)
            ->where('cgid')->eq($gid)
            ->andWhere('user')->eq($userID)
            ->exec();

        return !dao::isError();
    }

    /**
     * Join or quit a chat. 
     * 
     * @param  string $gid 
     * @param  int    $userID 
     * @param  bool   $join 
     * @access public
     * @return bool
     */
    public function joinChat($gid = '', $userID = 0, $join = true)
    {
        if($join)
        {
            /* Join chat. */
            $data = $this->dao->select('*')->from(TABLE_IM_CHATUSER)->where('cgid')->eq($gid)->andWhere('user')->eq($userID)->fetch();
            if($data) 
            {
                /* If user hasn't quit the chat then return. */
                if($data->quit == '0000-00-00 00:00:00') return true;

                /* If user has quited the chat then update the record. */
                $data = new stdclass();
                $data->join = helper::now();
                $data->quit = '0000-00-00 00:00:00';
                $this->dao->update(TABLE_IM_CHATUSER)->data($data)->where('cgid')->eq($gid)->andWhere('user')->eq($userID)->exec();

                return !dao::isError();
            }

            /* Create a new record about user's chat info. */
            $data = new stdclass();
            $data->cgid = $gid;
            $data->user = $userID;
            $data->join = helper::now();

            $this->dao->insert(TABLE_IM_CHATUSER)->data($data)->exec();

            $id = $this->dao->lastInsertID();
            
            $this->dao->update(TABLE_IM_CHATUSER)->set('`order`')->eq($id)->where('id')->eq($id)->exec();
        }
        else
        {
            /* Quit chat. */
            $this->dao->update(TABLE_IM_CHATUSER)->set('quit')->eq(helper::now())->where('cgid')->eq($gid)->andWhere('user')->eq($userID)->exec();
        }
        return !dao::isError();
    }

    /**
     * Create messages.  
     * 
     * @param  array  $messageList 
     * @param  int    $userID
     * @access public
     * @return array 
     */
    public function createMessage($messageList = array(), $userID = 0)
    {
        $idList   = array();
        $chatList = array();
        foreach($messageList as $message)
        {
            $msg = $this->dao->select('*')->from(TABLE_IM_MESSAGE)->where('gid')->eq($message->gid)->fetch();
            if($msg)
            {
                if($msg->contentType == 'image' || $msg->contentType == 'file')
                {
                    $this->dao->update(TABLE_IM_MESSAGE)->set('content')->eq($message->content)->where('id')->eq($msg->id)->exec();
                }
                $idList[] = $msg->id;
            }
            elseif(!$msg)
            {
                if(!(isset($message->user) && $message->user)) $message->user = $userID;
                if(!(isset($message->date) && $message->date)) $message->date = helper::now();
                
                $this->dao->insert(TABLE_IM_MESSAGE)->data($message)->exec();
                $idList[] = $this->dao->lastInsertID();
            }
            $chatList[$message->cgid] = $message->cgid;
        }
        if(empty($idList)) return array();

        $this->dao->update(TABLE_IM_CHAT)->set('lastActiveTime')->eq(helper::now())->where('gid')->in($chatList)->exec();

        return $this->getMessageList($idList);
    }

    /**
     * Save offline messages. 
     * 
     * @param  array  $messages
     * @param  array  $users
     * @access public
     * @return bool 
     */
    public function saveOfflineMessages($messages = array(), $users = array())
    {
        foreach($users as $user)
        {
            $data = new stdclass();
            $data->user    = $user;
            $data->message = helper::jsonEncode($messages);
            $this->dao->insert(TABLE_IM_USERMESSAGE)->data($data)->exec();
        }
        return !dao::isError();
    }

    /**
     * Upgrade xuanxuan. 
     * 
     * @access public
     * @return void
     */
    public function upgrade()
    {
        $version = $this->getVersion();
        if(version_compare($this->config->xuanxuan->version, $version, '<=')) 
        {
            $output = <<<EOT
<html>
  <head><meta charset='utf-8'></head>
  <body>
    <div style='text-align: center'>
      <h1>{$this->lang->chat->latestVersion}</h1>
    </div>
  </body>
</html>
EOT;
            die($output);
        }

        switch($version)
        {
        case '1.0': $this->loadModel('upgrade')->execSQL($this->getUpgradeFile($version));
        case '1.1.0':
        default: $this->loadModel('setting')->setItem('system.sys.xuanxuan.global.version', $this->config->xuanxuan->version);
        }

        if(dao::isError())
        {
            $error  = dao::getError(true);
            $output = <<<EOT
<html>
  <head><meta charset='utf-8'></head>
  <body>
    <div style='text-align: center'>
      <h1>{$this->lang->chat->upgradeFail}</h1>
      <p>{$error}</p>
    </div>
  </body>
</html>
EOT;
        }
        else
        {
            $output = <<<EOT
<html>
  <head><meta charset='utf-8'></head>
  <body>
    <div style='text-align: center'>
      <h1>{$this->lang->chat->upgradeSuccess}</h1>
    </div>
  </body>
</html>
EOT;
        }
        die($output);
    }

    /**
     * Get version of xuanxuan.
     * 
     * @access public
     * @return string
     */
    public function getVersion()
    {
        $version = !empty($this->config->xuanxuan->global->version) ? $this->config->xuanxuan->global->version : '1.0';
        return $version;
    }

    /**
     * Get upgrade file. 
     * 
     * @param  string $version 
     * @access public
     * @return string
     */
    public function getUpgradeFile($version = '1.0')
    {
        return $this->app->getBasepath() . 'db' . DS . 'upgradexuanxuan' . $version . '.sql';
    }
}
