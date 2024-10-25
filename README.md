# AI-chat-chat
這是一個可以與 AI 聊天的平台。

## Database Schema


## API Document
[AI-chat-chat API 文件](https://hospitable-pound-cd1.notion.site/AI-chat-chat-API-548b8fccf2fa438795b0da669b117fa1?pvs=74)

## Installation
1. 若未下載 Docker Desktop 或是 [OrbStack](https://orbstack.dev/)（建議）者，需先下載。

2. 先確認有沒有任何程序佔用 80 port（或是 Docker 要使用的 port 號），若有，需先停止。

3. 將 fork 的專案 clone 至本地，請執行以下 command：
( `Path` 為欲放專案的本地路徑， `Username` 為個人 GitHub 帳號， `Your Name` 為專案名稱後綴，請自行替換)
```
cd {Path}
```
```
git clone https://github.com/{Username}/BECamp_T13_HW2_Laravel-AI_{Your Name}
```

4. 將專案中的 .env.example 複製一份在專案中，並將檔名改為 .env ，完成後儲存。

5. 請執行以下 command ，安裝專案所需相關套件並啟動開發環境：
```
composer install
```
```
./vendor/bin/sail up -d
```
```
./vendor/bin/sail artisan key:generate
```
```
./vendor/bin/sail artisan migrate
```


## Feature
### Authentication
- 註冊
- 登入
- 登出

### Thread
- 新增一個對話串
- 編輯對話串名稱
- 刪除一個對話串

### Message
- 新增一則文字訊息並得到一則AI回覆文字訊息


## Demo
### Authentication
- 註冊
- 登入
- 登出
- 串接 OpenAI API，透過夾帶著註冊名稱的 prompt，檢測名稱是否違反善良風俗

https://github.com/user-attachments/assets/537448b6-b1a8-4b43-855b-7f576e835ec0

### Thread
- 新增、編輯、刪除一個對話串
- 使用 Policy 進行新增、編輯、刪除權限控管

https://github.com/user-attachments/assets/da5bb003-b0a1-48d0-959e-c66812601be0

### Message
- 串接 OpenAI API，新增一則文字訊息並得到一則 AI 回覆文字訊息

https://github.com/user-attachments/assets/d61f2865-c44f-4a62-bd22-22dbfe62a158

