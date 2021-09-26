# MultipleServersCounter
Sum the player count on multiple servers pmmp plugin.

複数サーバーのプレイヤー数をまとめるpmmpプラグイン  
# ?How to use?
リリースよりリリースされている .phar をpluginsファイルに入れてください
そうすると plugin_data に config.yml が生成されるはずです
```yml
PluginPrefix: PlayerCounter #Log prefix ログのプレフィックス
target_server:
  1:
    ip: be.kuwa.cf #target server IP 取得先のサーバーのIP
    port: 19132 #target server port 取得先のサーバーのポート
  2:
    ip: be.kuwa.cf
    port: 20000
TimeOut: 10 #Time to time out connection 接続がタイムアウトするまでの時間
debug_log: false #Whether to log ログを出すかどうか
```
By having them fill in as above
The plugin will work!
上記のように記入してもらうことで
プラグインが動作するようになります！
# Supported servers
BDS BDSX PMMP Nukiit WaterDogProxy Geyser

Since the client sends a packet requesting the status and receives the status
Most servers that can get the status of motd with the Minecraft client should work!

クライアントからステータスを要求するパケットを送信してステータスを受信しているので
マインクラフトクライアントでmotdとうのステータスを取得できるサーバーであればほとんど動作するはずです！
