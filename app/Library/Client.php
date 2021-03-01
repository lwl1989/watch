<?php
namespace App\Library;

class Client
{
	/**
	 * 發送消息
	 * @param $args
	 * @return string
	 */
	/*public static function post($args) : string
	{
		$url = 'http://13.125.57.182';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json','Content-Length:'.strlen($args)]);
		$output = curl_exec($ch);
		static::throwError($ch);
		curl_close($ch);

		return $output;
	}*/

	/**
	 * 刪除定時任務
	 * @param $task_id
	 * @return string
	 * @throws \Exception
	 */
	public static function delete($task_id) : string
	{
		$delete = json_encode(['uuid' => $task_id]);

		$ch = curl_init();
		curl_setopt ( $ch, CURLOPT_URL, env('FCM_URL'));//'http://13.125.57.182'
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, 'DELETE' );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $delete);
		curl_setopt ( $ch, CURLOPT_FRESH_CONNECT, false );
		curl_setopt ( $ch, CURLOPT_HEADER, true );
		curl_setopt ( $ch, CURLOPT_TIMEOUT, 5184000 );
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 120 );
		curl_setopt ( $ch, CURLOPT_FILETIME, true );
		curl_setopt ( $ch, CURLOPT_NOSIGNAL, true );
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json','Content-Length:'.strlen($delete)]);

		$output = curl_exec($ch);
		static::throwError($ch);
		curl_close($ch);

		return $output;
	}

	/**
	 * 拋出curl 錯誤
	 * @param $ch
	 * @throws \Exception
	 */
	private static function throwError($ch)
	{
		$errNumber = curl_errno($ch);
		$errMessage = curl_error($ch);

		if ( $errNumber > 0 && $errMessage != '') {
			curl_close($ch);
			throw new \Exception($errMessage, $errNumber);
		}
	}
}