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
  $data=`{$WGET} -O- -q --no-check-certificate --tries=2 --user-agent="Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:21.0) Gecko/20100101 Firefox/21.0" --referer "{$URL}" --keep-session-cookies --load-cookies={$PP}{$SP}cookie.txt --save-cookies={$PP}{$SP}cookie.txt --header "Cookie: {$CKS}" "{$URL}"`;
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
  $OUTPUT['照片網址']="";
  
  //商品編號-------------------------------------------  
  //file_put_contents("C:\\ruten\\a.txt",$data);
  $jd = json_decode(getDom($data,"script[type=\"application/ld+json\"]")[0],true);
  /*
  Array
(
    [@context] => http://schema.org/
    [@type] => Product
    [name] => 機車考照，機車考題，二本一起念，必過
    [image] => https://img.ruten.com.tw/s2/c/5e/56/21910111959638_710.jpg
    [description] => 直購價：80元。物品狀態：使用不到一週。支付方式包含PChomePay支付連、郵寄、超商取貨付款、貨到付款、面交取貨付款。(21910111959638)。露天拍賣提供shadowjohn的賣場的交通工具 , 機車百貨 , 其他機車百貨等眾多商品，歡迎參觀選購！
    [productId] => 21910111959638
    [brand] => Array
        (
            [@type] => Thing
            [name] => shadowjohn
        )

    [offers] => Array
        (
            [@type] => Offer
            [priceCurrency] => TWD
            [price] => 80
            [availability] => http://schema.org/InStock
        )

)
  */
  $title = $jd['productId'];
  $title = strip_tags($title);
  $title = trim($title);
  $title = str_replace_deep(" ","",$title);
  $OUTPUT['商品編號']=$title;  
  //拍賣網址-------------------------------------------
  $OUTPUT['拍賣網址']=$URL;
  //分類-------------------------------------------
  $title = strip_tags(getDom($data,".breadcrumb-content .breadcrumb-text")[0]);
  $title = trim($title);
  $title = str_replace_deep(" ","",$title);
  $title = current(explode("{",$title));
  $OUTPUT['分類']=$title;    
  //標題-------------------------------------------
  $title = $jd['name'];
  $title = strip_tags($title);
  $title = trim($title);
  $OUTPUT['標題']=$title;
 
  //直標價-------------------------------------------
  $title = $jd['offers']['price'];  
  $title = trim($title);
  $title = str_replace("&#36;","",$title);
  $OUTPUT['直標價']=$title;  
  //尚餘數量-------------------------------------------
  //$title = strip_tags(getDom($data,"strong[class='rt-text-isolated']")[1]);
  $jd1 = json_decode(get_between_new($data,"RT.context = ",";"),true);
  
  $title = $jd1['item']['remainNum'];
  $title = trim($title);
  $OUTPUT['尚餘數量']=$title;  
  //物品所在地-------------------------------------------
  $title = $jd1['item']['location'];
  $title = trim($title);
  $title = str_replace("物品所在地：&nbsp;","",$title);
  $OUTPUT['物品所在地']=$title;
  //上架時間-------------------------------------------
  $title = $jd1['item']['postDateTime'];
  $title = trim($title);  
  $OUTPUT['上架時間']="{$title}";  
  //內容-------------------------------------------
  $iframe_src=trim(getDomF($data,"#embedded_goods_comments","src")[0]);
      
  //容網址
  //
  $iframe_src = "https://goods.ruten.com.tw/item/{$iframe_src}";
  //echo $iframe_src;
  //exit();  
  $cmd="{$WGET} -O - -q --user-agent=\"Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:21.0) Gecko/20100101 Firefox/21.0\" --referer \"{$URL}\" --keep-session-cookies --load-cookies={$PP}{$SP}cookie.txt --save-cookies={$PP}{$SP}cookie.txt --header \"Cookie: {$CKS}\" \"{$iframe_src}\" ";
  //$cmd = addslashes($cmd);
  $cmd = htmlspecialchars_decode($cmd);
  $content_data = `{$cmd}`;
  //file_put_contents("C:\\ruten\\a.txt",$content_data);
  //$content_data = file_get_contents("{$PP}{$SP}CONTENT.txt");
  //echo $content_data;
  //exit();  
  $content_data = str_replace_deep("\r","\n",$content_data);
  $content_data = str_replace_deep("\n\n","\n",get_between_new($content_data,"<body>","</body>"));
  $content_data = str_replace(",","，",$content_data);
  $content_data = str_replace("+","＋",$content_data);
  $content_data = str_replace(":","：",$content_data);
  $content_data = str_replace("🔺"," ",$content_data);
  $content_data = str_replace("➕"," ",$content_data);
  $OUTPUT['內容(HTML)']=$content_data;
  //file_put_contents("C:\\ruten\\".time().".txt",$content_data);
  $DATA_HTML="{$content_data}";
  $content_data = br2nl($content_data);
  $content_data = strip_tags($content_data);
  $content_data = trim($content_data);
    
  $OUTPUT['內容']=$content_data;
  
  
  //print_r($OUTPUT);
  //exit(); 
  
  //嘗試抓內文的圖片
  $content_html = str_get_html($DATA_HTML);
  $content_img_arr = ARRAY();  
  foreach($content_html->find('img') as $element)
  {
    $PIC_URL = $element->src;
    $bn = basename($PIC_URL);
    array_push($content_img_arr,$PIC_URL);
    if(!is_file("{$PP}{$SP}{$UID}{$SP}{$kind_name_big5}{$SP}{$OUTPUT['商品編號']}{$SP}{$bn}"))
    {
      $cmd = "{$WGET} --no-check-certificate -O \"{$PP}{$SP}{$UID}{$SP}{$kind_name_big5}{$SP}{$OUTPUT['商品編號']}{$SP}{$bn}\" -q --user-agent=\"Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:21.0) Gecko/20100101 Firefox/21.0\" --referer \"{$URL}\" --keep-session-cookies --load-cookies={$PP}{$SP}cookie.txt --save-cookies={$PP}{$SP}cookie.txt --header \"Cookie: {$CKS}\" \"{$PIC_URL}\" ";
      echo "抓內容圖...: {$PIC_URL} => {$PP}{$SP}{$UID}{$SP}{$kind_name_big5}{$SP}{$OUTPUT['商品編號']}{$SP}{$bn}\n";
      `{$cmd}`;
      //exit();
    }
  }
  $OUTPUT['內容圖片']=implode("\n",$content_img_arr);
  
  //ap_log($logtxt,print_r($OUTPUT,true));  
  //exit();
  //$OUTPUT['CMD']=$cmd;  
  //照片網址123-------------------------------------------
  $pgj = get_between_new($data,"RT.context = ",";");
  $jpgj=json_decode($pgj,true);
  $imgs=ARRAY();
  $step=1;
  $pics=ARRAY();
  foreach($jpgj['item']['images'] as $v)
  {
    if($v=="") continue;
    //$URL = "https://img.ruten.com.tw/{$v['ori']}";
    $URL = "{$v['original']}";
    array_push($pics,$URL);
    //array_push($imgs,$URL);    
    $step++;    
  }
  $OUTPUT['照片網址']=implode("\n",$pics);

  //照片-------------------------------------------
  $pgj = get_between_new($data,"RT.context = ",";");
  $jpgj=json_decode($pgj,true);
  //$OUTPUT['照片']=print_r($jpgj,true);
  //print_r($jpgj); 
  //https://img.ruten.com.tw/s2/6/4f/ee/21613277213678_537.jpg
  @mkdir("{$PP}{$SP}{$UID}{$SP}{$kind_name_big5}{$SP}{$OUTPUT['商品編號']}",0777,true);
  $imgs=ARRAY();
  foreach($jpgj['item']['images'] as $v)
  {
    if($v=="") continue;                       
    $URL = "{$v['original']}";
    $bn = basename($URL);
    array_push($imgs,$bn);
    if(!is_file("{$PP}{$SP}{$UID}{$SP}{$kind_name_big5}{$SP}{$OUTPUT['商品編號']}{$SP}{$bn}"))
    {
      $cmd = "{$WGET} --no-check-certificate -O \"{$PP}{$SP}{$UID}{$SP}{$kind_name_big5}{$SP}{$OUTPUT['商品編號']}{$SP}{$bn}\" -q --user-agent=\"Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:21.0) Gecko/20100101 Firefox/21.0\" --referer \"{$URL}\" --keep-session-cookies --load-cookies={$PP}{$SP}cookie.txt --save-cookies={$PP}{$SP}cookie.txt --header \"Cookie: {$CKS}\" \"{$URL}\" ";
      `{$cmd}`;      
    }
  }
  //$OUTPUT['照片']=implode(",",$imgs);
  return $OUTPUT; 
}