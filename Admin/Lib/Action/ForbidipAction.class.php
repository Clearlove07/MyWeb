<?php
/***********************************************************
    [EasyTalk] (C)2009 - 2011 nextsns.com
    This is NOT a freeware, use is subject to license terms

    @Filename ForbidipAction.class.php $

    @Author hjoeson $

    @Date 2011-10-20 08:45:20 $
*************************************************************/

class ForbidipAction extends IniAction{

    public function _initialize() {
		parent::init();
        parent::tologin();
    }

    public function index() {
        $data=M('Forbidip')->select();
        $this->assign('position','用户管理 -> 禁止IP');
        $this->assign('data',$data);
        $this->display();
    }

    public function save() {
        $fModel=M('Forbidip');
        $delid=$_POST['delid'];
        $ips=$_POST['ips'];
        $times=$_POST['times'];
        $n_ip=$_POST['n_ip'];
        $n_time=$_POST['n_time'];

        $ipdata = $fModel->select();
        //修改
        if ($ipdata) {
            foreach ($ipdata as $val) {
                if (in_array($val['ip'],$delid)) {
                    $fModel->where("ip='$val[ip]'")->delete();
                } else {
                    if ($val['ip']!=$ips[$val['ip']] || $val['lasttime']!=$times[$val['ip']]) {
                        if ($times[$val['ip']]==0) {
                            $fModel->where("ip='$val[ip]'")->setField(array('ip'=>$ips[$val['ip']],'lasttime'=>0));
                        } else {
                            $fModel->where("ip='$val[ip]'")->setField(array('ip'=>$ips[$val['ip']],'lasttime'=>strtotime($times[$val['ip']])));
                        }
                    }
                }
            }
        }
        //新增
        if ($n_ip) {
            foreach ($n_ip as $key=>$val) {
                if ($val!='0.0.0.0') {
                    $insert['ip']=$val;
                    if ($n_time[$key]==0) {
                        $insert['lasttime']=0;
                    } else {
                        $insert['lasttime']=strtotime($n_time[$key]);
                    }
                    $fModel->add($insert);
                }
            }
        }
        //缓存
        $ipdata = $fModel->select();
        A('Setting')->deleteDir('./Home/Runtime/Data/forbidip.php');
        F('forbidip',$ipdata,'./Home/Runtime/Data/');
        msgreturn('IP信息保存成功！',SITE_URL.'/admin.php?s=/Forbidip');
    }
}
?>