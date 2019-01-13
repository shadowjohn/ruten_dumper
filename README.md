# ruten_dumper
<h2>露天資料匯出機</h2>
<br>
<br>
版本：V1.4
作者：羽山秋人 (http://3wa.tw)

使用方式：<br>
1、輸入使用者的帳號，如:shadowjohn<br>
2、按下「開始匯出」<br>
3、試用模式是站長想拿來喬錢時才會使用的，匯些Sample給人看<br>
4、預設下載位置：C:\ruten<br>
   詳見setting.ini可修改
   RUTEN_PATH=C:\ruten

心得：<br>
    此程式是使用node-webkit搭配PHP 5.6開發而成。<br>
爬虫的工具我覺得php內鍵的curl有點怪，特別是windows下cookie的部分。<br>
在Linux裡是滿正常，所以直接用wget、curl替代。<br>
目前V1.0尚未把不需要的東西精簡，先發行上來，所以有點大包。<br>
能用是福，我是這麼想的XD<br>

圖片參考：<br>
<center>
父親節這天重新再整理一下，重新把露天拍賣匯出機寫好了<br>
<br>
<img src="http://3wa.tw/photo/small.php?w_size=850&compassion=95&file_name=users/shadow/20160808_234409_0.png&noshow=1">
<br>
比較特別的是，這次改用node-webkit寫成單機板<br>
<br>
更好維護，更好操作，在Windows下也可直接使用<br>
<br>
<img src="http://3wa.tw/photo/small.php?w_size=850&compassion=95&file_name=users/shadow/20160809_084715_0.png&noshow=1"><br>
<br>
匯出的過程隨時監看輕輕鬆鬆XD<br>
<br>
<img src="http://3wa.tw/photo/small.php?w_size=850&compassion=95&file_name=users/shadow/20160808_234409_1.png&noshow=1"><br>
<br>
抓下來的範例，圖片會依「商品編號」放置<br>
<br>
<img src="http://3wa.tw/photo/small.php?w_size=850&compassion=95&file_name=users/shadow/20160808_234409_2.png&noshow=1"><br>
<br>
資料夾裡有圖片<br>
<br>
<img src="http://3wa.tw/photo/small.php?w_size=850&compassion=95&file_name=users/shadow/20160808_235906_0.png&noshow=1"><br>
<br>
匯整的xls檔案<br>
<br>
試用模式還可以方便站長與「需求者」確認要匯的內容是否合宜~<br>
<br>
如要再進一步客製也是可以<br>
<br>
<br>
如有匯出的服務需求，可與站長討論：<br>
<br>
Line：shadowjohn<br>
<br>
當然協助匯出會需要一點點費用，量多有優惠XD<br>
</center>
<br>
<hr>

# Fri Jan 11 2019 <FeatherMountain(http://3wa.tw)> - V1.4
  - 可以取得使用者買賣歷史紀錄.
  
# Fri Jan 11 2019 <FeatherMountain(http://3wa.tw)> - V1.3
  - 修正WGET無法下載https的問題.
  - 修正金額前有亂碼.

# Mon Oct 03 2016 <FeatherMountain(http://3wa.tw)> - V1.2
  - 拍賣內容如果使用者有自己的圖片連結，也可以下載嘍.<br>    

# Tue Aug 08 2016 <FeatherMountain(http://3wa.tw)> - V1.1
  - 修正18禁也可以匯出成功的功能.

# Tue Aug 08 2016 <FeatherMountain(http://3wa.tw)> - V1.0
  - First version initial.                 
