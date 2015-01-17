<?php
/***********************************************************
    [EasyTalk] (C)2009 - 2011 nextsns.com
    This is NOT a freeware, use is subject to license terms

    @Filename CacheAction.class.php $

    @Author hjoeson $

    @Date 2011-01-09 08:45:20 $
*************************************************************/

class CacheAction extends IniAction{

    public function _initialize() {
		parent::init();
        parent::tologin();
    }

    public function index() {
        $siteall=M('System')->select();
        foreach($siteall as $val) {
            $site[$val['name']]=$val;
        }
        $this->assign('site',$site);
        $this->assign('position','其他设置 -> 缓存管理');
        $this->display();
    }

    public function clearcache() {
        $clearcache=$_POST['clearcache'];
		$saction=A('Setting');
		$sModel=M('system');
        foreach ($clearcache as $val) {
            if ($val=='setting') {
                $path='./Home/Runtime/Data';
                $saction->deleteDir($path);
                mkdir($path);
                chmod($path,0777);
                //重新写入site
                $site=array();
                $data = $sModel->select();
                foreach ($data as $key=>$val) {
                    $site[$val['name']]=$val['contents'];
                }
                F('site',$site,'./Home/Runtime/Data/');
                //admin cache
                $path='./Admin/Runtime/Data';
                $saction->deleteDir($path);
                mkdir($path);
                chmod($path,0777);
            }
            if ($val=='dltheme') {
                $path='./Public/attachments/downtheme';
                $saction->deleteDir($path);
                mkdir($path);
                chmod($path,0777);
            }
            if ($val=='webcache') {
                $path='./Home/Runtime/Temp';
                $saction->deleteDir($path);
                mkdir($path);
                chmod($path,0777);
            }
            if ($val=='tpcache') {
                //home
                $path='./Home/Runtime/Cache';
                $saction->deleteDir($path);
                mkdir($path);
                chmod($path,0777);
                //admin
                $path='./Admin/Runtime/Cache';
                $saction->deleteDir($path);
                mkdir($path);
                chmod($path,0777);
            }
            $sModel->where("name='cachetime'")->setField('contents',time());
            $saction->deleteDir('./Home/Runtime/~app.php');
            $saction->deleteDir('./Home/Runtime/~easytalk_runtime.php');
        }
		$this->makeCache();
        msgreturn('缓存清理成功',SITE_URL.'/admin.php?s=/Cache');
    }

	public function makeCache() {
		//site
        $site=array();
        $data = M('system')->select();
        foreach ($data as $key=>$val) {
            $site[$val['name']]=$val['contents'];
        }
        F('site',$site,'./Home/Runtime/Data/');
		//badword
		$words = M('Words')->select();
        F('badword',$words,'./Home/Runtime/Data/');
		//emotion
		$emo=M("Emotions")->order('`id` DESC')->select();
		F('emotion',$emo,'./Home/Runtime/Data/');
		//vipgroup
		$vipgroup = M('Vipgroup')->select();
        F('vipgroup',$vipgroup,'./Home/Runtime/Data/');
	}
}
?>