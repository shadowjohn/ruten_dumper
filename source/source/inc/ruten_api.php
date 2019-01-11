<?php
  class ruten_buy_sell{
    private $UID; 
    public function setUID($UID){
      $this->UID=$UID;
    }    
    public function getTotals()
    {
      global $CKS;
      global $WGET;
      $URL = "https://mybid.ruten.com.tw/credit/point?{$this->UID}";
      //echo $URL;
      $data = `{$WGET} -O- -q --tries=2 --user-agent="Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:21.0) Gecko/20100101 Firefox/21.0" --referer "{$URL}" --keep-session-cookies --header "Cookie: {$CKS}" "{$URL}"`;
      $data = big5toutf8($data);
      //file_put_contents("C:\\temp\\a.txt",$data);
      $m = explode("頁 共",trim(strip_tags(getDom($data,"td[class='text_SplitPage_4']")[0])));
      //第 1/41 頁 共 801 筆
      $OUTPUT=ARRAY();
      $OUTPUT['total_pages'] = trim(str_replace("第 1/","",trim($m[0])));
      $OUTPUT['totals'] = trim(str_replace(" 筆","",trim($m[1])));			      
      return $OUTPUT;
    }
    public function getBuySellJson($PAGE)
    {
      //第一頁是 0
      global $CKS;
      global $WGET; 
      $URL = "https://mybid.ruten.com.tw/credit/point?{$this->UID}&all&all&{$PAGE}";
      $data = `{$WGET} -O- -q --tries=2 --user-agent="Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:21.0) Gecko/20100101 Firefox/21.0" --referer "{$URL}" --keep-session-cookies --header "Cookie: {$CKS}" "{$URL}"`;
      $data = big5toutf8($data);
      $DATA = get_between_new($data,"var f_list=",";\n");
      //echo $DATA;
      //exit();
      return $DATA;
    }
    public function parseBuySellJson($json)
    {
      $jd = json_decode($json,true);
      $OUTPUT=ARRAY();
      for($i=0,$max_i=count($jd['OrderList']);$i<$max_i;$i++)
      {
        /*
        [no] => 11090819520254
        [name] => 【歐樂克修繕家】Environment Friendly 南星 油漆去除劑 去漆劑 去漆水 除漆 低味去漆劑 4公斤
        [point] => good
        [user] => chunje
        [user_type] => s
        [credit] => 12675
        [good_credit] => 26070
        [date] => 2018-11-22 14:24:05
        [bid_date] => 2018-11-14 10:06:21
        [change_date] => 
        [money] => 700元
        [is_back] => n
        [hidden_pic] => n
        [is_set_trans] => none
        [content] => Array
            (
                [0] => Array
                    (
                      [type] => p
                      [content] => 專業.用心服務的好賣家!!! 
                      [date] => 2018-11-22 14:00:01
                    )

            )

        */
        $d = ARRAY();
        $d['網址'] = ($jd['OrderList'][$i]['no']!="")? "https://goods.ruten.com.tw/item/show?{$jd['OrderList'][$i]['no']}":"";
        $d['行為'] = ($jd['OrderList'][$i]['user_type']=='s') ? "買":"賣";        
        $d['露天編號'] = $jd['OrderList'][$i]['no'];
        $d['物品名稱'] = $jd['OrderList'][$i]['name'];
        $d['評價'] = $jd['OrderList'][$i]['point'];
        $d['買家'] = ($jd['OrderList'][$i]['user_type']=='s') ? $this->UID : $jd['OrderList'][$i]['user'];
        $d['賣家'] = ($jd['OrderList'][$i]['user_type']=='b') ? $this->UID : $jd['OrderList'][$i]['user'];
        $d['時間'] = $jd['OrderList'][$i]['date'];
        $d['金額'] = str_replace("元","",$jd['OrderList'][$i]['money']);
        
        array_push($OUTPUT,$d);
      }
      return $OUTPUT;
    } 
  }