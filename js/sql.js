function saveDump(){
	
}
function parseDump(obj)
{
	var html='<table cellpadding="3" border="1"><tbody><tr>',
		res=new Array();
	toLog('Count(*) is '+obj.count,'green');		
		
	for(k in obj.columns){
		html+='<th>'+obj.columns[k]+'</th>';
	}
	html+='</tr>';
	
	for(k in obj.data)
	{
		res=obj.data[k];
		html+='<tr>';
		
		for(row in res){
			html+='<td>'+res[row]+'</td>';
		}
		html+='</tr>';
	}
	html+='</tbody></table>';
	$('#columns').html(html);
}
function showFile()
{
	var file=$('#fileDir').val(),
		petition={};
	if(file=='' || injection.url==''){return false;}
	changeStatus(1);	
	
	petition.what='file';
	petition.array=[file];
	
	$.post('',{injection:injection,petition:petition},function (data)
	{
		changeStatus(0);
		data=jQuery.parseJSON(data);
		
		if(data.result==''){
			toLog("Could'nt read file, safe host :)",'red');
		}else{
			$('#fileResult').val(data.result);
		}		
	});
}
function findPanel()
{
	var site=$('#adminUrl').val(),
		success=$('#successRes').val();
		
	if(site=='' || success==''){return false;}
	
	$('#adminResults').html('<table cellpadding="3" border="1"><tbody><tr><th>Page</th><th>Response</th></tr></tbody></table>');
	changeStatus(1);
	$.get('',{findpanel:site,success:success},function (data)
	{
		changeStatus(0);
		data=jQuery.parseJSON(data);
		for(k in data){
			$('#adminResults tbody').append('<tr><td><a target="_blank" href="'+data[k]['page']+'">'+data[k]['page']+'</a></td><td>'+data[k]['header']+'</td></tr>');
		}
		
	});
}
function show(info)
{
	var array=new Array(),
		petition={what:info};
	changeStatus(1);
	switch(info)
	{
		case 'tables':				
			$('#databaseTree [type=db].jstree-clicked').each(function(){
				array[array.length]={'db':$.trim($(this).text())};
			});	
		break;
		case 'columns':
			$('#databaseTree [type=table].jstree-clicked').each(function(){
				array[array.length]={'db':$(this).attr('db'),'table':$.trim($(this).text())};
			});
		break;
		case 'data':
			$('#databaseTree [type=column].jstree-clicked').each(function(){
				if(typeof array[0] =='undefined'){
					array[0]={'db':$(this).attr('db'),'table':$(this).attr('table'),'column':new Array()};
				}
				array[0].column[array[0].column.length]=$.trim($(this).text());
			});
		break;
	}
	if(array.length<1){
		changeStatus(0);
		return false;
	}
	petition.array=array;
	$.post('',{injection:injection,petition:petition},function (data)
	{
		changeStatus(0);
		data=jQuery.parseJSON(data);
		if(data.injection.length<1)
		{
			toLog('No injection found','red');
			return false;
		}

		injection=data.injection;
		parseDBs(injection.databases);
		if(info=='data'){
			parseDump(data.result);
		}
		
			
	});	
}
function clearLog(){
	$('#console').html('');
}
function changeStatus(status)
{
	if(status){
		$('#loadingIcon').show();
		$('#status').text("Working");
	}else{
		$('#loadingIcon').hide();
		$('#status').text("I'm IDLE");
	}
}
function toLog(text,type)
{
	if(typeof type =='undefined'){
		type='';
	}
	$('#console').append('<span class="'+type+'">'+text+'</span><br>');
	$("#console").animate({ scrollTop: $('#console').height() }, "fast");
}
function parseDBs(dbs,logIt)
{
	$('#showTables').attr('disabled','disabled');
	$('#showColumns').attr('disabled','disabled');
	$('#showData').attr('disabled','disabled');
	
	var html='<ul>',
		html2='',
		html3='',
		count=0,
		count2=0,
		openLis=new Array(), //what lis will shown open after parse
		jsTree={"plugins" : ["themes","html_data","ui","crrm"]};
		
	for(k in dbs) //parse DBs
	{
		html2='';
		html3='';
		if(typeof dbs[k]== "object")
		{
			html2='<ul rel="open">';
			for(i in dbs[k]) //parse tables
			{
				if(typeof dbs[k][i]== "object")
				{
					html3='<ul>';
					for(e in dbs[k][i]) //parse columns
					{
						html3+='<li><a href="#" type="column" table="'+i+'" db="'+k+'">'+dbs[k][i][e]+'</a></li>';
					}
					html3+='</ul>';
					$('#showData').removeAttr('disabled','');
					openLis[openLis.length]='openTB'+count2;	
				}
				html2+='<li id="openTB'+count2+'"><a href="#" type="table" db="'+k+'">'+i+'</a> '+html3+'</li>';
				html3='';
				count2++;
			}
			html2+='</ul>';
			$('#showColumns').removeAttr('disabled','');
			openLis[openLis.length]='open'+count;						
		}
			
		html+='<li id="open'+count+'" ><a href="#" type="db">'+k+'</a> '+html2+'</li>';
		
		if(typeof logIt !='undefined'){
			toLog('Data Base Found: '+k,'blue');
		}
		$('#showTables').removeAttr('disabled','');
		count++;
	}
	$('#databaseTree').html(html);
	
	if(openLis.length>0){
		jsTree.core={ "initially_open" : openLis };
	}
	
	$("#databaseTree").jstree(jsTree);
}
function check()
{
	var url=$('#url').val();		
	if(url==''){return false;}
	changeStatus(1);
	toLog('Checking site... May take a long time..');
	
	myURL =parseURL(url);
	$('#adminUrl').val(myURL.protocol+'://'+myURL.host);
	
	$.post('',{url:url},function (data)
	{
		data=jQuery.parseJSON(data);
		changeStatus(0);
		
		if(data.injection.length<1)
		{
			toLog('No injection found','red');
			return false;
		}

		injection=data.injection;
		var html='<span class="green">Injection found</span><br>';
		$('#console').html(html);
		html='<span class="green">Selected Column Count is '+injection.column.num_columns+'</span><br>'+
			'<span class="green">Valid String Column is '+injection.column.affected+'</span><br>'+
			'<span class="blue">DB Server: MySQL</span><br>';
		$('#console').append(html);
				
		parseDBs(injection.databases,true);
										
	})
	.fail(function() { $('#console').html('Something went wrong');changeStatus(0); });
}
function parseURL(url) {
    var a =  document.createElement('a');
    a.href = url;
    return {
        source: url,
        protocol: a.protocol.replace(':',''),
        host: a.hostname,
        port: a.port,
        query: a.search,
        params: (function(){
            var ret = {},
                seg = a.search.replace(/^\?/,'').split('&'),
                len = seg.length, i = 0, s;
            for (;i<len;i++) {
                if (!seg[i]) { continue; }
                s = seg[i].split('=');
                ret[s[0]] = s[1];
            }
            return ret;
        })(),
        file: (a.pathname.match(/\/([^\/?#]+)$/i) || [,''])[1],
        hash: a.hash.replace('#',''),
        path: a.pathname.replace(/^([^\/])/,'/$1'),
        relative: (a.href.match(/tps?:\/\/[^\/]+(.+)/) || [,''])[1],
        segments: a.pathname.replace(/^\//,'').split('/')
    };
}