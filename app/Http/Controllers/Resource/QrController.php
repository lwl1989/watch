<?php
namespace App\Http\Controllers\Resource;

use App\Http\Controllers\Controller;
use App\Library\Constant\Common;
use App\Library\Constant\Scheme;
use App\Library\Constant\Url;
use App\Models\Activity\Activities;
use App\Models\Activity\ActivityDownInfo;
use App\Models\Activity\ActivityDownLog;
use App\Models\Activity\ActivityNewYear;
use App\Models\RegisterUsers\UserProfile;
use App\Services\RegisterUsers\UsersService;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Picqer\Barcode\BarcodeGeneratorJPG;
use Picqer\Barcode\Exceptions\BarcodeException;

class QrController extends Controller
{
	/**
	 * 用戶二維碼
	 * @return array
	 */
	public function user() : array
	{
		$renderer = new ImageRenderer(
			new RendererStyle(400),
			new ImagickImageBackEnd()
		);
		$writer = new Writer($renderer);
		/* @var $user \App\Models\Model */
		$user = Auth::user();
		$fallBack = $url = env('SITE_URL');
		$encrypted = Crypt::encryptString(json_encode(['id'=>$user->getAttribute('id'),'time'=>time()]));
		$shortUrl = sprintf('%s/ul?action=/open/user/number&id=%s&realUrl=%s&fallUrl=%s', env('APP_URL'),
			$encrypted, urlencode($url), urlencode($fallBack));

		$qrCodeStream = $writer->writeString($shortUrl);
		return [
			'content'   =>  base64_encode($qrCodeStream)
		];
	}

	public function userIdNumber(): array
	{
		try{
			$number=UserProfile::query()->where('user_id',Auth::id())
				->first(['id_number']);
			if(empty($number)){
				$content = '';
			}else{
				$generator=new BarcodeGeneratorJPG();
				$content = base64_encode($generator->getBarcode($number['id_number'], $generator::TYPE_CODE_128, 2,60));
			}
		}catch (BarcodeException $exception){
			echo $exception->getMessage();
			$content='';
		}

		return [
			'content' => $content
		];
	}

	/**
	 * 商店二維碼
	 * @param $id
	 * @param Request $request
	 * @return array
	 */
	public function shop($id, Request $request) : array
	{
		$type = $request->get('type', 'show');
		$shopName = $request->get('shop_name', '').'_商品兌換QRCode.png';
		$path = 'shop/qr/exchange/'.$id.'.png';
		$exists = Storage::disk('local')->exists($path);

		if ($exists) {
			$qrCodeStream = Storage::disk('local')->get($path);
		} else {
			$renderer = new ImageRenderer(
				new RendererStyle(400),
				new ImagickImageBackEnd()
			);
			$writer = new Writer($renderer);

			$fallBack = $url = env('SITE_URL');
			$url = env('APP_URL') . '/shop/' . $id;

			$shortUrl = sprintf('%s/ul?action=/shop/exchange&sid=%s&realUrl=%s&fallUrl=%s', env('APP_URL'),
				$id, urlencode($url), urlencode($fallBack));
			$qrCodeStream = $writer->writeString($shortUrl);

			Storage::disk('local')->put($path, $qrCodeStream);
		}

		if ($type === 'down') {
			header("Content-type: application/octet-stream");
			header("Accept-Ranges: bytes");
			header("Accept-Length:". strlen($qrCodeStream));
			header("Content-Disposition: attachment; filename=". $shopName);
			exit($qrCodeStream);
		}

		return ['content' => base64_encode($qrCodeStream)];
	}

	/**
	 * 商品二維碼
	 * @param Request $request
	 * @param $id
	 * @throws \Exception
	 */
	public function goods(Request $request, $id)
	{
		$type = $request->get('type', 'show');
		$goodsName = $request->get('goods_name', '').'_商品資訊QRCode.png';

		$renderer = new ImageRenderer(
			new RendererStyle(300, 1),
			new ImagickImageBackEnd()
		);
		$writer = new Writer($renderer);

		$shortUrl = Scheme::getUnifyUrl(
			sprintf(Scheme::OPEN_GOODS_DETAIL_ACTION, $id),
			Url::getShareGoodsUrl($id)
		);
		$qrCodeStream = $writer->writeString($shortUrl);

		if ($type === 'down') {
			header("Content-type: application/octet-stream");
			header("Accept-Ranges: bytes");
			header("Accept-Length:". strlen($qrCodeStream));
			header("Content-Disposition: attachment; filename=". $goodsName);
			exit($qrCodeStream);
		}

		header('content-type: image/png');
		echo $qrCodeStream;
	}

	/**
	 * 活動二維碼
	 * @param $id
	 * @param Request $request
	 * @return array
	 */
	public function active($id, Request $request) : array
	{
		$activity = Activities::query()->find($id, ['is_live_type'])->toArray();
		if ($activity['is_live_type'] === 2) {
			$qr_path = env('APP_URL').'/images/img_download_qr.png';
			return [
				'content' => base64_encode(file_get_contents($qr_path))
			];
		}

		$type = $request->get('type', 'show');
		$name = '現場報到QR Code_'. $request->get('activity_name', ''). '.png';

		$path = 'active/qr/join/'.$id.'.png';
		$exists = Storage::disk('local')->exists($path);

		if($exists) {
			$qrCodeStream = Storage::disk('local')->get($path);
		}else {
			$renderer = new ImageRenderer(
				new RendererStyle(400),
				new ImagickImageBackEnd()
			);
			$writer = new Writer($renderer);
			$fallBack = $url = env('SITE_URL');
			$url = env('APP_URL') . '/active/' . $id;

			$shortUrl = sprintf('%s/ul?action=/active/join&aid=%s&realUrl=%s&fallUrl=%s', env('APP_URL'),
				$id, urlencode($url), urlencode($fallBack));

			$qrCodeStream = $writer->writeString($shortUrl);

			Storage::disk('local')->put($path, $qrCodeStream);
		}

//	    if ($type === 'down') {
//		    header("Content-type: application/octet-stream");
//		    header("Accept-Ranges: bytes");
//		    header("Accept-Length:". strlen($qrCodeStream));
//		    header("Content-Disposition: attachment; filename=". $name);
//		    exit($qrCodeStream);
//	    }

		return [
			'content'   =>  base64_encode($qrCodeStream)
		];
	}

	/**
	 * 新年活動二維碼 已通過腳本跑
	 * @param Request $request
	 * @return array
	 */
	public function newYearActive(Request $request) : array
	{
		$i = 1;
		$count = ActivityNewYear::query()->count(['id']);
		if ($count < 1200) {
			$i = $count + 1;
		}
		if ($count === 1200) {
			return [
				'content'   =>  '1200張qrcode生成完畢'
			];
		}

		for ($i; $i <= 1200; $i++) {
			$name = '新年活動二維碼QR Code_'. $i. '.png';
			$path = 'qr/join/newYear/'.$i.'.png';
			$exists = Storage::disk('public')->exists($path);

			if ($exists) {
				$qrCodeStream = Storage::disk('public')->get($path);
			} else {
				$renderer = new ImageRenderer(
					new RendererStyle(400),
					new ImagickImageBackEnd()
				);

				$writer = new Writer($renderer);
				$fallBack = $url = env('SITE_URL');
				$down_url = env('DOWNLOAD_URL');

				$shortUrl = sprintf('%s/ul?action=/active/join&aid=%s&realUrl=%s&fallUrl=%s', env('APP_URL'),
					'newYear2019_'. $i, urlencode($down_url), urlencode($fallBack));

				$qrCodeStream = $writer->writeString($shortUrl);

				Storage::disk('public')->put($path, $qrCodeStream);
			}

			//寫入庫
			$insert = [
				'start_time'    => date('2019-02-01 18:00:00'),
				'end_time'      => date('2019-02-02 18:00:00'),
			];

			ActivityNewYear::query()->insert($insert);
		}

		return [
			'content'   =>  '1200張qrcode生成完畢'
		];
	}

	/**
	 * 多張QR生成
	 * @param Request $request
	 * @return array
	 */
	public function multipleActive(Request $request) : array
	{
		set_time_limit(0);
		ini_set('memory_limit', '-1');

		$begin = 1;
		$end = 1;

		$id = $request->input('id', 0);

		$activities = Activities::query()
			->find($id, ['live_person_limit', 'name'])
			->toArray();
		$limit = $activities['live_person_limit'];
		$name = $activities['name'];

		if ($limit === 0) {
			return ['code' => -2];
		}

		$info = ActivityDownInfo::query()
			->where('activity_id', $id)
			->orderBy('sort', 'desc')
			->limit(1)
			->get(['sort'])
			->toArray();

		if (count($info) > 0) {//下載過
			$sort = $info[0]['sort'];

			if ($sort === $limit) {
				return ['code' => -1];
			}

			if ($sort < $limit) {
				$begin = $sort + 1;
				$end = $limit;
			}
		} else {
			$end = $limit;
		}

		//qr數量少  立即生成
		if ($end - $begin <= 50) {
            $renderer = new ImageRenderer(
                new RendererStyle(400),
                new ImagickImageBackEnd()
            );

            $writer = new Writer($renderer);
            $fallBack = $url = env('SITE_URL');
            $down_url = env('DOWNLOAD_URL');

            for ($begin; $begin <= $end; $begin++) {
                $shortUrl = sprintf('%s/ul?action=/active/join&aid=%s&realUrl=%s&fallUrl=%s', env('APP_URL'),
                    'multipleQR_'. $begin. '_'. $id, urlencode($down_url), urlencode($fallBack));
                $stream = $writer->writeString($shortUrl);
                $qrCodeStream[] = [
                    'key' => $begin,
                    'content' => base64_encode($stream)
                ];
            }

            return [
                'content' => $qrCodeStream ?? []
            ];
        } else {
		    //qr數量多 腳本跑 檢測服務器上是否生成了qr
            $path = 'qr/activity/autoProduceMultipleQr/'. $id. '/'. $name. '_現場報到QR碼_'. $end. '.jpg';
            $exist = Storage::disk('public')->exists($path);

            //未生成  返回提示
            if ($exist == false) {
                return ['code' => -3];
            }

            //已生成 判斷是增加還是新增 讀取圖片 返回流
            for ($begin; $begin <= $end; $begin++) {
                $path = 'qr/activity/autoProduceMultipleQr/'. $id. '/'. $name. '_現場報到QR碼_'. $begin. '.jpg';
                $stream = Storage::disk('public')->get($path);

                $qrCodeStream[] = [
                    'key' => $begin,
                    'content' => base64_encode($stream)
                ];
            }

            return [
                'content' => $qrCodeStream ?? []
            ];
        }
	}

	/**
	 * 縣民優惠二維碼
	 * @param Request $request
	 * @param $id
	 * @param string $name
	 * @param string $from
	 * @return array
	 */
	public function preferential(Request $request, $id, string $name='', string $from='api') : array
	{
		if ($from=='api') {
			$type = $request->get('type', 'show');
			$fileName = $request->get('preferential_name', '').'_優惠資訊QRCode.png';
		} else {
			$type = 'card';
			$fileName = $name.'_優惠資訊QRCode.png';
		}


		$path = 'preferential/qr/checkIn/'.$id.'.png';
		$exists = Storage::disk('local')->exists($path);

		if($exists) {
			$qrCodeStream = Storage::disk('local')->get($path);
		}else {
			$renderer = new ImageRenderer(
				new RendererStyle(400),
				new ImagickImageBackEnd()
			);
			$writer = new Writer($renderer);
			$fallBack = $url = env('SITE_URL');
			$url = env('APP_URL') . '/preferential/' . $id;

			$shortUrl = sprintf('%s/ul?action=/preferential/checkIn&aid=%s&realUrl=%s&fallUrl=%s', env('APP_URL'),
				$id, urlencode($url), urlencode($fallBack));

			$qrCodeStream = $writer->writeString($shortUrl);

			Storage::disk('local')->put($path, $qrCodeStream);
		}
		if ($type === 'down') {
			header("Content-type: application/octet-stream");
			header("Accept-Ranges: bytes");
			header("Accept-Length:". strlen($qrCodeStream));
			header("Content-Disposition: attachment; filename=". $fileName);
			exit($qrCodeStream);
		}else if($type ==='show'){
			header('content-type: image/png');
			echo $qrCodeStream;
		}
		return [
			'content'   =>  base64_encode($qrCodeStream)
		];
	}
}