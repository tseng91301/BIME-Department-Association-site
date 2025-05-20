# 台大生機系 - 系學會網站

* 因為此網站仍在運作中，因此有些 secret resource 不會上傳到 Repository 上

### 程式碼相關資料

`local_server/`:
裡面包含著網站的每個頁面以及 php 後台，相關 secret 或帳號資訊都會放在資料庫或 environment 文件中，確保安全性。

`app.py` 以及 `forward_service.py` 是架設在目前伺服器上面所需要的文件，因為我們的伺服器在學校裡面，比較沒辦法直接開放防火牆給外部直接連線，因此我使用轉發機制。透過 render 伺服器託管平台暫存使用者請求，伺服器本機執行 `forward_service.py` ，抓取 render 上暫存的使用者請求，經過伺服器後端處理完再發送回去，讓平台轉發回應給使用者，透過這種方法巧妙繞過防火牆限制。

`app.py` 是放在 render 平台供其執行的程式碼，裡面除了請求轉發之外，還有關於 Line chat bot 的相關設置，這邊同樣是將 Line bot 接收到的訊息轉發到本機伺服器中，在由其進行處理。

在根目錄列表中有數個 `.sh` 執行檔，那是給伺服器開啟服務的腳本。

### To Access This Site

* 造訪網站： https://ntu-bime-linebot.onrender.com/index.html
* Line Bot 連結： https://line.me/R/ti/p/%40074sjmyz
* Line Bot ID： @074sjmyz