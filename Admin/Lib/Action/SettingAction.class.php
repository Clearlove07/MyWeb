<?php
/***********************************************************
    [EasyTalk] (C)2009 - 2011 nextsns.com
    This is NOT a freeware, use is subject to license terms

    @Filename SettingAction.class.php $

    @Author hjoeson $

    @Date 2011-01-09 08:45:20 $
*************************************************************/

class SettingAction extends IniAction{

    public function _initialize() {
		parent::init();
        parent::tologin();
		$siteall=M('System')->select();
        foreach($siteall as $val) {
            $site[$val['name']]=$val;
        }
        $this->assign('site',$site);
    }

    public function index() {
        $this->assign('position','全局设置 -> 网站设置');
        $this->display();
    }

    public function mail() {
        $this->assign('position','全局设置 -> 邮件设置');
        $this->display();
    }

    public function ads() {
        $this->assign('position','其他设置 -> 通用广告管理');
        $this->display();
    }

    public function about() {
        $this->assign('position','其他设置 -> 关于我们设置');
        $this->display();
    }

    public function shorturl() {
        $this->assign('position','全局设置 -> 短域名设置');
        $this->display();
    }

    public function switchs() {
        $this->assign('position','全局设置 -> 网站开关');
        $this->display();
    }

    private function resetarray($arr) {
        $arr2=array();
        if (is_array($arr)) {
            foreach($arr as $val) {
                $arr2[]=$val;
            }
        }
        return $arr2;
    }

    private function keyfu($arr) {
        if (is_array($arr)) {
            $min=0;
            foreach($arr as $val) {
                $min=min($val,$min);
            }
            if ($min<0) {
                $add=abs($min);
                foreach($arr as $val) {
                    $arr2[]=intval($val)+$add;
                }
                return $arr2;
            }
        }
        return $arr;
    }

    private function keysort($arr,$key) {
        if ($arr[$key]) {
            $key+=1;
            return $this->keysort($arr,$key);
        } else {
            return $key;
        }
    }

	public function uplogo() {
		$logo=$_FILES['upload'];
		if (strtolower($logo['type'])!='image/png') {
			msgreturn('格式错误！图片格式必须是png',SITE_URL.'/admin.php?s=/Setting');
		}
		$info=getimagesize($logo['tmp_name']);
		if ($info[0]!=155 || $info[1]!=30) {
			msgreturn('图片尺寸不正确，应为 155 x 30',SITE_URL.'/admin.php?s=/Setting');
		}
		if (is_uploaded_file($logo['tmp_name']) && $logo["error"]==0) {
            $zipfile=ET_ROOT.'/Public/images/logo.png';
            move_uploaded_file($logo['tmp_name'],$zipfile);
            chmod($zipfile,0777);
        }
		msgreturn('恭喜您，logo上传成功',SITE_URL.'/admin.php?s=/Setting');
	}

    public function webset() {
        $system=M('System');
        $sitedata=$_POST['site'];
        $reurl=$_POST['reurl'];
        //微博插件数据
        if ($sitedata['weibodata']) {
            foreach($sitedata['weibodata'] as $key=>$val) {
                $weibodata[$key]=clean_html($val);
            }
            $sitedata['weibodata']=json_encode($weibodata);
        }
		if ($sitedata['weiboopen']) {
            foreach($sitedata['weiboopen'] as $key=>$val) {
                $weiboopen[$key]=$val;
            }
            $sitedata['weiboopen']=json_encode($weiboopen);
        }
        if ($sitedata) {
			$_namelist=$system->select();
			foreach ($_namelist as $val) {
				$namelist[$val['name']]=1;
			}
            $allowhtml=array('ad1','ad2','ad3','foottongji','about','contect','join','weibodata','weiboopen');
            foreach($sitedata as $key=>$val) {
                if (!in_array($key,$allowhtml)) {
                    $val=clean_html($val);
                }
				if (!$namelist[$key]) {
					$system->add(array('name'=>$key,'title'=>$key,'contents'=>$val));
				}
                $system->where("name='$key'")->setField('contents',$val);
            }
        }
        //clearcache
        $this->deleteDir('./Home/Runtime/Data/site.php');
        //重新写入site
        $site=array();
        $data = M('system')->select();
        foreach ($data as $key=>$val) {
            $site[$val['name']]=$val['contents'];
        }
        F('site',$site,'./Home/Runtime/Data/');
        msgreturn('恭喜您，设置成功了',SITE_URL.'/admin.php?s=/'.$reurl);
    }

    public function deleteDir($dirName){
        if(!is_dir($dirName)){
            @unlink($dirName);
            return false;
        }
        $handle = @opendir($dirName);
        while(($file = @readdir($handle)) !== false){
            if($file != '.' && $file != '..'){
                $dir = $dirName . '/' . $file;
                is_dir($dir) ? $this->deleteDir($dir) : @unlink($dir);
            }
        }
        closedir($handle);
        return rmdir($dirName);
    }

	public function ucenter() {
		$file = file_get_contents('api/uc_client/config.inc.php');
		$file=str_replace(array("if (!defined('IN_ET')) exit();",'<?php','?>','	'),'',$file);
		$file=trim($file);

		$this->assign('file',$file);
		$this->assign('position','插件管理 -> Ucenter整合');
        $this->display();
	}

	public function doucenter() {
		$config=stripcslashes($_POST['ucenterconfig']);
		$openucenter=intval($_POST['openucenter']);
		
		$defined = "<?php
		define('ADMIN_UID', ".ADMIN_UID.");
		define('ET_VESION', '".ET_VESION."');
		define('ET_RELEASE', '".ET_RELEASE."');
		define('ET_UC', $openucenter);
		define('SITE_URL','".SITE_URL."');
		?>";

		if($fp = fopen('define.inc.php', 'w')) {
			fwrite($fp, $defined);
			fclose($fp);
		}

		$config = "<?php
		if (!defined('IN_ET')) exit();
		$config
		?>";

		if($fp = fopen('api/uc_client/config.inc.php', 'w')) {
			fwrite($fp, $config);
			fclose($fp);
		}

		msgreturn('恭喜您，Ucenter设置保存成功了！',SITE_URL.'/admin.php?s=/Setting/ucenter');
	}
}
?>