<?php
namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function index(){
        return [];
    }

    /**
     * @api               {get} /api/webhook 测试webhook
     * @apiGroup          webhook
     * @apiName           webhook
     * @apiVersion        1.0.0
     *
     * @apiSuccessExample Success-Response
     * []
     *
     */
    public function webHook() : array {

        $secret = env('HOOK_GIT','');

        $signature = $_SERVER['HTTP_X_HUB_SIGNATURE'];

        if ($signature) {
            $hash = "sha1=".hash_hmac('sha1', file_get_contents("php://input"), $secret);
            if (strcmp($signature, $hash) == 0) {
                $path = env('BASH_PATH', '/var/www/html');
                echo shell_exec('cd '.$path.' && git pull && apidoc -i app/Http/Controllers -o public/api');
                exit();
            }
        }


            return [];
    }
}
