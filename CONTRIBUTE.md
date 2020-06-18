# CONTRIBUTE

不具合修正／リファクタリング／新機能追加があれば遠慮なく `Pull Request` ください。

## ローカルでのデバッグおよび開発の仕方

## 前提条件

### 開発の必須条件

- `git` コマンドの操作の基本がわかる。
  - コードの変更、変更内容の取り消し（リカバリ） 、本家のリポジトリで発生した変更の反映などに必要です。
  - [Git](https://ja.wikipedia.org/wiki/Git) とは @ Wikipedia
  - [Git 日本語マニュアル](https://git-scm.com/book/ja/v2/%E4%BD%BF%E3%81%84%E5%A7%8B%E3%82%81%E3%82%8B-%E3%83%90%E3%83%BC%E3%82%B8%E3%83%A7%E3%83%B3%E7%AE%A1%E7%90%86%E3%81%AB%E9%96%A2%E3%81%97%E3%81%A6) @ 公式
- `composer` コマンドの基本がわかる。
  - 後述の PHPUnit のインストールおよびテストの実行に必要です。
  - [Composer](https://ja.wikipedia.org/wiki/Composer) とは @ Wikipedia
  - [Composer 英語マニュアル](https://getcomposer.org/doc/) @ 公式
- `phpunit` を使ったことがある。
  - このリポジトリは PHPUnit コマンドにより[単体テスト](https://ja.wikipedia.org/wiki/%E5%8D%98%E4%BD%93%E3%83%86%E3%82%B9%E3%83%88)を行なっています。このテストをパスしないと本家のリポジトリにはマージ（変更が適用）されません。
  - [PHPUnit](https://ja.wikipedia.org/wiki/PHPUnit) とは @ Wikipedia
  - [PHPUnit 日本語マニュアル](https://phpunit.readthedocs.io/ja/latest/) @ 公式
- ローカルに PHP v5.6 の実行環境がある（推奨 PHP 5.6.40）
  - `docker` コマンド、および `docker-compose` コマンドがインストールされている場合は、仮想環境を用意しているので、ローカルに PHP 5 をインストールする必要はありません。

### 推奨条件（オプショナル）

- `Docker` および `docker-compose` コマンドが使える。
  - ユニットテストの実行を `composer` コマンドを通して実行できるようにしています。Docker および docker-compose が使える状態で以下のコマンドを叩くと、ダミーの SMTP サーバーを起動し、PHP 5.6 の環境でログインできます。

    ```shellsession
    $ # コンテナ起動
    $ composer docker-dev
    ...(略)
    /app # # PHP5 のコンテナ内操作
    /app # php -v
    PHP 5.6.40 (cli) (built: Jan 31 2019 01:25:07)
    Copyright (c) 1997-2016 The PHP Group
    Zend Engine v2.6.0, Copyright (c) 1998-2016 Zend Technologies
    /app #
    /app # # テストの実行
    /app # composer test
    ...(略)
    OK (120 tests, 139 assertions)
    All tests passed.
    ```
  -
- GitHub のアカウントを持っている。
  - ローカルで行なった変更（不具合修正や機能拡張など）を本家のリポジトリに反映依頼（プルリクエスト、PR）するのに必要です。
  - [GitHub](https://ja.wikipedia.org/wiki/GitHub) @ Wikipedia
  - [GitHub トップページ](https://github.com/) @ GitHub


## 仕様

このリポジトリは PHPUnit によるテスト実行して、各種テストをパスしたら OK としています。

そのため、機能追加および Issue 対応にはテストを作成してから実装してください。（詳しくは下記「テスト駆動型について」参照）

## 作業前の準備

1. [このリポジトリ](https://github.com/KEINOS/auto-reply-sakura)を自分のリポジトリに `Fork` する。
2. `Fork` したリポジトリをローカルに `clone` する。

以下 `Fork` 元のリポジトリを `upstream`、`Fork` 先のリポジトリを `origin`、`clone` したローカルのリポジトリを `local` とします。

## 基本的な作業の流れ

手をつけたい Issue がある場合は、`origin` の作業ブランチを `draft` で `upsteam` に `PR` してしまってから作業してください。これは同じ作業のバッティングが発生しないようにするためです。

1. `local` で作業用のブランチを作成して `origin` に `push` する。ブランチ名は内容がわかりやすいものにする。
2. `origin` の作業ブランチを `upstream` の `master` ブランチに [Draft](https://github.blog/jp/2019-02-19-introducing-draft-pull-requests/) で `pull request` する。
3. `local` で作業を開始します。
4. コミットして `origin` に `push` します。（PR した Draft にも反映されます）
5. 「もういいでしょう」という状態になったら、PR 先で `draft` を外し、その旨を PR にコメントしてください。他のコントリビュータのレビューが行われます。
6. レビューで指摘があった場合は、修正＆ `push` を行います。
7. 管理者の判断、もしくは `LGTM` のコメント or `approved` が 2 件以上付くと `master` にマージされます。

## テスト駆動型について

このリポジトリは、ゆるい[テスト駆動型開発](https://ja.wikipedia.org/wiki/%E3%83%86%E3%82%B9%E3%83%88%E9%A7%86%E5%8B%95%E9%96%8B%E7%99%BA)を行なっています。つまり、先にテストを作成し、テストが失敗することを確認後、テストをパスするように実装していく流れです。

1. 新規機能（関数やメソッド）の追加や Issue（不具合など）対策で、期待する動作を確認するテストを書く。
2. 実装／対策前に**テストが失敗することを確認**する。
    - 新規関数やメソッドを追加する場合は、ダミーの値を返すだけの空の関数／メソッドを作る。
    - Issue 対応の場合は、現象が再現するテストを書く。再現した場合は `False` で失敗するようにする。
3. 作業前のコミットを行う。
4. テストを通るように実装を行う。（必要ならコミットする）
5. 実装が終わったら、作業内容をコミットして作業ブランチに `push` する。
