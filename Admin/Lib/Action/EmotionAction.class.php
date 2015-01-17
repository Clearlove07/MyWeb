<?php
/***********************************************************
    [EasyTalk] (C)2009 - 2011 nextsns.com
    This is NOT a freeware, use is subject to license terms

    @Filename EmotionAction.class.php $

    @Author hjoeson $

    @Date 2011-04-18 08:45:20 $
*************************************************************/

class EmotionAction extends IniAction {

	public function _initialize() {
		parent::init();
        parent::tologin();
    }

	public function index() {
        $eModel=M('Emotions');
        $content = $eModel->order("`id` DESC")->select();
        $this->assign('content',$content);
        $this->assign('position','全局设置 -> 表情管理');
        $this->display();
    }

	public function save() {
        $eModel=M('Emotions');
        $delid=$_POST['delid'];
        $ident=$_POST['ident'];
        $url=$_POST['url'];
        $n_ident=$_POST['n_ident'];
        $n_url=$_POST['n_url'];

        $emos = $eModel->order("`id` DESC")->select();
        //修改
        if ($emos) {
            foreach ($emos as $val) {
                if (in_array($val['id'],$delid)) {
                    $eModel->where("id='$val[id]'")->delete();
                } else {
                    if ($val['identifier']!=$ident[$val['id']] || $val['url']!=$url[$val['id']]) {
                        $eModel->where("id='$val[id]'")->setField(array('identifier'=>$ident[$val['id']],'url'=>$url[$val['id']]));
                    }
                }
            }
        }
        //新增
        if ($n_ident) {
            foreach ($n_ident as $key=>$val) {
                if ($val) {
                    $insert['identifier']=$val;
                    $insert['url']=$n_url[$key];
                    $eModel->add($insert);
                }
            }
        }
        //缓存
        $emotions = $eModel->order("`id` DESC")->select();
        A('Setting')->deleteDir('./Home/Runtime/Data/emotion.php');
        F('emotion',$emotions,'./Home/Runtime/Data/');
        msgreturn('表情信息保存成功！',SITE_URL.'/admin.php?s=/Emotion');
    }
}
?>