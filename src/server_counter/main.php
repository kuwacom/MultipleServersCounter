<?php
namespace server_counter;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\event\Listener;
use pocketmine\Server;
use pocketmine\event\server\QueryRegenerateEvent;
use pocketmine\utils\TextFormat as color;

class main extends PluginBase implements Listener{
	public function onEnable(){
		$this->getLogger()->info(color::GREEN.'PlayerCounter > 新規起動時は config.yml が生成されます！');
		$this->getLogger()->info(color::GREEN.'PlayerCounter > ステータス取得先のサーバーIP Portを設定してください！');
		$config = new Config($this->getDataFolder() . "config.yml", Config::YAML,[
			'PluginPrefix' => 'PlayerCounter',
			'target_server' => [
				1 => [
					'ip' => 'be.kuwa.cf',
					'port' => 19132
				],
				[
					'ip' => '0.0.0.0',
					'port' => 19132
				]
				],
			'TimeOut' => 10,
			'debug_log' => false
			//pmmpメモ YMAL生成について
			//ここの配列は target_serverに 1 を作りその中に ip を作り 0.0.0.0 を書き込み
			//さらにportを作り 19132 を割り当てるという配列
			//1 => ○○ と書いた後は , で区切って続ければ勝手に連番してくれる
		]);
		$this->getServer()->getPluginManager()->registerEvents($this, $this);//QueryRegenerateEvent では必要
	}
	public function getStatus($ip, $port, $timeout){
		$data = b"\x01\x00\x00\x00\x00L\x00\x00\x00\x00\xff\xff\x00\xfe\xfe\xfe\xfe\xfd\xfd\xfd\xfd\x124Vx\x00\x00\x00\x00\x00\x00\x00\x00";

		$sndtimeo = $timeout; // 接続タイムアウト
		$rcvtimeo = $timeout; // 受信タイムアウト
		$error = 'None';
		// ソケット作成
		$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP );
		// オプション設定
		socket_set_option( $sock, SOL_SOCKET, SO_SNDTIMEO, array("sec"=>$sndtimeo,"usec"=>0) );
		socket_set_option( $sock, SOL_SOCKET, SO_RCVTIMEO, array("sec"=>$rcvtimeo,"usec"=>0) );
		
		// 接続
		try{
			socket_connect($sock, $ip, $port);//後々エラーで止まらないようにする
		} catch(Exception $e) {
			$error = 'error';
		}

		$error_code = socket_last_error($sock);
		// エラーコード一覧 https://www.php.net/manual/ja/function.socket-last-error.php#95160
		if($error_code == 0){
			$error = 'None';
		}elseif($error_code == 110){
			$error = 'timeout';
		}elseif($error_code == 11001){
			$error = 'nothost';
		}else{
			$error = socket_strerror($error_code);
		}
		
		if($error == "None"){
			// 送受信
			$start_time = microtime(true);
			socket_write($sock, $data);
			$end_time = microtime(true);
			$status = socket_read( $sock, 10240 );
			$rec_time = microtime(true);
		
			$connect_time = ($end_time - $start_time) * 1000;
			$rec_time = ($rec_time - $end_time) * 1000;
			$total_time = $connect_time + $rec_time;
			// 一応処理時間測定
			$error_code = socket_last_error($sock);
			// エラーコード一覧 https://www.php.net/manual/ja/function.socket-last-error.php#95160
			if($error_code == 0){
				$error = 'None';
			}elseif($error_code == 110){
				$error = 'timeout';
			}else{
				$error = socket_strerror($error_code);
				// echo $error;
			}
		}
		
		if($error == 'None'){
			$status = strstr($status, 'MCPE');
			$status = explode(';', $status);

			return $status[4];
		}else{
		        return 'error';
	        }
	}

    public function query(QueryRegenerateEvent $event){
		$config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
		$prefix = $config->get('PluginPrefix');
		$timeout = $config->get('TimeOut');
		$num = 0;
		$player_count = sizeof($this->getServer()->getOnlinePlayers());// サーバー自体の接続人数
		foreach ($config->get('target_server') as $i => $nalue) {
			$num = $num + 1;
			if ($config->get('debug_log') == 'true') {
			$this->getServer()->getLogger()->info(color::BLUE.'ステータス取得中...');
			}
			$count = main::getStatus($config->get('target_server')[$num]['ip'], $config->get('target_server')[$num]['port'],$timeout);
			if ($config->get('debug_log') == 'true') {
				$this->getServer()->getLogger()->info(color::YELLOW.$prefix.' > '.
					'ip:'.
					$config->get('target_server')[$num]['ip'].
					' port:'.
					$config->get('target_server')[$num]['port'].
					' 接続人数:'.
					$count);
				$this->getServer()->getLogger()->info(color::GREEN.$prefix.' > '.'ステータスを取得しました！');
			}
			$player_count = $player_count + $count;// setting.yml内のサーバーの接続人数を足す
		}
		$event->setPlayerCount($player_count);//接続人数変更
		if ($config->get('debug_log') == 'true') {
			$this->getServer()->getLogger()->info(color::GREEN.$prefix.' > '."サーバーの接続人数を更新しました！");
		}	
    }
}
