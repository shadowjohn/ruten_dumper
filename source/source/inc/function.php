<?php
function getRutenItemInfo($URL){
  global $PP;
  global $SP;
  global $UID;
  global $kind_name_big5;
  global $WGET;
  global $CURL;
  global $logtxt;
  global $CKS;
  //$data=`{$WGET} -O- -q --tries=2 --user-agent="Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:21.0) Gecko/20100101 Firefox/21.0" --referer "{$URL}" --save-cookies cookies.txt --header "Cookie: _ts_id=3wagood" "{$URL}#auc"`;
  //echo $URL;
  //exit();
  $data=`{$WGET} -O- -q --tries=2 --user-agent="Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:21.0) Gecko/20100101 Firefox/21.0" --referer "{$URL}" --keep-session-cookies --load-cookies={$PP}{$SP}cookie.txt --save-cookies={$PP}{$SP}cookie.txt --header "Cookie: {$CKS}" "{$URL}"`;
  //$data = `{$CURL} --cookie-jar "{$PP}{$SP}cookie_curl.txt" "{$URL}" -H "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0" -H "Cookie: _ts_id=3400390A3302350C3C0B;"`;          
  //print_r($data);
  //exit();
  $OUTPUT=ARRAY();
  $OUTPUT['商品編號']="";
  $OUTPUT['拍賣網址']="";
  $OUTPUT['分類']="";
  $OUTPUT['標題']="";
  $OUTPUT['直標價']="";
  $OUTPUT['尚餘數量']="";
  $OUTPUT['物品所在地']="";
  $OUTPUT['上架時間']="";
  $OUTPUT['內容']="";
  $OUTPUT['照片網址1']="";
  $OUTPUT['照片網址2']="";
  $OUTPUT['照片網址3']="";
  $OUTPUT['照片']="";
  //商品編號-------------------------------------------
  $title = strip_tags(getDom($data,".item-number .content")[0]);
  $title = trim($title);
  $title = str_replace_deep(" ","",$title);
  $OUTPUT['商品編號']=$title;  
  //拍賣網址-------------------------------------------
  $OUTPUT['拍賣網址']=$URL;
  //分類-------------------------------------------
  $title = strip_tags(getDom($data,".breadcrumb-content .breadcrumb-text")[0]);
  $title = trim($title);
  $title = str_replace_deep(" ","",$title);
  $OUTPUT['分類']=$title;
  //標題-------------------------------------------
  $title = strip_tags(getDom($data,".item-title")[0]);
  $title = trim($title);
  $OUTPUT['標題']=$title;
  //直標價-------------------------------------------
  $title = strip_tags(getDom($data,".item-purchase-stack strong")[0]);
  $title = trim($title);
  $OUTPUT['直標價']=$title;
  //尚餘數量-------------------------------------------
  $title = strip_tags(getDom($data,".item-info-detail tbody td strong")[0]);
  $title = trim($title);
  $OUTPUT['尚餘數量']=$title;
  //物品所在地-------------------------------------------
  $title = strip_tags(getDom($data,".location .content")[0]);
  $title = trim($title);
  $OUTPUT['物品所在地']=$title;
  //上架時間-------------------------------------------
  $dtitle = strip_tags(getDom($data,".upload-time span .date")[0]);
  $dtitle = trim($dtitle);
  $ttitle = strip_tags(getDom($data,".upload-time span .time")[0]);
  $ttitle = trim($ttitle);
  $OUTPUT['上架時間']="{$dtitle} {$ttitle}";
  //內容-------------------------------------------
  $iframe_src=trim(getDomF($data,"#embedded_goods_comments","src")[0]);  
  //容網址
  //echo $iframe_src;  
  $cmd="{$WGET} -O - -q --user-agent=\"Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:21.0) Gecko/20100101 Firefox/21.0\" --referer \"{$URL}\" --keep-session-cookies --load-cookies={$PP}{$SP}cookie.txt --save-cookies={$PP}{$SP}cookie.txt --header \"Cookie: {$CKS}\" \"{$iframe_src}\" ";
  //$cmd = addslashes($cmd);
  $cmd = htmlspecialchars_decode($cmd);
  $content_data = `{$cmd}`;
  //$content_data = file_get_contents("{$PP}{$SP}CONTENT.txt");
  $content_data = str_replace_deep("\r","\n",$content_data);
  $content_data = str_replace_deep("\n\n","\n",big5toutf8(get_between_new($content_data,"<body>","</body>")));
  $content_data = str_replace(",","，",$content_data);
  $content_data = br2nl($content_data);
  $content_data = strip_tags($content_data);
  $content_data = trim($content_data);  
  $OUTPUT['內容']="{$content_data}";
  //ap_log($logtxt,print_r($OUTPUT,true));  
  //exit();
  //$OUTPUT['CMD']=$cmd;  
  //照片網址123-------------------------------------------
  $pgj = get_between_new($data,"RT.context = ",";");
  $jpgj=json_decode($pgj,true);
  $imgs=ARRAY();
  $step=1;
  foreach($jpgj['goods_img'] as $v)
  {
    if($v=="") continue;
    $URL = "https://img.ruten.com.tw/{$v}";
    //array_push($imgs,$URL);
    $OUTPUT['照片網址'.$step]=$URL;
    $step++;
  }
  
  //照片-------------------------------------------
  $pgj = get_between_new($data,"RT.context = ",";");
  $jpgj=json_decode($pgj,true);
  //$OUTPUT['照片']=print_r($jpgj,true); 
  //https://img.ruten.com.tw/s2/6/4f/ee/21613277213678_537.jpg
  @mkdir("{$PP}{$SP}{$UID}{$SP}{$kind_name_big5}{$SP}{$OUTPUT['商品編號']}",0777,true);
  $imgs=ARRAY();
  foreach($jpgj['goods_img'] as $v)
  {
    if($v=="") continue;
    $URL = "https://img.ruten.com.tw/{$v}";
    $bn = basename($URL);
    array_push($imgs,$bn);
    if(!is_file("{$PP}{$SP}{$UID}{$SP}{$kind_name_big5}{$SP}{$OUTPUT['商品編號']}{$SP}{$bn}"))
    {
      $cmd = "{$WGET} --no-check-certificate -O \"{$PP}{$SP}{$UID}{$SP}{$kind_name_big5}{$SP}{$OUTPUT['商品編號']}{$SP}{$bn}\" -q --user-agent=\"Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:21.0) Gecko/20100101 Firefox/21.0\" --referer \"{$URL}\" --keep-session-cookies --load-cookies={$PP}{$SP}cookie.txt --save-cookies={$PP}{$SP}cookie.txt --header \"Cookie: {$CKS}\" \"{$URL}\" ";
      `{$cmd}`;      
    }
  }
  $OUTPUT['照片']=implode(",",$imgs);
  return $OUTPUT; 
}