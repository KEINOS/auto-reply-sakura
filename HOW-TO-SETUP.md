# 設置の仕方

## 前提条件

- **サーバーの PHP バージョンがで v5 系（v5.6.40 以上）である**。
- メールの転送設定ファイル（`.mailfilter`）を編集した独自のメール転送機能の実装は、**「さくらインターネット」の[動作保証外である](https://help.sakura.ad.jp/206206501/)ことを理解している**。
- 「さくらインターネットに SSH 接続して作業する」と言われて意味がわかる。
- 「`composer` でパッケージをインストール」「`git` で `clone` する」と言われて意味がわかる。
- [OSS](https://ja.wikipedia.org/wiki/%E3%82%AA%E3%83%BC%E3%83%97%E3%83%B3%E3%82%BD%E3%83%BC%E3%82%B9%E3%82%BD%E3%83%95%E3%83%88%E3%82%A6%E3%82%A7%E3%82%A2) のメリット・デメリットを理解している。
- [MIT ライセンス](https://github.com/KEINOS/auto-reply-sakura/blob/master/LICENSE)とは何かを理解している。

## 主な流れ

1. このリポジトリを clone する
2. 依存パッケージをインストールする（`composer instal --no-dev`）
3. 設定ファイルの設定
4. 返信テンプレートの設定
5. メールボックスの転送設定

## 基本的な仕組み

- 自動転送を使いたいユーザーのメールボックスのルートに `.mailfilter` ファイルを設置して、転送先にスクリプトを指定すると、届いたメールのソースがスクリプトの標準入力に渡されます。
- 届いたメールのソースがメインスクリプトの `./auto-reply.php` の標準入力に渡されると、そのメール送信者に自動返信されます。
- 自動返信のタイミングは `./config/config.json` の設定ファイルで変更できます。
- 自動返信される本文は `./template/reply_body.utf8.txt` に記載します。

### `.mailfilter` ファイルに記述する内容

`MailBox` ディレクトリの各ユーザーの `.mailfilter` に `cc "| <スクリプトの絶対パス>"` を記載すると、届いたメールがスクリプトの標準入力に渡されます。

```bash
cc "| /home/<account name>/<path>/<to>/auto-reply.php"
```

```bash
% # 設置先と記述内容の例
% #   さくらの契約アカウントが keinos、ユーザー test 宛てに届いたメールで自動返信したい場合
% pwd
/home/keinos/MailBox/test

% cat .mailfilter
cc "| /home/keinos/auto-reply-sakura/auto-reply.php"
```

## 具体的な設置手順

### スクリプトの設置

1. SSH で、さくらのサーバーに入り **`/www` より上の階層**でこのリポジトリを `clone` する。リポジトリ名が長いので「`auto-reply`」として `clone` する。

    ```bash
    % # ルートに移動
    cd ~
    % # リポジトリを auto-reply として clone
    git clone https://github.com/KEINOS/auto-reply-sakura.git auto-reply
    % # clone したリポジトリに移動
    cd auto-reply
    ```

2. このリポジトリに必要な依存パッケージのインストール（要 `composer` コマンド）

    ```bash
    composer install --no-dev
    ```

### 設定ファイルの設置と編集

3. 以下の設定ファイルをコピー＆リネームして編集する。（必須以外の不要な要素は削除可能）
    - `cp ./config/config.json-sample ./config/config.json`
      - 必須要素:
          - 有効な JSON 形式であること。（`cat ./config.json | jq .` で確認）
          - `weekday_to_reply`: 配列。自動返信させたい曜日の指定。
          - `mail_title_to_reply`: 文字列。
          - `from->email`: 文字列。

4. 以下の自動返信の本文定型ファイルをコピー＆リネームして編集する。
    - `cp ./template/reply_body.utf8.txt.sample ./template/reply_body.utf8.txt`
      - 必須条件: 1文字以上の UTF-8 テキスト（BOM なし）

5. 自動転送設定したいユーザーのメールボックスのルートに `.mailfilter` ファイルを（なければ）設置し、転送文を追記する。（以下は `test` ユーザーの場合の例）

    ```bash
    % # test ユーザーのメールボックスのルートに移動
    cd ~/MailBox/test

    % # .mailfilter が存在するか確認
    % ls -la
    total 24
    drwx------   4 keinos  users  512  4月 10 11:51 .
    drwx------  23 keinos  users  512  4月  1 16:06 ..
    drwx------   2 keinos  users  512  3月 11 15:09 .class
    -rw-r--r--   1 keinos  users    0  4月 10 11:51 .comment
    -rw-------   1 keinos  users   24  4月 10 11:51 .mailfilter
    -rw-r--r--   1 keinos  users   64  3月 11 15:09 .mailpassword
    -rw-r--r--   1 keinos  users    0  4月 10 11:51 .whitelist
    drwx------   9 keinos  users  512  4月 10 11:51 maildir

    % # .mailfilter を編集
    （略）

    % # .mailfilter の中身確認（以下は携帯にも転送している例）
    % cat .mailfilter
    cc "!my_mobile_phone@docomade.jp"
    cc "| /home/keinos/auto-reply/auto-reply.php"

    % # .mailfilter のアクセス権を 600 に変更しておく
    % chmod 0600 ./.mailfilter
    ```

### スクリプトの更新

スクリプトのアップデートがあった場合は `git pull` で更新します。また、`composer` のオートローダーの更新も忘れないようにします。

```shellscript
% cd ~/auto-reply
% # スクリプトの更新
% git pull origin
...
% # パッケージの更新
% compose update --no-dev
...
% # オートローダーの更新
% composer dump-autoload
...
```

## コラボレーション

### 不具合報告／要望

- すでに同じ内容のものがないか確認後、 [Issues](https://github.com/KEINOS/auto-reply-sakura/issues) で `issue` を立ててください。
- 必ず対応／返信があるとは限りません。特に「動きません」だけで情報が不足しているような場合はスルーされる可能性が高いです。

### `Pull Request`

Issue 対応／不具合修正／リファクタリング／新機能追加があれば遠慮なく `PR` ください。（詳しくは下記参照）

- [CONTRIBUTE.md](./CONTRIBUTE.md)
