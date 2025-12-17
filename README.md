# 実践学習ターム 模擬案件初級\_フリマアプリ

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

---

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

"The stream or file could not be opned"エラーが発生した場合は、ディレクトリ/ファイルの権限を変更してください

```bash
sudo chmod -R 777 src/staroge
```

---

## 開発環境

| 項目       | 内容                      |
| ---------- | ------------------------- |
| Web アプリ | http://localhost/products |
| phpMyAdmin | http://localhost:8080/    |

---

## URL 一覧

---

## 使用技術（実行環境）

| カテゴリ       | 技術                    | バージョン |
| -------------- | ----------------------- | ---------- |
| フレームワーク | Laravel                 | 8.83.8     |
| 言語           | PHP                     | 8.1.33     |
| Web サーバ     | Nginx                   | 1.21.1     |
| データベース   | MySQL                   | 8.0.26     |
| 管理ツール     | phpMyAdmin              | 最新       |
| コンテナ環境   | Docker / Docker Compose | 3.8        |

---

## ER 図

![ER図](src/public/erd.svg)

---

## テーブル仕様書

### users テーブル

| カラム名                  | 型              | PK  | NN  | UQ  | FK  | 説明                   |
| ------------------------- | --------------- | --- | --- | --- | --- | ---------------------- |
| id                        | bigint unsigned | ○   | ○   |     |     | ユーザー ID            |
| name                      | varchar(255)    |     | ○   |     |     | 氏名                   |
| email                     | varchar(255)    |     | ○   | ○   |     | メールアドレス         |
| email_verified_at         | timestamp       |     |     |     |     | メール認証日時         |
| password                  | varchar(255)    |     | ○   |     |     | パスワード（ハッシュ） |
| two_factor_secret         | text            |     |     |     |     | 二要素認証データ       |
| two_factor_recovery_codes | text            |     |     |     |     | 二要素認証リカバリー   |
| two_factor_confirmed_at   | timestamp       |     |     |     |     | 2FA 有効化日時         |
| remember_token            | varchar(100)    |     |     |     |     | ログイン保持トークン   |
| created_at                | timestamp       |     |     |     |     | 作成日時               |
| updated_at                | timestamp       |     |     |     |     | 更新日時               |

### profiles テーブル

| カラム名      | 型              | PK  | NN  | UQ  | FK  | 説明                 |
| ------------- | --------------- | --- | --- | --- | --- | -------------------- |
| id            | bigint unsigned | ○   |     |     |     |                      |
| user_id       | varchar(255)    |     | ○   | ○   | ○   | ユーザー ID          |
| username      | varchar(255)    |     | ○   |     |     | ユーザー名(アプリ内) |
| profile_image | varchar(255)    |     |     |     |     | プロフィール画像     |
| postal_code   | varchar(8)      |     | ○   |     |     | 郵便番号             |
| address       | varchar(255)    |     | ○   |     |     | 住所                 |
| building      | varchar(255)    |     |     |     |     | 建物名               |
| created_at    | timestamp       |     |     |     |     | 作成日時             |
| updated_at    | timestamp       |     |     |     |     | 更新日時             |

### categories テーブル

| カラム名   | 型              | PK  | NN  | 説明        |
| ---------- | --------------- | --- | --- | ----------- |
| id         | bigint unsigned | ○   | ○   | カテゴリ ID |
| name       | varchar(255)    |     | ○   | カテゴリ名  |
| created_at | timestamp       |     |     | 作成日時    |
| updated_at | timestamp       |     |     | 更新日時    |

### conditions テーブル

| カラム名   | 型              | PK  | NN  | 説明             |
| ---------- | --------------- | --- | --- | ---------------- |
| id         | bigint unsigned | ○   | ○   | 状態 ID          |
| name       | varchar(255)    |     | ○   | コンディション名 |
| created_at | timestamp       |     |     | 作成日時         |
| updated_at | timestamp       |     |     | 更新日時         |

### items テーブル

| カラム名     | 型              | PK  | NN  | UQ  | FK  | 参照先        | 説明           |
| ------------ | --------------- | --- | --- | --- | --- | ------------- | -------------- |
| id           | bigint unsigned | ○   | ○   |     |     |               | 商品 ID        |
| user_id      | bigint unsigned |     | ○   |     | ○   | users.id      | 出品者         |
| category_id  | bigint unsigned |     | ○   |     | ○   | categories.id | カテゴリ       |
| condition_id | bigint unsigned |     | ○   |     | ○   | conditions.id | 商品状態       |
| name         | varchar(255)    |     | ○   |     |     |               | 商品名         |
| brand        | varchar(255)    |     |     |     |     |               | ブランド名     |
| description  | text            |     |     |     |     |               | 商品説明       |
| price        | int             |     | ○   |     |     |               | 販売価格       |
| image        | varchar(255)    |     | ○   |     |     |               | 画像ファイル名 |
| created_at   | timestamp       |     |     |     |     |               | 作成日時       |
| updated_at   | timestamp       |     |     |     |     |               | 更新日時       |

### comments テーブル

| カラム名   | 型              | PK  | NN  | FK  | 参照先   | 説明           |
| ---------- | --------------- | --- | --- | --- | -------- | -------------- |
| id         | bigint unsigned | ○   | ○   |     |          | コメント ID    |
| user_id    | bigint unsigned |     | ○   | ○   | users.id | コメント投稿者 |
| item_id    | bigint unsigned |     | ○   | ○   | items.id | 対象商品       |
| comment    | text            |     | ○   |     |          | コメント本文   |
| created_at | timestamp       |     |     |     |          | 作成日時       |
| updated_at | timestamp       |     |     |     |          | 更新日時       |

### likes テーブル

| カラム名   | 型              | PK  | NN  | UQ       | FK  | 参照先   | 説明               |
| ---------- | --------------- | --- | --- | -------- | --- | -------- | ------------------ |
| id         | bigint unsigned | ○   | ○   |          |     |          | いいね ID          |
| user_id    | bigint unsigned |     | ○   | ○ (複合) | ○   | users.id | いいねしたユーザー |
| item_id    | bigint unsigned |     | ○   | ○ (複合) | ○   | items.id | いいねされた商品   |
| created_at | timestamp       |     |     |          |     | 作成日時 |                    |
| updated_at | timestamp       |     |     |          |     | 更新日時 |                    |

### purchases テーブル

| カラム名    | 型              | PK  | NN  | UQ  | FK  | 参照先   | 説明                              |
| ----------- | --------------- | --- | --- | --- | --- | -------- | --------------------------------- |
| id          | bigint unsigned | ○   | ○   |     |     |          | 購入 ID                           |
| user_id     | bigint unsigned |     | ○   |     | ○   | users.id | 購入者                            |
| item_id     | bigint unsigned |     | ○   | ○   | ○   | items.id | 購入商品（1 商品は 1 回だけ購入） |
| postal_code | varchar(8)      |     | ○   |     |     |          | 郵便番号                          |
| address     | varchar(255)    |     | ○   |     |     |          | 住所                              |
| building    | varchar(255)    |     |     |     |     |          | 建物名                            |
| created_at  | timestamp       |     |     |     |     | 作成日時 |                                   |
| updated_at  | timestamp       |     |     |     |     | 更新日時 |                                   |

## モデルのリレーション一覧

### User.php

```php
public function items() {
    return $this->hasMany(Item::class);
}

public function comments() {
    return $this->hasMany(Comment::class);
}

public function likes() {
    return $this->hasMany(Like::class);
}

public function purchases() {
    return $this->hasMany(Purchase::class);
}
```

### Item.php

```php
public function user() {
    return $this->belongsTo(User::class);
}

public function category() {
    return $this->belongsTo(Category::class);
}

public function condition() {
    return $this->belongsTo(Condition::class);
}

public function comments() {
    return $this->hasMany(Comment::class);
}

public function likes() {
    return $this->hasMany(Like::class);
}

public function purchase() {
    return $this->hasOne(Purchase::class);
}
```

### Comment.php

```php
public function user() {
    return $this->belongsTo(User::class);
}

public function item() {
    return $this->belongsTo(Item::class);
}
```

### Like.php

```php
public function user() {
    return $this->belongsTo(User::class);
}

public function item() {
    return $this->belongsTo(Item::class);
}
```

### Purchase.php

```php
public function user() {
    return $this->belongsTo(User::class);
}

public function item() {
    return $this->belongsTo(Item::class);
}
```

## 実装にかかる補足事項

-
