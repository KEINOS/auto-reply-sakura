[![](https://travis-ci.org/KEINOS/auto-reply-sakura.svg?branch=master)](https://travis-ci.org/KEINOS/auto-reply-sakura "Build Status")
[![](https://img.shields.io/badge/PHP-5.6.xx-blue)](https://github.com/KEINOS/auto-reply-sakura/blob/master/docker/Dockerfile#L3  "Supported PHP Version")

# auto-reply-sakura

このリポジトリおよび `auto-reply.php` のスクリプトは、**「さくらインターネット」のメールの自動返信機能を拡張する PHP スクリプトです**。指定された定休日および時間にメールが届いた場合に、定型文を自動返信します。

- 「さくらインターネット」のベーシック・プラン + PHP v5.6.40 でメールの自動返信をカスタム（任意のタイミングで返信）したい場合に利用ください。

## 自動返信の本体スクリプトについて

- **`./auto-reply.php`: 自動返信を実行する PHP スクリプトです。**
  - PHP 5.6 でのみ動作確認しています。
  - 受信したメール本文を標準入力（STDIN）から受け取ると、送信者に適宜自動返信を行います。
  - 自動返信の条件などは次項の設定ファイルで行います。

## 設定ファイルについて

- **`./config/config.json`: 自動返信の各種設定が記載されています。**
  - UTF-8, BOM なしのテキストで JSON 形式で記載する必要があります。
  - 設定可能な内容
    - 自動返信を行う曜日（オプションで返信の開始および終了時間の指定も可能）
    - 返信メールのタイトル
    - 自動返信の送信者メールアドレス指定

## 自動返信の本文テンプレート・ファイルについて

- **`./template/reply_body.utf8.txt`: 自動返信時のメール本文が記載されています。**
  - UTF-8, BOM なしのテキストで記載する必要があります。
  - 文頭・文末の空行（改行だけの行）は削除され、文頭に空行が自動で１行追加されます。

## その他

### スクリプトの設置の仕方

- [HOW-TO-SETUP.md](https://github.com/KEINOS/auto-reply-sakura/blob/master/HOW-TO-SETUP.md) をご覧ください。

### コントリビューション（不具合報告、改善要望など）

- [CONTRIBUTE.md](https://github.com/KEINOS/auto-reply-sakura/blob/master/CONTRIBUTE.md) をご覧ください。

### リポジトリ情報

- [原本／リポジトリ](https://github.com/KEINOS/auto-reply-sakura/) @ GitHub
- 著者: [KEINOS](https://github.com/KEINOS) と[コントリビューター](https://github.com/KEINOS/auto-reply-sakura/graphs/contributors)
- [MIT ライセンス](https://github.com/KEINOS/auto-reply-sakura/blob/master/LICENSE)
