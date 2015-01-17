<?php
class IniAction extends Action{
	public $admin     =  '';
    public $loginname     =  '';

    public function init() {
		import('@.ORG.Cookie');
        $adminauth = Cookie::get('adminauth');
        list($unadmin,$uidadmin) = explode("\t", authcode($adminauth,'DECODE'));
		$user = M("Users")->where("user_name='$unadmin' AND user_id='$uidadmin' AND isadmin=1")->find();
        if ($user) {
			$this->loginname=$unadmin;
            Cookie::set('adminauth', authcode("$unadmin\t$uidadmin",'ENCODE'));
            $this->admin = $user;
        } else {
            $this->admin='';
        }

        //vip分组
        $vipgroup=@include(ET_ROOT.'/Home/Runtime/Data/vipgroup.php');
        if (!$vipgroup) {
            $vipgroup = M('Vipgroup')->select();
            F('vipgroup',$vipgroup,'./Home/Runtime/Data/');
        }

        $this->assign('loginname',$user_name);
        $this->assign('admin',$this->admin);
    }


	public function toadmin() {
        if ($this->admin['user_id'] && $this->admin['isadmin']==1) {
            echo '<script>parent.location.href="'.SITE_URL.'/admin.php?s=/Index"</script>';
			exit;
        }
    }

	public function tologin() {
		if (!$this->admin['user_id'] || $this->admin['isadmin']!=1) {
            $this->redirect('/Login/index');
			exit;
        }
	}
}
?>