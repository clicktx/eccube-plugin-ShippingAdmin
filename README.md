ShippingAdmin
=============
### EC-CUBE用 管理機能 出荷業務効率化プラグイン
EC-CUBE用のプラグインです。出荷時の業務を効率化するためのプラグインとなります。

# 主な機能
宅配業者の荷物管理番号が登録できるようになります。

## 新規受注（銀行振込）
- 銀行振込（payment_id:3）の場合は入金待ち（order_status:2）にオーダーステータスを変更する

## 【管理機能】受注管理
- 検索時の絞り込み条件を追加
    - 配送業者
    - 荷物追跡番号
    - 出荷日
- 検索で表示される結果の 注文番号に「注文情報表示」へのリンク

## 【管理機能】受注管理＞受注登録(編集含む)
- 「荷物追跡番号」を登録・編集出来る

## 【管理機能】受注管理＞対応状況管理（ステータス変更）
- ステータスを削除に変更する時は「削除確認」にチェックしないと削除できない
- リスト表示時に「荷物追跡番号」列が追加される
- 表示結果の行をクリックするとチェックボックスをチェック・解除できる
- Lightbox風のウィンドウで「荷物追跡番号」を画面遷移なしで登録・編集出来る
- ステータスを「発送済み」に変更時、荷物追跡番号が未登録の場合はエラーになる

### 【管理機能】ステータス変更による自動メール送信
ステータス変更するとメールを送信する。

- キャンセルに変更（メールテンプレートID: 3）
- 入金済みに変更（メールテンプレートID: 20）
- 発送済みに変更（メールテンプレートID: 50）


# 注意点
- ステータス変更（対応状況の変更）は「受注管理＞対応状況管理」から行う事
