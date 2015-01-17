<?php 
/***********************************************************
    [EasyTalk] (C)2009 - 2011 nextsns.com
    This is NOT a freeware, use is subject to license terms

    @Filename UpdateAction.class.php $

    @Author hjoeson $

    @Date 2011-01-09 08:45:20 $
*************************************************************/

class UpdateAction extends IniAction {
	
	public function _initialize() {
		parent::init();
        parent::tologin();
    }

	public function index() {
        $uuid=$this->getuuid();
        $site = F('site');
		$pars = array(
			'sitename'=>$site['sitename'],
			'siteurl'=>SITE_URL,
			'version'=>ET_VESION,
			'release'=>ET_RELEASE,
			'os'=>PHP_OS,
			'php'=>phpversion(),
			'mysql'=>mysql_get_server_info(),
			'browser'=>urlencode($_SERVER['HTTP_USER_AGENT']),
			'uuid'=>urlencode($uuid),
		);
		$data = http_build_query($pars);
		$verify = md5($uuid);
		$url='http://www.nextsns.com/update.php?'.$data.'&verify='.$verify;
		echo file_get_contents($url);
	}
	
	private function getuuid(){
		$system=M('System')->where("name='uuid'")->find();
		if (!$system['contents']) {
			$mModel=M();
			$data=$mModel->query('SELECT UUID() as uuid');
			$system['contents']=$data[0]['uuid'];
			M('System')->where("`name`='uuid'")->setField('contents',$system['contents']);
		}
		return $system['contents'];
	}
}
?>