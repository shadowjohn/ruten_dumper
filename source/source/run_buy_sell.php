<?php
  include 'inc/config.php';
  include 'inc/ruten_api.php';
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
  //cookies
  $CKS="_ts_id=3wagood;adultchk=ok;bid_nick=3939889; login=1;";
  
  function ap_log($logtxt,$data)
  {
    file_put_contents($logtxt,$data,FILE_APPEND);
  }    
  $inipath = "{$PWD}{$SP}..{$SP}..{$SP}setting.ini";
  $ini=parse_ini_file($inipath);
  @mkdir("{$ini['RUTEN_PATH']}",0777,true);
  $PP="{$ini['RUTEN_PATH']}";
  if(is_file("{$PP}{$SP}18x_cookie.txt"))
  {
    //修正18禁也可以使用
    $CKS = file_get_contents("{$PP}{$SP}18x_cookie.txt");
  }
  if($argc!=2)
  {
    echo "必需要有會員id...\n";
    exit();
  }  
  
  echo "Starting download...{$argv[1]}\n";
  $UID=$argv[1];
  $logtxt="{$ini['RUTEN_PATH']}{$SP}{$UID}{$SP}log.txt";
     
  $RT = new ruten_buy_sell();
  $RT->setUID($UID);
  $totals = $RT->getTotals();
  
  echo "總頁數...{$totals['total_pages']}\n";
  echo "總筆數...{$totals['totals']}\n";
  //$json = $RT->getBuySellJson(1);
  //$RT->parseBuySellJson($json);
  $output_file_utf8 = "{$PP}{$SP}[{$UID}]_買賣紀錄.html";
  $output_file = utf8tobig5($output_file_utf8);
  file_put_contents($output_file,"");
  $tmp = "
<doctype html>
<html>
<head>
  <meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\">
  <title>{$UID} 露天買賣紀錄</title>
</head>
<body>
帳號：{$UID}<br>
交易總筆數：{$totals['totals']}<br>
總交易金額：{總交易金額}<br>
買東西金額：{買東西金額}<br>
賣東西金額：{賣東西金額}<br>
<hr>
";
  file_put_contents($output_file,$tmp);
  $tmp = "<table cellpadding='0' cellspacing='0' border='1'>";
  $tmp.= "<thead>";
  $tmp.= "<tr>";
    $tmp.= "<th>項次</th>";
    $tmp.= "<th>買賣時間</th>";
    $tmp.= "<th>賣家</th>";
    $tmp.= "<th>買家</th>";
    $tmp.= "<th>行為</th>";
    $tmp.= "<th>金額</th>";
    $tmp.= "<th>物品名稱</th>";
    $tmp.= "<th>網址</th>";
  $tmp.= "</tr>";
  $tmp.= "</thead>";
  $tmp.= "<tbody>";
  file_put_contents($output_file,$tmp,FILE_APPEND);
  $s = 1;    
  $total_money = 0;
  $total_sell_money = 0;
  $total_buy_money = 0;
  for($i=0;$i<$totals['total_pages'];$i++)
  {
    $tmp = "";
    $json = $RT->getBuySellJson($i);
    //echo $json;
    //exit();
    $jd = $RT->parseBuySellJson($json);
    //print_r($jd);
    for($j=0,$max_j=count($jd);$j<$max_j;$j++)
    {
      switch($jd[$j]['行為'])
      {
        case '賣':
          $total_sell_money+=$jd[$j]['金額'];
          break;
        case '買':
          $total_buy_money+=$jd[$j]['金額'];
          break;
      }
      $total_money+=$jd[$j]['金額'];
      $tmp = "
<tr>
  <td>{$s}</td>
  <td>{$jd[$j]['時間']}</td>
  <td>{$jd[$j]['賣家']}</td>
  <td>{$jd[$j]['買家']}</td>
  <td>{$jd[$j]['行為']}</td>
  <td>{$jd[$j]['金額']}</td>
  <td>{$jd[$j]['物品名稱']}</td>
  <td>{$jd[$j]['網址']}</td>
</tr>
";
      $s++;
      file_put_contents($output_file,$tmp,FILE_APPEND);
    }
    echo sprintf("{$UID} 資料取得中... %d / %d ...\n",($i+1),$totals['total_pages']);
  }
  $tmp = "</tbody>";
  $tmp.= "</html>";
  file_put_contents($output_file,$tmp,FILE_APPEND);
  $data = file_get_contents($output_file);
  $data = str_replace("{總交易金額}",my_money_format($total_money),$data);
  $data = str_replace("{買東西金額}",my_money_format($total_buy_money),$data);
  $data = str_replace("{賣東西金額}",my_money_format($total_sell_money),$data);
  file_put_contents($output_file,$data);
  echo "\n完成，詳見：{$output_file_utf8}";   
  exit();  
