<?php
/***********************************************************
    [EasyTalk] (C)2009 - 2011 nextsns.com
    This is NOT a freeware, use is subject to license terms

    @Filename WordAction.class.php $

    @Author hjoeson $

    @Date 2011-01-09 08:45:20 $
*************************************************************/

class WordAction extends IniAction{

    public function _initialize() {
		parent::init();
        parent::tologin();
    }

    public function index() {
        $word=M('Words')->select();
        $this->assign('position','全局设置 -> 词语过滤');
        $this->assign('word',$word);
        $this->display();
    }

    public function save() {
        $wModel=M('Words');
        $delid=$_POST['delid'];
        $wordname=$_POST['wordname'];
        $optype=$_POST['optype'];
        $n_wordname=$_POST['n_wordname'];
        $n_optype=$_POST['n_optype'];

        $words = $wModel->select();
        //修改
        if ($words) {
            foreach ($words as $val) {
                if (in_array($val['id'],$delid)) {
                    $wModel->where("id='$val[id]'")->delete();
                } else {
                    if ($val['word']!=$wordname[$val['id']] || $val['type']!=$optype[$val['id']]) {
                        $wModel->where("id='$val[id]'")->setField(array('word'=>$wordname[$val['id']],'type'=>$optype[$val['id']]));
                    }
                }
            }
        }
        //新增
        if ($n_wordname) {
            foreach ($n_wordname as $key=>$val) {
                if ($val) {
                    $insert['word']=$val;
                    $insert['type']=$n_optype[$key];
                    $wModel->add($insert);
                }
            }
        }
        //缓存
        $words = $wModel->select();
        A('Setting')->deleteDir('./Home/Runtime/Data/badword.php');
        F('badword',$words,'./Home/Runtime/Data/');
        msgreturn('词语信息保存成功！',SITE_URL.'/admin.php?s=/Word');
    }
}
?>