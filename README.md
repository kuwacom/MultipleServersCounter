# MultipleServersCounter
複数サーバーのプレイヤー数をまとめるpmmpプラグイン  Sum the player count on multiple servers pmmp plugin.
## 英語対応化お知らせ
現在はまだ日本語のみのリリースですが英語にも対応予定です！
Currently it is only released in Japanese
but it will be available in English!
# 使い方
リリースよりリリースされている .phar をpluginsファイルに入れてください
そうすると plugin_data に config.yml が生成されるはずです
```yml
PluginPrefix: PlayerCounter #ログのプレフィックス
target_server:
  1:
    ip: be.kuwa.cf #取得先のサーバーのIP
    port: 19132 #取得先のサーバーのポート
  2:
    ip: be.kuwa.cf
    port: 20000
TimeOut: 10 #接続がタイムアウトするまでの時間
debug_log: false #ログを出すかどうか
```
上記のように記入してもらうことで
プラグインが動作するようになります！
# 対応サーバー
BDS BDSX PMMP Nukiit WaterDogProxy Geyser
クライアントからステータスを要求するパケットを送信してステータスを受信しているので
マインクラフトクライアントでmotdとうのステータスを取得できるサーバーであればほとんど動作するはずです！
