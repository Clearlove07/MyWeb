<?php
/***********************************************************
    [EasyTalk] (C)2009 - 2011 nextsns.com
    This is NOT a freeware, use is subject to license terms

    @Filename LoginAction.class.php $

    @Author hjoeson $

    @Date 2011-01-09 08:45:20 $
*************************************************************/

class LoginAction extends IniAction{
	
    public function _initialize() {
		parent::init();
    }

    public function dologin() {
		parent::toadmin();
        $user_name=$_POST['username'];
        $password=$_POST['password'];
		$authcode=trim($_POST['authcode']);

		if (!$user_name || !$password || !$authcode || $authcode!=$_SESSION['authcode']) {
			$this->redirect('/Login/index');
			exit;
		}

        if (ET_UC==TRUE) {
            list($uid, $username, $password, $email) = uc_user_login($user_name,$password);
            if($username && $uid>0) {
                $user = M("Users")->where("user_name='$username' AND isadmin=1")->field('user_id,user_name')->find();
                if($user) {
                    Cookie::set('adminauth', authcode("$user_name\t$user[user_id]",'ENCODE'));
                    echo '<script>parent.location.href="'.SITE_URL.'/admin.php?s=/Index"</script>';
                } else {
                    $this->redirect('/Login/index');
                }
            } else {
                $this->redirect('/Login/index');
            }
        } else {
            $password=md5(md5($password));
			$user = M("Users")->where("user_name='$user_name' AND password='$password' AND isadmin=1")->find();
			if($user) {
				Cookie::set('adminauth', authcode("$user_name\t$user[user_id]",'ENCODE'));
				echo '<script>parent.location.href="'.SITE_URL.'/admin.php?s=/Index"</script>';
			} else {
				$this->redirect('/Login/index');
			}
        }
    }

    public function logout() {
        setcookie('adminauth','',-1,'/');
        Cookie::delete('adminauth');
        $this->redirect('/Login/index');
    }
}
?>