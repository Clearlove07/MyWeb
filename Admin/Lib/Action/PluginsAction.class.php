<?php
/***********************************************************
    [EasyTalk] (C)2009 - 2011 nextsns.com
    This is NOT a freeware, use is subject to license terms

    @Filename PluginsAction.class.php $

    @Author hjoeson $

    @Date 2011-01-09 08:45:20 $
*************************************************************/

class PluginsAction extends IniAction{

    public function _initialize() {
		parent::init();
        parent::tologin();
    }

    public function index() {
        $plusname=array();
        $data=M('Plugins')->where("type!='api'")->select();
        foreach ($data as $pname) {
            $plusname[]=$pname['directory'];
        }
		if(is_dir(ET_ROOT.'/Apps')) {
			$dir = dir(ET_ROOT.'/Apps');
			while($entry = $dir->read()) {
				if ($entry!='.' && $entry!='..') {
                    if (@file_exists(ET_ROOT .'/Apps/'.$entry.'/info.php')
                        && @file_exists(ET_ROOT .'/Apps/'.$entry.'/index.class.php')
                        && !in_array($entry,$plusname)) {
                        $info=@include(ET_ROOT .'/Apps/'.$entry.'/info.php');
                        $plugins[$entry]=$info;
                    }
                }
			}
			$dir->close();
		}

        $this->assign('position','添加应用插件');
        $this->assign('plugins',$plugins);
        $this->display();
    }

	public function center() {
		$this->assign('position','应用中心');
        $this->display();
	}

    public function admin() {
        $data=M('Plugins')->where("type!='api'")->select();
        foreach ($data as $plus) {
            if (@file_exists(ET_ROOT .'/Apps/'.$plus['directory'].'/info.php')) {
                $info=@include(ET_ROOT .'/Apps/'.$plus['directory'].'/info.php');
            } else {
                $info='';
            }
			if (@file_exists(ET_ROOT .'/Apps/'.$plus['directory'].'/admin.class.php')) {
                $isadmin=1;
            } else {
                $isadmin=0;
            }
            $plugins[$plus['directory']]=array('plus'=>$plus,'info'=>$info,'isadmin'=>$isadmin);
        }

        $this->assign('position','管理应用插件');
        $this->assign('plugins',$plugins);
        $this->display();
    }

    public function switchs() {
        $appname=$_GET['appname'];
        $s=intval($_GET['s']);
        if ($appname) {
            M('Plugins')->where("directory='$appname' AND type!='api'")->setField('available',$s);
            msgreturn('插件操作成功！',SITE_URL.'/admin.php?s=/Plugins/admin');
        } else {
            msgreturn('很抱歉，插件操作失败！',SITE_URL.'/admin.php?s=/Plugins/admin');
        }
    }

    public function install() {
        $appname=$_GET['appname'];
        $plusname=array();
        if ($appname=='weibologin') {
            msgreturn('该应用插件不需要安装，正在进入设置页面！',SITE_URL.'/admin.php?s=/Plugins/weibo');
            exit;
        }
        $data=M('Plugins')->where("type!='api'")->select();
        foreach ($data as $pname) {
            $plusname[]=$pname['directory'];
        }
        if (@file_exists(ET_ROOT .'/Apps/'.$appname.'/info.php')
            && @file_exists(ET_ROOT .'/Apps/'.$appname.'/index.class.php')
            && !in_array($appname,$plusname)) {
            $info=@include(ET_ROOT .'/Apps/'.$appname.'/info.php');
            //setup
            include_once(ET_ROOT .'/Apps/'.$appname.'/index.class.php');
            if (class_exists($appname)) {
                $plus=new $appname($this);
                $setup=$plus->install();
                if ($setup==true) {
                    $insert['name']=$info['name'];
                    $insert['directory']=$appname;
                    $insert['available']=0;
                    $insert['type']=$info['type'];
					if ($info['type']=='app') {
						$insert['applogo']=$info['applogo'];
						$insert['icourl']=$info['icourl'];
					}
					$insert['appinfo']=$info['info'];
					$insert['appauthor']=$info['author'];
                    M('Plugins')->add($insert);
                    msgreturn('【'.$info['name'].'】已经成功安装！',SITE_URL.'/admin.php?s=/Plugins/admin');
                } else {
                    msgreturn('很抱歉，该应用插件安装失败！',SITE_URL.'/admin.php?s=/Plugins');
                }
            } else {
                msgreturn('很抱歉，该应用插件不能被识别！',SITE_URL.'/admin.php?s=/Plugins');
            }
        } else {
            $this->assign('url',SITE_URL.'/admin.php?s=/Plugins');
            if (in_array($appname,$plusname)) {
                msgreturn('该应用插件已经安装了！',SITE_URL.'/admin.php?s=/Plugins');
            } else {
                msgreturn('该应用插件不存在或者不能被识别！',SITE_URL.'/admin.php?s=/Plugins');
            }
        }
    }

    public function uninstall() {
        $appname=$_GET['appname'];
        if (@file_exists(ET_ROOT .'/Apps/'.$appname.'/index.class.php')) {
            include_once(ET_ROOT .'/Apps/'.$appname.'/index.class.php');
            if (class_exists($appname)) {
                $plus=new $appname($this);
                $unsetup=$plus->uninstall();
                if ($unsetup==true) {
					$app=M('Plugins')->where("directory='$appname' AND type!='api'")->find();
					if ($app) {
						M('Plugins')->where("id='$app[id]'")->delete();
						M('Myapps')->where("appid='$app[id]'")->delete();
					}
                    msgreturn('应用插件已经成功卸载！',SITE_URL.'/admin.php?s=/Plugins/admin');
                } else {
                    msgreturn('很抱歉，该应用插件卸载失败！',SITE_URL.'/admin.php?s=/Plugins/admin');
                }
            } else {
                msgreturn('很抱歉，该应用插件未被识别！',SITE_URL.'/admin.php?s=/Plugins/admin');
            }
        } else {
            msgreturn('很抱歉，该应用插件未被识别！',SITE_URL.'/admin.php?s=/Plugins/admin');
        }
    }

    public function appsetting() {
        $appname=$_GET['appname'];
        $plugin=M('Plugins')->where("directory='$appname' AND type!='api'")->find();

        if (@file_exists(ET_ROOT .'/Apps/'.$appname.'/admin.class.php')) {
            include_once(ET_ROOT .'/Apps/'.$appname.'/admin.class.php');
            if (class_exists($appname)) {
                $admin=new $appname($this);
                $appadmin=$admin->index();
            }
        }

        $this->assign('appadmin',$appadmin);
        $this->assign('position',$plugin['name'].'管理');
        $this->assign('plugin',$plugin);
        $this->display();
    }

    public function doadmin() {
        $appname=$_GET['appname'];
        $action=$_GET['action'];

        if (@file_exists(ET_ROOT .'/Apps/'.$appname.'/admin.class.php')) {
            include_once(ET_ROOT .'/Apps/'.$appname.'/admin.class.php');
            if (class_exists($appname)) {
                $admin=new $appname($this);
                $appadmin=$admin->$action();
            }
        }
    }

    public function delapp() {
        $appname=$_GET['appname'];

        if ($appname) {
            A('Setting')->deleteDir(ET_ROOT.'/Apps/'.$appname);
            msgreturn('应用插件文件删除成功！',SITE_URL.'/admin.php?s=/Plugins');
        } else {
            msgreturn('应用插件文件删除失败！',SITE_URL.'/admin.php?s=/Plugins');
        }
    }

    //微博插件
    public function weibo() {
        $siteall=M('System')->select();
        foreach($siteall as $val) {
            if ($val['name']=='weiboopen' || $val['name']=='weibodata') {
                $site[$val['name']]=$val;
            }
        }
        $site['weibodata']=json_decode($site['weibodata']['contents'],true);
		$site['weiboopen']=json_decode($site['weiboopen']['contents'],true);

        $this->assign('site',$site);
        $this->assign('position','应用插件 -> 微博同步与一键登陆');
        $this->display();
    }
}
?>