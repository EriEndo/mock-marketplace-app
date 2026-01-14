# 実践学習ターム 模擬案件初級\_フリマアプリ

## プロジェクト概要

本プロジェクトは、COACHTECH 実践学習タームにおける模擬案件として開発した
フリマアプリ（coachtech フリマ）です。
ユーザーが商品を出品・購入できる基本的なフリマサービスを想定し、
実務を意識した設計・実装を行うことを目的としています。

本アプリでは、以下のような機能を実装しています。

- ユーザー登録・ログイン機能
- プロフィール登録・編集機能（住所情報含む）
- 商品の出品・一覧表示・詳細表示
- 商品へのいいね・コメント機能
- 商品購入機能（1 商品 1 購入制御、購入済み商品には SOLD 表示）
- ER 図・テーブル設計を意識したデータベース設計

## 環境構築

### Docker ビルド

#### 1. リポジトリをクローン

```bash
git clone git@github.com:EriEndo/mock-marketplace-app.git
cd mock-marketplace-app
```

#### 2. Docker の起動

```bash
Docker Desktop を起動
docker-compose up -d --build
```

### Laravel 環境構築

#### 1. Laravel のセットアップ

```bash
docker-compose exec php bash
composer install
cp .env.example .env
```

上記コマンドで .env ファイルを作成したら、開いて以下のように設定を変更します：

```text
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```

#### 2. アプリケーションキーの作成

```bash
php artisan key:generate
```

#### 3. マイグレーションの実行

```bash
php artisan migrate
```

#### 4. シーディングの実行

```bash
php artisan db:seed
```

#### 5. 画像アップロード用シンボリックリンク作成

```bash
php artisan storage:link
```

- 画像アップロード機能を利用するために必須です。
- public/storage → storage/app/public のシンボリックリンクが生成されます。
- このリンクは Git に含まれないため、clone した環境では 必ず実行してください。

#### 補足

"The stream or file could not be opened"エラーが発生した場合は、ディレクトリ/ファイルの権限を変更してください

```bash
sudo chmod -R 777 src/storage
```

## テスト環境（.env.testing）

本アプリでは、テスト環境用に `.env.testing` を使用します。

### テスト環境用設定ファイルの作成

```bash
cp .env.testing.example .env.testing
```

### アプリケーションキーの生成（テスト環境）

```bash
php artisan key:generate --env=testing
```

### Stripe 設定

本アプリでは Stripe を利用した購入処理を実装していますが、
Stripe の Secret Key はリポジトリには含めていません。
テスト環境で購入処理を確認する場合は各自で取得した Stripe のテスト用 Secret Key を
.env.testing に設定してください。
※ .env.testing.example にはダミー値のみを記載しています。

```text
STRIPE_SECRET=your_stripe_secret_key
```

## ログイン情報

本アプリでは、管理者専用の機能は実装していないため、
管理者ユーザーは存在しません。

一般ユーザーは、シーディング実行時に複数作成されます。
以下のいずれかのユーザーでログインして動作確認が可能です。

- メールアドレス：データベースの users テーブルに登録されている任意のメールアドレス
- パスワード：password

## 使用技術（実行環境）

| カテゴリ       | 技術                    | バージョン |
| -------------- | ----------------------- | ---------- |
| フレームワーク | Laravel                 | 8.83.8     |
| 言語           | PHP                     | 8.1.33     |
| Web サーバ     | Nginx                   | 1.21.1     |
| データベース   | MySQL                   | 8.0.26     |
| 管理ツール     | phpMyAdmin              | 最新       |
| コンテナ環境   | Docker / Docker Compose | 3.8        |

## 開発環境

| 項目       | 内容                   |
| ---------- | ---------------------- |
| Web アプリ | http://localhost       |
| phpMyAdmin | http://localhost:8080/ |

## URL 一覧

本アプリは「ログイン＝メール認証済み」を前提としています。
未認証ユーザーがログインした場合は、認証案内ページへリダイレクトされます。

### ゲスト利用（未ログインでも可）

| HTTP | URL             | ルート名     | 説明                     |
| ---- | --------------- | ------------ | ------------------------ |
| GET  | /               | items.index  | トップページ（商品一覧） |
| GET  | /item/{item_id} | items.detail | 商品詳細ページ           |
| GET  | /search         | items.search | 商品検索                 |

### ログイン必須（メール認証済みユーザー）

| HTTP  | URL                         | ルート名                | 説明               |
| ----- | --------------------------- | ----------------------- | ------------------ |
| POST  | /item/{item_id}/comment     | comment.store           | コメント投稿       |
| POST  | /item/{item_id}/like        | item.like               | いいね追加/解除    |
| GET   | /sell                       | sell.create             | 出品画面           |
| POST  | /sell                       | sell.store              | 出品処理           |
| GET   | /mypage                     | mypage.index            | マイページ         |
| GET   | /mypage/profile             | mypage.profile.edit     | プロフィール編集   |
| PATCH | /mypage/profile             | mypage.profile.update   | プロフィール更新   |
| GET   | /purchase/{item_id}         | purchase.form           | 購入確認画面       |
| POST  | /purchase/{item_id}         | purchase.execute        | 購入処理           |
| GET   | /purchase/success/{item_id} | purchase.success        | 購入完了画面       |
| GET   | /purchase/address/{item_id} | purchase.address.form   | 配送先住所変更画面 |
| PATCH | /purchase/address/{item_id} | purchase.address.update | 配送先住所更新     |

### メール認証フロー（未認証ユーザー向け）

| HTTP | URL                              | ルート名            | 説明           | 備考                            |
| ---- | -------------------------------- | ------------------- | -------------- | ------------------------------- |
| GET  | /verify-guide                    | verify.guide        | メール認証案内 | auth 必須（ログイン直後に誘導） |
| POST | /email/verification-notification | verification.send   | 認証メール再送 | auth + throttle                 |
| GET  | /email/verify/{id}/{hash}        | verification.verify | 認証完了処理   | signed + throttle               |

## ER 図

![ER図](src/public/erd.svg)

## テーブル仕様書

### users テーブル

| カラム名                  | 型              | PK  | NN  | UQ  |
| ------------------------- | --------------- | --- | --- | --- |
| id                        | bigint unsigned | ○   |     |     |
| name                      | varchar(255)    |     | ○   |     |
| email                     | varchar(255)    |     | ○   | ○   |
| email_verified_at         | timestamp       |     |     |     |
| password                  | varchar(255)    |     | ○   |     |
| two_factor_secret         | text            |     |     |     |
| two_factor_recovery_codes | text            |     |     |     |
| two_factor_confirmed_at   | timestamp       |     |     |     |
| remember_token            | varchar(100)    |     |     |     |
| created_at                | timestamp       |     |     |     |
| updated_at                | timestamp       |     |     |     |

### profiles テーブル

| カラム名      | 型              | PK  | NN  | UQ  | FK       | 補足                                  |
| ------------- | --------------- | --- | --- | --- | -------- | ------------------------------------- |
| id            | bigint unsigned | ○   |     |     |          |                                       |
| user_id       | bigint unsigned |     | ○   | ○   | users.id | 1 ユーザーにつき 1 プロフィールとする |
| username      | varchar(255)    |     | ○   |     |          |                                       |
| profile_image | varchar(255)    |     |     |     |          | 任意入力                              |
| postal_code   | varchar(8)      |     | ○   |     |          |                                       |
| address       | varchar(255)    |     | ○   |     |          |                                       |
| building      | varchar(255)    |     |     |     |          | 任意入力                              |
| created_at    | timestamp       |     |     |     |          |                                       |
| updated_at    | timestamp       |     |     |     |          |                                       |

### items テーブル

| カラム名     | 型              | PK  | NN  | FK            | 補足     |
| ------------ | --------------- | --- | --- | ------------- | -------- |
| id           | bigint unsigned | ○   |     |               |          |
| user_id      | bigint unsigned |     | ○   | users.id      |          |
| condition_id | bigint unsigned |     | ○   | conditions.id |          |
| name         | varchar(255)    |     | ○   |               |          |
| brand        | varchar(255)    |     |     |               | 任意入力 |
| description  | text            |     | ○   |               |          |
| price        | int             |     | ○   |               |          |
| image        | varchar(255)    |     | ○   |               |          |
| created_at   | timestamp       |     |     |               |          |
| updated_at   | timestamp       |     |     |               |          |

### conditions テーブル

| カラム名   | 型              | PK  | NN  |
| ---------- | --------------- | --- | --- |
| id         | bigint unsigned | ○   |     |
| name       | varchar(255)    |     | ○   |
| created_at | timestamp       |     |     |
| updated_at | timestamp       |     |     |

### categories テーブル

| カラム名   | 型              | PK  | NN  |
| ---------- | --------------- | --- | --- |
| id         | bigint unsigned | ○   |     |
| name       | varchar(255)    |     | ○   |
| created_at | timestamp       |     |     |
| updated_at | timestamp       |     |     |

### category_item 中間テーブル

| カラム名               | 型              | PK  | NN  | UQ  | FK            | 補足                           |
| ---------------------- | --------------- | --- | --- | --- | ------------- | ------------------------------ |
| id                     | bigint unsigned | ○   |     |     |               |                                |
| item_id                | bigint unsigned |     | ○   |     | items.id      |                                |
| category_id            | bigint unsigned |     | ○   |     | categories.id |                                |
| created_at             | timestamp       |     |     |     |               |                                |
| updated_at             | timestamp       |     |     |     |               |                                |
| (item_id, category_id) |                 |     |     | ○   |               | 複合 UQ, DB レベルで重複を防止 |

### likes テーブル

| カラム名           | 型              | PK  | NN  | UQ  | FK       | 補足                                                                              |
| ------------------ | --------------- | --- | --- | --- | -------- | --------------------------------------------------------------------------------- |
| id                 | bigint unsigned | ○   |     |     |          |                                                                                   |
| user_id            | bigint unsigned |     | ○   |     | users.id |                                                                                   |
| item_id            | bigint unsigned |     | ○   |     | items.id |                                                                                   |
| created_at         | timestamp       |     |     |     |          |                                                                                   |
| updated_at         | timestamp       |     |     |     |          |                                                                                   |
| (user_id, item_id) |                 |     |     | ○   |          | 複合 UQ, DB レベルで重複を防止 (1 ユーザーは同じ 1 商品に 1 回しかいいねできない) |

### comments テーブル

| カラム名   | 型              | PK  | NN  | FK       |
| ---------- | --------------- | --- | --- | -------- |
| id         | bigint unsigned | ○   |     |          |
| user_id    | bigint unsigned |     | ○   | users.id |
| item_id    | bigint unsigned |     | ○   | items.id |
| comment    | text            |     | ○   |          |
| created_at | timestamp       |     |     |          |
| updated_at | timestamp       |     |     |          |

### purchases テーブル

| カラム名       | 型                      | PK  | NN  | UQ  | FK       | 補足                                  |
| -------------- | ----------------------- | --- | --- | --- | -------- | ------------------------------------- |
| id             | bigint unsigned         | ○   | ○   |     |          |                                       |
| user_id        | bigint unsigned         |     | ○   |     | users.id |                                       |
| item_id        | bigint unsigned         |     | ○   | ○   | items.id | 1 商品は 1 回しか売れない             |
| payment_method | enum('konbini', 'card') |     | ○   |     |          | konbini=コンビニ払い, card=カード払い |
| postal_code    | varchar(8)              |     | ○   |     |          |                                       |
| address        | varchar(255)            |     | ○   |     |          |                                       |
| building       | varchar(255)            |     |     |     |          | 任意入力                              |
| created_at     | timestamp               |     |     |     |          |                                       |
| updated_at     | timestamp               |     |     |     |          |                                       |

## モデルのリレーション一覧

### User.php

```php
    public function items(){return $this->hasMany(Item::class);}
    public function comments(){return $this->hasMany(Comment::class);}
    public function likes(){return $this->hasMany(Like::class);}
    public function purchases(){return $this->hasMany(Purchase::class);}
    public function profile(){return $this->hasOne(Profile::class);}
```

### Profile.php

```php
    public function user(){return $this->belongsTo(User::class);}
```

### Item.php

```php
    public function user(){return $this->belongsTo(User::class);}
    public function categories(){return $this->belongsToMany(Category::class, 'category_item');}
    public function condition(){return $this->belongsTo(Condition::class);}
    public function comments(){return $this->hasMany(Comment::class);}
    public function likes(){return $this->hasMany(Like::class);}
    public function purchase(){return $this->hasOne(Purchase::class);}
```

### Category.php

```php
    public function items(){return $this->belongsToMany(Item::class, 'category_item');}
```

### Condition.php

```php
    public function items(){return $this->hasMany(Item::class);}
```

### Like.php

```php
    public function user(){return $this->belongsTo(User::class);}
    public function item(){return $this->belongsTo(Item::class);}
```

### Comment.php

```php
    public function user(){return $this->belongsTo(User::class);}
    public function item(){return $this->belongsTo(Item::class);}
```

### Purchase.php

```php
    public function user(){return $this->belongsTo(User::class);}
    public function item(){return $this->belongsTo(Item::class);}
```

## 実装にかかる補足事項

### seed データの内容

・マスタデータ（カテゴリ、商品状態）を作成

・ユーザーおよびプロフィール  
 プロフィールが入力済みのユーザーと、未入力項目を含むユーザーの両方の状態を再現

・商品  
 ユーザーに紐付いた商品出品データを作成（商品とカテゴリが紐付いた状態）

・いいね  
 商品に対するいいねデータを作成

・コメント  
 ユーザーと商品に紐付いたコメントデータを作成

・購入  
 一部商品を購入済み状態（ purchase レコードと紐づけ）として作成
