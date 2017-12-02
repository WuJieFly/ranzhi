<?php
/**
 * The model file of backup module of RanZhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     backup
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
class backupModel extends model
{
    /**
     * Backup SQL 
     * 
     * @param  string    $backupFile 
     * @access public
     * @return object
     */
    public function backSQL($backupFile)
    {
        $zdb = $this->app->loadClass('zdb');
        return $zdb->dump($backupFile);
    }

    /**
     * Backup file.
     * 
     * @param  string    $backupFile 
     * @access public
     * @return object
     */
    public function backFile($backupFile)
    {
        $return = new stdclass();
        $return->result = true;
        $return->error  = '';

        $this->app->loadClass('pclzip', true);
        $zip = new pclzip($backupFile);
        $zip->create($this->app->getBasePath() . 'www/data/', PCLZIP_OPT_REMOVE_PATH, $this->app->getBasePath() . 'www/data/');
        if($zip->error_code != 0)
        {
            $return->result = false;
            $return->error  = $zip->errorInfo();
        }

        return $return;
    }

    /**
     * Restore SQL 
     * 
     * @param  string    $backupFile 
     * @access public
     * @return object
     */
    public function restoreSQL($backupFile)
    {
        $zdb = $this->app->loadClass('zdb');
        return $zdb->import($backupFile);
    }

    /**
     * Restore File 
     * 
     * @param  string    $backupFile 
     * @access public
     * @return object
     */
    public function restoreFile($backupFile)
    {
        $return = new stdclass();
        $return->result = true;
        $return->error  = '';

        $this->app->loadClass('pclzip', true);
        $zip = new pclzip($backupFile);
        if($zip->extract(PCLZIP_OPT_PATH, $this->app->getBasePath() . 'www/data/') == 0)
        {
            $return->result = false;
            $return->error  = $zip->errorInfo();
        }

        return $return;
    }

    /**
     * Set save days for backup
     * 
     * @access public
     * @return bool
     */
    public function setSaveDays()
    {
        $this->loadModel('setting')->setItem('system.sys.common.backup.saveDays', $this->post->saveDays);

        if(!dao::isError())
        {
            $cron = $this->dao->select('*')->from(TABLE_CRON)->where('command')->like('appName=sys&moduleName=backup&methodName=batchdelete%')->fetch();
            if($cron)
            {
                $this->dao->update(TABLE_CRON)
                    ->set('command')->eq('appName=sys&moduleName=backup&methodName=batchdelete&saveDays=' . $this->post->saveDays)
                    ->set('remark')->eq(sprintf($this->lang->backup->deleteInfo, $this->post->saveDays))
                    ->where('id')->eq($cron->id)
                    ->exec();
            }
            else
            {
                $cron = new stdclass();
                $cron->m       = '1';
                $cron->h       = '1';
                $cron->dom     = '*';
                $cron->mon     = '*';
                $cron->dow     = '*';
                $cron->command = 'appName=sys&moduleName=backup&methodName=batchdelete&saveDays=' . $this->post->saveDays;
                $cron->remark  = sprintf($this->lang->backup->deleteInfo, $this->post->saveDays);
                $cron->type    = 'ranzhi';
                $cron->status  = 'normal';
                $this->dao->insert(TABLE_CRON)->data($cron)->autoCheck()->exec();
            }
        }
        return !dao::isError();
    }

    /**
     * Add file header.
     * 
     * @param  string    $fileName 
     * @access public
     * @return bool
     */
    public function addFileHeader($fileName)
    {
        $firstline = false;
        $die       = "<?php die();?>\n";
        $fileSize  = filesize($fileName);

        $fh    = fopen($fileName, 'c+');
        $delta = strlen($die);
        while(true)
        {
            $offset = ftell($fh);
            $line   = fread($fh, 1024 * 1024);
            if(!$firstline)
            {
                $line = $die . $line;
                $firstline = true;
            }
            else
            {
                $line = $compensate . $line;
            }
            
            $compensate = fread($fh, $delta);
            fseek($fh, $offset);
            fwrite($fh, $line);

            if(ftell($fh) >= $fileSize)
            {
                fwrite($fh, $compensate);
                break;
            }
        }
        fclose($fh);
        return true;
    }

    /**
     * Remove file header.
     * 
     * @param  string    $fileName 
     * @access public
     * @return bool
     */
    public function removeFileHeader($fileName)
    {
        $firstline = false;
        $die       = "<?php die();?>\n";
        $fileSize  = filesize($fileName);

        $fh = fopen($fileName, 'c+');
        while(true)
        {
            $offset = ftell($fh);
            if($firstline and $delta) fseek($fh, $offset + $delta);
            $line = fread($fh, 1024 * 1024);
            if(!$firstline)
            {
                $firstline    = true;
                $beforeLength = strlen($line);
                $line         = str_replace($die, '', $line);
                $afterLength  = strlen($line);
                $delta        = $beforeLength - $afterLength;
                if($delta == 0)
                {
                    fclose($fh);
                    return true;
                }
            }
            fseek($fh, $offset);
            fwrite($fh, $line);

            if(ftell($fh) >= $fileSize - $delta) break;
        }
        ftruncate($fh, ($fileSize - $delta));
        fclose($fh);
        return true;
    }
}
