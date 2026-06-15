# Full Page Password Protect

[English](README.md) | **日本語**

WordPress 標準のパスワード保護を拡張し、本文だけでなくページ全体を保護するプラグインです。

- **バージョン:** 1.0.0
- **必要な WordPress:** 5.8 以上
- **必要な PHP:** 7.4 以上
- **ライセンス:** [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html)

## 概要

WordPress の標準動作では、本文だけが非表示になり、タイトル・アイキャッチ画像・カスタムフィールド・テーマの出力など、ページの他の部分が表示されてしまうことがあります。

このプラグインは、正しいパスワードが入力されるまで、個別ページ全体をシンプルなパスワード入力画面に置き換えます。WordPress 標準のパスワードフォームとパスワード処理を使用し、独自のログイン機能・カスタム Cookie・ユーザーアカウント・パスワード保存は行いません。

また、アーカイブページ・検索結果・タクソノミー一覧・REST API レスポンスでの、保護コンテンツの意図しない露出を抑えるのにも役立ちます。

## 主な機能

- 個別ページ全体をパスワード入力画面に置き換える
- WordPress 標準のパスワードフォームとパスワード処理を使用
- パスワード入力前にタイトル・画像などのページ要素が表示されないよう支援
- パスワード画面に no-cache ヘッダーを送信
- パスワード画面に `noindex, nofollow` を付与
- REST API レスポンスでの保護コンテンツ露出を抑制
- 既定で保護投稿を公開一覧から除外
- タイトルのみ表示モードを任意で利用可能
- 選択した有効な投稿タイプに対応
- パスワード画面の既定メッセージを翻訳可能
- 第三者サービス・トラッキングスクリプト・外部アセットを読み込まない

## インストール

1. `full-page-password-protect` フォルダを `/wp-content/plugins/` にアップロードするか、WordPress のプラグイン画面からインストールします。
2. WordPress の **プラグイン** 画面で有効化します。
3. **設定 > Full Page Password** を開きます。
4. 対象の投稿タイプと一覧表示モードを確認します。

パスワード保護自体は、各投稿・固定ページの WordPress 標準 **公開範囲 > パスワード保護** から設定します。

## 設定

| 設定項目 | 説明 |
| --- | --- |
| **プラグインを有効化** | ページ全体のパスワード保護のオン・オフを切り替えます。 |
| **保護対象の投稿タイプ** | ページ全体のパスワード保護を適用する、有効化された投稿タイプを選択します。 |
| **一覧表示モード** | `exclude` は保護投稿を一覧クエリから除外します。`title_only` は一覧に残しつつ、抜粋・本文・アイキャッチ画像を非表示にします。 |
| **パスワード説明文** | パスワード画面のフォーム上に表示するメッセージです。 |

## よくある質問

**独自のパスワードシステムを使いますか？**  
いいえ。WordPress 標準の投稿パスワード機能を使用します。

**パスワードを保存しますか？**  
いいえ。パスワードは WordPress の通常の方法で管理されます。

**ブロックテーマでも動作しますか？**  
はい。クラシックテーマ・ブロックテーマの両方で動作します。

**REST API の出力も保護されますか？**  
はい。保護された投稿について、プラグイン設定に応じてコンテンツ関連フィールドが公開 REST レスポンスから隠されます。

**既存のパスワード保護投稿でも使えますか？**  
はい。WordPress 標準の公開範囲設定でパスワードを設定し、プラグインを有効化してください。

## プライバシー

このプラグインは個人データを収集・保存・送信しません。外部サービスの呼び出し、リモートアセットの読み込み、トラッキングスクリプトの追加も行いません。

## 開発

### プロジェクト構成

```text
full-page-password-protect/
├── assets/css/frontend.css
├── includes/
│   ├── class-fppp-archive.php
│   ├── class-fppp-plugin.php
│   ├── class-fppp-protector.php
│   ├── class-fppp-rest.php
│   └── class-fppp-settings.php
├── languages/
├── templates/password-form.php
├── full-page-password-protect.php
├── readme.txt
└── uninstall.php
```

### 翻訳

- テキストドメイン: `full-page-password-protect`
- 日本語翻訳: `languages/full-page-password-protect-ja.po`

`.po` ファイルを編集したあと、`.mo` ファイルをコンパイルするには:

```bash
msgfmt -o languages/full-page-password-protect-ja.mo languages/full-page-password-protect-ja.po
```

## リンク

- [製品ページ](https://sora-style.org/products/full-page-password-protect/)
- [WordPress.org プラグインページ](https://wordpress.org/plugins/full-page-password-protect/)
- [寄付](https://sora-style.org/donate/)
- [Sora Style](https://sora-style.org/)

## 変更履歴

### 1.0.0

- 初回リリース。
