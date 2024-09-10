# Laravel OpenAI

## Introduction

## Prerequisites

## Installation (Author: [Helen](https://github.com/lovepp0518))

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


## 作業目標

## Usage
- 請 Fork 一份到 `Goodidea-backend-camp` 這個 Organization，名稱取叫 `BECamp_T13_HW2_Laravel-AI_{Your Name}`，例如 `BECamp_T13_HW2_Laravel-AI_JYu`。
- 根據每個功能開 branch，發 PR 到自己的 main 分支。
- 發 PR 前請先確認 CI 流程有通過。
- 主專案不定時會進行調整，請盡量保持與主專案最新狀態。
- 本專案有使用 [Laravel Sail](https://laravel.com/docs/11.x/sail)，自己斟酌要不要使用。

## Working Flow

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
- 新增一則文字訊息並得到一則AI回覆文字訊息（Chat Thread）


## Demo
### Authentication
- 註冊
- 登入
- 登出
- 串接 OpenAI API ，透過夾帶著註冊名稱的 prompt，檢測名稱是否違反善良風俗

https://github.com/user-attachments/assets/537448b6-b1a8-4b43-855b-7f576e835ec0

### Thread
- 新增、編輯、刪除一個對話串
- 使用 Policy 進行新增、編輯、刪除權限控管

https://github.com/user-attachments/assets/da5bb003-b0a1-48d0-959e-c66812601be0

### Message
- 新增一則文字訊息並得到一則AI回覆文字訊息（Chat Thread）
- 串接 OpenAI API 完成

https://github.com/user-attachments/assets/d61f2865-c44f-4a62-bd22-22dbfe62a158

