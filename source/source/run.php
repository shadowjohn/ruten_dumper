<?php
  include 'inc/config.php';
  error_reporting(E_ALL & ~E_NOTICE);
  $SP = DIRECTORY_SEPARATOR;
  @ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)');    
  @ini_set('memory_limit','2048M');
  @ini_set('post_max_size', '2048M');    
  @header("Content-Type: text/html; charset=utf-8");          
  date_default_timezone_set("Asia/Taipei");
  $PWD=dirname(__FILE__);
  $WGET="{$PWD}{$SP}bin{$SP}wget.exe";
  $CURL="{$PWD}{$SP}bin{$SP}curl.exe";  
  
  function ap_log($logtxt,$data)
  {
    file_put_contents($logtxt,$data,FILE_APPEND);
  }    
  $inipath = "{$PWD}{$SP}..{$SP}..{$SP}setting.ini";
  $ini=parse_ini_file($inipath);
  @mkdir("{$ini['RUTEN_PATH']}",0777,true);
  $PP="{$ini['RUTEN_PATH']}";
  if($argc!=3)
  {
    echo "必需要有會員id...\n";
    exit();
  }  
  $istry=$argv[2];
  echo "Starting download...{$argv[1]}\n";
  $UID=$argv[1];
  $logtxt="{$ini['RUTEN_PATH']}{$SP}{$UID}{$SP}log.txt";
   
  $URL = "http://class.ruten.com.tw/user/class_frame.php?sid={$UID}";  
  $ch = curl_init();
//   curl_setopt($ch, CURLOPT_URL, $URL);
//   $RANDOM_USER_AGENT = sprintf("Mozilla/%d.%d (compatible; MSIE %d.%d; Windows NT %d.%d; SV1)",
//                                           rand(3,5),
//                                           rand(0,2),
//                                           rand(4,6),
//                                           rand(0,2),
//                                           rand(5,6),
//                                           rand(0,2)
//                                           );
//   //curl_setopt($ch, CURLOPT_COOKIE, "_ts_id=".urlencode("3WA羽山超帥"));
//   curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)");  
//   curl_setopt($ch, CURLOPT_USERAGENT, $RANDOM_USER_AGENT);
//   curl_setopt($ch, CURLOPT_COOKIEJAR, "{$PP}{$SP}cookie.txt");                  //保存cookie
//   curl_setopt($ch, CURLOPT_COOKIEFILE, "{$PP}{$SP}cookie.txt"); 
//   echo "\n\nCookie: {$PP}{$SP}cookie.txt \n\n";                 
//   curl_setopt($ch, CURLOPT_REFERER, $URL);
//   ob_start();
//   curl_exec($ch);
//   $data = ob_get_contents();
//   ob_end_clean();
  $data=`{$WGET} -O- -q --tries=2 --user-agent="Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:21.0) Gecko/20100101 Firefox/21.0" --referer "{$URL}" --load-cookies={$PP}{$SP}cookie.txt --save-cookies={$PP}{$SP}cookie.txt --keep-session-cookies --header "Cookie: _ts_id=3wagood" "{$URL}"`;
  $data = str_replace("</a> ","</a>\n",$data);
        
  $data = str_replace("</font><a ","</font><aa \n",$data); 
  
  preg_match_all('/a href=\"(.*)" target="_top">(.*)<\/a>\n/', $data,$all_kind);
  //print_r($all_kind);
  $user_kinds = ARRAY();
  //pre_print_r($all_kind);
  //unset($all_kind[0][count($all_kind[0])-1]);
  //unset($all_kind[1][count($all_kind[1])-1]);
  //unset($all_kind[2][count($all_kind[2])-1]);
  
  //&c=0&d=&o=2&m=1&p=2&k=
  //http://class.ruten.com.tw/user/index00.php?s=a0938160803&c=0&d=&o=2&m=1&p=2&k=    
  $all_kind[0][count($all_kind[0])] = "http://class.ruten.com.tw/user/index00.php?s={$UID}&c=0&d=&o=2&m=1&k=";
  $all_kind[1][count($all_kind[1])] = "index00.php?s={$UID}&c=0&d=&o=2&m=1&k=";
  $all_kind[2][count($all_kind[2])] = "全部商品";
  
  for($i=0;$i<count($all_kind[1]);$i++)
  {
    $d = ARRAY();
    $d['url']=$all_kind[1][$i];
    $d['name']=$all_kind[2][$i];
    $d['name'] = str_replace("/","_",$d['name']);
    $d['name'] = str_replace(" ","",$d['name']);
    array_push($user_kinds,$d);
  }

  //印出有哪些分類
  print_r($user_kinds);
  //exit();

  $first_time = 1;
  $step=0;
  foreach($user_kinds as $kind_k=>$kind_v)
  {    
    $step++;
    if($step<=8) continue;
    if( $kind_v['name'] == '全部商品') continue;
    if( $kind_v['name'] != '未分類商品(66)') continue;
    
    $kind_name_big5 = utf8tobig5($kind_v['name']);
    //$kind_name_big5 = utf8tobig5("BOSCH");
    $kind_name_big5 = str_replace("?","",$kind_name_big5);
    $kind_name_big5 = str_replace("*","",$kind_name_big5);
    //$kind_name_big5 = addslashes($kind_name_big5);
    
    if(count($user_kinds)!=1)
    {
      if(
        //$kind_v['name']=='全部商品'
        //||
        //$kind_v['name']=='無法分類'
        //||
        //$kind_v['name']!='►汽車配件_收納'
        1
      )
      {
        //continue;
      }
    }
    else
    {
      $kind_v['url']="index00.php?s={$UID}&c=0&d=&o=2&m=1&k=";
      $kind_v['name']='全部商品';
    }
     
    @mkdir("{$PP}{$SP}{$UID}{$SP}{$kind_name_big5}",0777,true);
    if(!is_dir("{$PP}{$SP}{$UID}{$SP}{$kind_name_big5}"))
    {
      echo "ERROR: {$kind_v['name']}";
      exit();
    }          
    file_put_contents("{$PP}{$SP}{$UID}{$SP}{$kind_name_big5}{$SP}{$kind_name_big5}.xls","");
    //print_r($user_kinds);
    //exit();
  
    $URL = "http://class.ruten.com.tw/user/{$kind_v['url']}";
    echo $URL;
    
    //$content=`{$WGET} -O- -q --tries=2 --user-agent="Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:21.0) Gecko/20100101 Firefox/21.0" --referer "{$URL}" --save-cookies cookies.txt --header "Cookie: _ts_id=3wagood" "{$URL}"`;
    $content=`{$WGET} -O- -q --tries=2 --user-agent="Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:21.0) Gecko/20100101 Firefox/21.0" --referer "{$URL}" --load-cookies={$PP}{$SP}cookie.txt --save-cookies={$PP}{$SP}cookie.txt --keep-session-cookies --header "Cookie: _ts_id=3wagood" "{$URL}"`;
    //$data = big5toutf8($content);
    $pgj = get_between_new($content,"RT.context = ",";");
    $jpgj = json_decode($pgj,true);
        
    //總頁數=ceil($jpgj['page']['total']/$jpgj['page']['perPage'])
    $totals_page=ceil($jpgj['page']['total']/$jpgj['page']['perPage']);
    echo "總筆數={$jpgj['page']['total']}\n";
    echo "總頁數={$totals_page}\n";
    //exit(); 
    $OUTPUT=ARRAY();    
    for($i=0,$max_i=$totals_page;$i<$max_i;$i++)
    {      
      $page=($i+1);
      $URL = "http://class.ruten.com.tw/user/{$kind_v['url']}&c=0&o=2&m=1&p={$page}";
      echo $URL;
      //exit();           
      $data=`{$WGET} -O- -q --tries=2 --user-agent="Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:21.0) Gecko/20100101 Firefox/21.0" --referer "{$URL}" --load-cookies={$PP}{$SP}cookie.txt --save-cookies={$PP}{$SP}cookie.txt --keep-session-cookies --header "Cookie: _ts_id=3wagood" "{$URL}"`;        
      
      //       ap_log($logtxt,$data);
      //       exit();
      //       if($data=="")
      //       {              
      //         $i--;
      //         usleep(1000);
      //         echo "Retry...no data {$i}<br>";
      //         continue;
      //       }  
      //$titles = getDom($data,".item-info h3 a");
      //print_r($titles);
      $links= getDomF($data,".item-info h3 a","href");
      print_r($links);
      for($j=0,$max_j=count($links);$j<$max_j;$j++)
      {
        //here get contents
        if($istry=="1")
        {
          if($max_j>=5){
            $max_j=5;
          }
        }
        $item_info = getRutenItemInfo($links[$j]);
        print_r($item_info);
        
        array_push($OUTPUT,$item_info);
      }
      if($istry=="1")
      {
        break;
      }
      //exit();
    }
    /*$csv = print_csv($OUTPUT,
        $fields='商品編號,分類,標題,直標價,尚餘數量,物品所在地,上架時間,內容,照片',
        $headers='商品編號,分類,標題,直標價,尚餘數量,物品所在地,上架時間,內容,照片',
        $is_need_header=true);
    */
    //$csv = utf8tobig5($csv);
    //file_put_contents("{$PP}{$SP}{$UID}{$SP}{$UID}_{$kind_name_big5}.csv",$csv);
    //原本想轉big5，好像不用了
    for($i=0,$max_i=count($OUTPUT);$i<$max_i;$i++)
    {
      foreach($OUTPUT[$i] as $k=>$v)
      {
        //$OUTPUT[$i][$k]=utf8tobig5($OUTPUT[$i][$k]);
      }
    }
    $output_file="{$PP}{$SP}{$UID}{$SP}{$kind_name_big5}{$SP}{$kind_name_big5}.xls";
    save_xls($output_file,$OUTPUT);
    echo "\n\nDone... ".big5toutf8($output_file)." -> {$kind_v['name']}\n";
    //exit();
  }