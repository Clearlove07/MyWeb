<?php
/***********************************************************
    [EasyTalk] (C)2009 - 2011 nextsns.com
    This is NOT a freeware, use is subject to license terms

    @Filename IndexAction.class.php $

    @Author hjoeson $

    @Date 2011-01-09 08:45:20 $
*************************************************************/

class IndexAction extends IniAction{

    public function _initialize() {
		parent::init();
        parent::tologin();
    }

    public function setindex() {
        $model=new Model();
        $pre=C('DB_PREFIX');
        $serverinfo = PHP_OS;
        $serverinfo .= @ini_get('safe_mode') ? ' Safe Mode' : NULL;
        $dbversion = $model->query("SELECT VERSION()");
        $dbversion = $dbversion['0']['VERSION()'];
        $dbsize = 0;
        $query = $model->query("SHOW TABLE STATUS LIKE '$pre%'", 'SILENT');
        foreach($query as $val) {
            $dbsize += $val['Data_length'] + $val['Index_length'];
        }
        $dbsize = $dbsize ? sizecount($dbsize) : "未知大小";
        $tongji=M('Tongji')->where("dateline='".date('Ymd')."' || dateline='".date('Ymd',strtotime("-1 day"))."'")->select();
        foreach($tongji as $val) {
            if ($val['dateline']==date('Ymd')) {
                if ($val['type']=='login') {
                    $tjdata['tdlogin']=$val['nums'];
                } else if ($val['type']=='register') {
                    $tjdata['tdreg']=$val['nums'];
                }
            } else {
                if ($val['type']=='login') {
                    $tjdata['ytdlogin']=$val['nums'];
                } else if ($val['type']=='register') {
                    $tjdata['ytdreg']=$val['nums'];
                }
            }
        }
        $tjdata['ppcount']=M('Users')->count();
        $this->assign('tjdata',$tjdata);
        $this->assign('serverinfo',$serverinfo);
        $this->assign('dbversion',$dbversion);
        $this->assign('dbsize',$dbsize);
        $this->assign('position','首页');
        $this->display();
    }
}
?>