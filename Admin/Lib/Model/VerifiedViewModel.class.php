<?php
/***********************************************************
    [EasyTalk] (C)2009 - 2011 nextsns.com
    This is NOT a freeware, use is subject to license terms

    @Filename VerifiedViewModel.class.php $

    @Author hjoeson $

    @Date 2011-01-09 08:45:20 $
*************************************************************/

class VerifiedViewModel extends ViewModel {
    public $viewFields = array(
        'Verified'=>array('id','user_id','authid','authinfo','phonenum','address','qq','_type'=>'LEFT'),
        'Users'=>array('user_id','user_name','nickname','user_head','user_auth','provinceid','cityid','msg_num','user_gender','follow_num','followme_num','lastcontent','lastconttime','_on'=>'Verified.user_id=Users.user_id'),
    );
}
?>