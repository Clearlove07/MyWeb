<?php
/***********************************************************
    [EasyTalk] (C)2009 - 2011 nextsns.com
    This is NOT a freeware, use is subject to license terms

    @Filename Function.php $

    @Author hjoeson $

    @Date 2011-01-09 08:45:20 $
*************************************************************/

function sizecount($filesize) {
	if($filesize >= 1073741824) {
		$filesize = round($filesize / 1073741824 * 100) / 100 . ' GB';
	} elseif($filesize >= 1048576) {
		$filesize = round($filesize / 1048576 * 100) / 100 . ' MB';
	} elseif($filesize >= 1024) {
		$filesize = round($filesize / 1024 * 100) / 100 . ' KB';
	} else {
		$filesize = $filesize . ' Bytes';
	}
	return $filesize;
}

function jbtype($id) {
    if ($id==1) {
        return '涉及黄色和暴力';
    } else if ($id==2) {
        return '政治反动';
    } else if ($id==3) {
        return '内容侵权';
    } else if ($id==4) {
        return '其他不良信息';
    }
}

function get_plugin_data( $plugin_file ) {
    $plugin_data = implode( '', file( $plugin_file ));

    preg_match( '|Plugin Name:(.*)$|mi', $plugin_data, $plugin_name );
    preg_match( '|Plugin URI:(.*)$|mi', $plugin_data, $plugin_uri );
    preg_match( '|Description:(.*)$|mi', $plugin_data, $description );
    preg_match( '|Author:(.*)$|mi', $plugin_data, $author_name );
    preg_match( '|Author URI:(.*)$|mi', $plugin_data, $author_uri );

    if ( preg_match( "|Version:(.*)|i", $plugin_data, $version )) {
        $version = trim( $version[1] );
    } else {
        $version = '';
    }
    $description = clean_html( trim( $description[1] ));

    $name = $plugin_name[1];
    $name = trim( $name );
    $plugin = $name;
    if ('' != trim($plugin_uri[1]) && '' != $name ) {
        $plugin = '<a href="' . trim( $plugin_uri[1] ) . '" title="'.__( 'Visit plugin homepage' ).'">'.$plugin.'</a>';
    }

    if ('' == $author_uri[1] ) {
        $author = trim( $author_name[1] );
    } else {
        $author = '<a href="' . trim( $author_uri[1] ) . '" title="'.__( 'Visit author homepage' ).'">' . trim( $author_name[1] ) . '</a>';
    }
    return array('Name' => $name, 'Title' => $plugin, 'Description' => $description, 'Author' => $author, 'Version' => $version);
}

function msgreturn($title,$url) {
    echo '
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
    <html xmlns="http://www.w3.org/1999/xhtml"><head>
    <title>Return</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="stylesheet" href="'.SITE_URL.'/Public/admin/style.css" type="text/css" media="all" />
    </head>
    <body>
    <div id="bodymain">
    <div class="title">系统跳转</div>
    <div class="content">
    <script type="text/javascript">setInterval(function(){window.location.href=\''.$url.'\';}, 1000);</script>
    <div class="return">
        <div class="tt">'.$title.'</div>
        <a href="'.$url.'">如果您的浏览器没有自动跳转，请点击这里</a>
    </div>
    </div>
    </div>
    </body>
    </html>';
    exit;
}
function versionCompare($pluginversion,$etversion) {
	$pluginversion = str_replace(array('x','.'),'',strtolower($pluginversion));
	$etversion = str_replace(array('x','.'),'',strtolower($etversion));
	if ($pluginversion<100) {
		$pluginversion = $pluginversion*10;
	}
	if ($etversion<100) {
		$etversion = $etversion*10;
	}
	if ($pluginversion<=$etversion) {
		return true;
	} else {
		return false;
	}
}
?>