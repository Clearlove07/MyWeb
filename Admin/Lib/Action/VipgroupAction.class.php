<?php
/***********************************************************
    [EasyTalk] (C)2009 - 2011 nextsns.com
    This is NOT a freeware, use is subject to license terms

    @Filename VipgroupAction.class.php $

    @Author hjoeson $

    @Date 2011-01-09 08:45:20 $
*************************************************************/

class VipgroupAction extends IniAction{

    public function _initialize() {
		parent::init();
        parent::tologin();
    }

    public function index() {
        $data=M('Vipgroup')->select();
        $this->assign('data',$data);
        $this->assign('position','用户管理 -> 用户认证分组管理');
        $this->display();
    }

    public function edit() {
        $vModel=M('Vipgroup');
        $edit=$_POST['edit'];
        $newgroup=$_POST['newgroup'];

        $vipgroup = $vModel->select();

        if ($vipgroup) {//原始的项目
            foreach ($vipgroup as $val) {
                $oripri[]=$val['id'];
            }
        }

        if ($edit) {
            foreach ($edit as $key=>$val) {
                $nowpri[]=$key;
                //修改元素
                if ($val['name'] && $val['iconurl'] && $val['titleurl']) {
                    $vModel->where("`id`='$key'")->setField(array('name'=>$val['name'],'iconurl'=>$val['iconurl'],'titleurl'=>$val['titleurl']));
                }
            }
        }
        //删除元素
        if ($nowpri)  {
            $removename = array_diff($oripri,$nowpri);
        } else {
            $removename = $oripri;
        }

        if ($removename) {
            $removenames=implode(',',$removename);
            $vModel->where("`id` IN ($removenames)")->delete();
        }
        //添加元素
        if ($newgroup) {
            foreach ($newgroup['name'] as $key=>$val) {
                $name=$val;
                $iconurl=$newgroup['iconurl'][$key];
                $titleurl=$newgroup['titleurl'][$key];
                if ($name && $iconurl && $titleurl) {
                    $insert['name']=$name;
                    $insert['iconurl']=$iconurl;
                    $insert['titleurl']=$titleurl;
                    $vModel->add($insert);
                }
            }
        }
        $vipgroup = $vModel->select();
        F('vipgroup',$vipgroup,'./Home/Runtime/Data/');
        msgreturn('认证分组保存成功！',SITE_URL.'/admin.php?s=/Vipgroup');
    }

	public function authadmin() {
		$vModel=D('VerifiedView');
		import("@.ORG.Page");
        C('PAGE_NUMBERS',10);

        $count=$vModel->count();
        $p= new Page($count,20);
        $page = $p->show("admin.php?s=/Vipgroup/authadmin/p/");
        $data = $vModel->order("id DESC")->limit($p->firstRow.','.$p->listRows)->select();
		
		$_groups=M('Vipgroup')->select();
		foreach ($_groups as $val) {
			$groups[$val['id']]=$val['name'];
		}
        $this->assign('groups',$groups);
		$this->assign('data',$data);
        $this->assign('page',$page);
		$this->assign('position','用户管理 -> 用户认证管理');
		$this->display();
	}

	public function doshenhe() {
		$id=$_POST['id'];
		$type=$_POST['type'];
		$reasons=$_POST['reasons'];
		$data=M('Verified')->where("id='$id'")->find();

		if ($id && $type && $reasons && $data) {
			M('Verified')->where("id='$id'")->delete();
			if ($type=='yes') {
				M('Users')->where("user_id='".$data['user_id']."'")->setField(array('user_auth'=>$data['authid'],'auth_info'=>$data['authinfo'],'priread'=>array('exp','priread+1')));
				$insert['senduid']=0;
				$insert['sendtouid']=$data['user_id'];
				$insert['messagebody']='恭喜您，您的认证已经通过！';
				$insert['sendtime']=time();
				M('Messages')->add($insert);
			} else {
				M('Users')->where("user_id='".$data['user_id']."'")->setField(array('user_auth'=>0,'auth_info'=>0));
				$insert['senduid']=0;
				$insert['sendtouid']=$data['user_id'];
				$insert['messagebody']='很抱歉，您的认证被拒绝，原因：'.$reasons;
				$insert['sendtime']=time();
				M('Messages')->add($insert);
			}
			echo 'success';
		} else {
			echo '数据传输错误！';
		}
	}
}
?>