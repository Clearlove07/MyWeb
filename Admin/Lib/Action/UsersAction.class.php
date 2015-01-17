<?php
/***********************************************************
    [EasyTalk] (C)2009 - 2011 nextsns.com
    This is NOT a freeware, use is subject to license terms

    @Filename UsersAction.class.php $

    @Author hjoeson $

    @Date 2011-01-09 08:45:20 $
*************************************************************/

class UsersAction extends IniAction {
    public $vipgroup;

    public function _initialize() {
		parent::init();
        parent::tologin();
		$this->vipgroup=M('Vipgroup')->select();
    }

    public function index() {
        if ($this->vipgroup) {
            foreach($this->vipgroup as $val){
                $sgroup.='<option value="'.$val['id'].'">'.$val['name'].'</option>';
            }
        }
        $this->assign('sgroup',$sgroup);
        $this->assign('position','用户管理 -> 搜索用户');
        $this->display();
    }

    public function regadmin() {
        $siteall=M('System')->select();
        foreach($siteall as $val) {
            $site[$val['name']]=$val;
        }
        $this->assign('site',$site);
        $this->assign('position','用户管理 -> 注册管理');
        $this->display();
    }

    public function edit() {
        $user_name=$_GET['user_name'];

        $user=M('Users')->where("user_name='$user_name'")->find();
        if (!$user) {
            msgreturn('很抱歉，没有找到您要编辑的用户',SITE_URL.'/admin.php?s=/Users');
        }

        if ($user['user_auth']==0) {
            $vgroup='<option value="0" selected>普通用户</option>';
        } else {
            $vgroup='<option value="0">普通用户</option>';
        }
        if ($this->vipgroup) {
            foreach($this->vipgroup as $val){
                if ($user['user_auth']==$val['id']) {
                    $vgroup.='<option value="'.$val['id'].'" selected>'.$val['name'].'</option>';
                } else {
                    $vgroup.='<option value="'.$val['id'].'">'.$val['name'].'</option>';
                }
            }
        }

        $this->assign('vgroup',$vgroup);
        $this->assign('user',$user);
        $this->assign('position','用户管理 -> 用户编辑');
        $this->display('index');
    }

    public function search() {
        $user_name=$_REQUEST['user_name'];
        $group=$_REQUEST['group'];
        import("@.ORG.Page");
        C('PAGE_NUMBERS',10);
        $umodel=M('Users');

        if ($group) {
            if ($user_name) {
                $where="user_name LIKE '%$user_name%' AND ";
            } else {
                $where="";
            }
            if ($group=='all') {
                $where.="1";
            } else if ($group=='admin1') {
                $where.="isadmin=1";
            } else if ($group=='admin2') {
                $where.="isadmin=2";
            } else if ($group=='public') {
                $where.="isadmin=0 AND userlock=0";
            } else if ($group=='lock') {
                $where.="userlock=1";
            } else if ($group=='close') {
                $where.="userlock=2";
            } else if (is_numeric($group) && $group>0) {
                $where.="user_auth='$group'";
            }
            $count=$umodel->where($where)->count();
            $p= new Page($count,20);
            $page = $p->show("admin.php?s=/Users/search/user_name/$user_name/group/$group/p/");

            $user=$umodel->where($where)->limit($p->firstRow.','.$p->listRows)->select();

            if ($this->vipgroup) {
                foreach($this->vipgroup as $val){
                    $sgroup2[$val['id']]=$val['name'];
                }
            }
        } else {
            header('location:'.SITE_URL.'/admin.php?s=/Users');
            exit;
        }
        $this->assign('position','用户管理 -> 搜索用户');
        $this->assign('user',$user);
        $this->assign('sgroup2',$sgroup2);
        $this->assign('page',$page);
        $this->assign('count',$count);
        $this->display('index');
    }

    public function admin() {
        $user=M('Users')->where("isadmin>0")->select();
        $this->assign('position','用户管理 -> 管理员管理');
        $this->assign('user',$user);
        $this->display();
    }

    public function adminedit() {
        $user_name=$_POST['user_name'];
        $isadmin=$_POST['isadmin'];

        M('Users')->where("user_id!=1 AND user_name='$user_name'")->setField('isadmin',$isadmin);
        msgreturn('管理员编辑成功',SITE_URL.'/admin.php?s=/Users/admin');
    }

    public function edituser() {
        $user_name=$_POST['user_name'];
        $userdata=$_POST['user'];
        $regmailauth=intval($_POST['regmailauth']);
        $delmsg=$_POST['delmsg'];

        $uModel=M('Users');
        if ($user_name) {
            $user=$uModel->where("user_name='$user_name'")->find();
            if ($user) {
                if ($userdata['nickname']!=$user['nickname']) {
                    $newdt=$uModel->where("nickname='$userdata[nickname]'")->find();
                    if ($newdt) {
                        msgreturn('很抱歉，您修改的新昵称，已经存在！',SITE_URL.'/admin.php?s=/Users/index/user_name/'.$user_name);
                    }
                }
                $keys=array();
                foreach($userdata as $key=>$val) {
                    if ($key=='password')  {
                        if ($val) {
                            $keys['password']=md5(md5(trim($val)));
							//ucenter 修改密码
							if (ET_UC==TRUE) {
								$ucresult = uc_user_edit($user_name,'',trim($val),'',1);
								if ($ucresult<0) {
									msgreturn('密码修改失败！'.$ucresult,SITE_URL.'/admin.php?s=/Users/edit/user_name/'.$user_name);
									exit;
								}
							}
                        }
                    } else {
                        $keys[$key]=$val;
                    }
                }
                //广播数清零
                if ($delmsg==1) {
                    $keys['msg_num']=0;
                }
                //邮件认证
                if ($regmailauth==1) {
                    $keys['regmailauth']=1;
                } else {
                    $keys['regmailauth']=0;
                }
                $uModel->where("user_name='$user_name'")->setField($keys);
                //删除用户数据
                if ($delmsg==1) {
                    $ct=M('Content');
                    $ctp=M('Content_topic');
					$tModel=M('Topic');
                    $data=$ct->where("user_id='$user[user_id]'")->select();
                    if (is_array($data)) {
                        foreach ($data as $val1) {
                            //删除话题
                            $data2=$ctp->where("content_id='$val1[content_id]'")->select();
                            if (is_array($data2)) {
                                foreach ($data2 as $val) {
                                    $tModel->where("id='$val[topic_id]'")->setDec('topictimes',1);
                                }
                            }
                            $ctp->where("content_id='$val1[content_id]'")->delete();
                            $ct->where("content_id='$val1[content_id]'")->delete();
                            M('Content_mention')->where("cid='$val1[content_id]'")->delete();
                        }
                    }
                }
                msgreturn('用户信息编辑成功',SITE_URL.'/admin.php?s=/Users/edit/user_name/'.$user_name);
            } else {
                msgreturn('很抱歉，没有找到您要编辑的用户',SITE_URL.'/admin.php?s=/Users');
            }
        }
    }
}
?>