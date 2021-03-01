<?php

namespace App\Http\Middleware;

use Closure;

class JsonInput
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $post = JsonInput::getInputRawData();
        foreach ($post as $key=>$value) {
            $request->offsetSet($key, $value);
        }
        return $next($request);
    }

    /**
     * 當頭沒有定義application的時候
     * @return array
     */
    public static function getInputRawData() : array
    {
        $rs = fopen('php://input', 'r');
        $input = stream_get_contents($rs);
        $data = [];
        if(!empty($input)) {
            $data = json_decode($input, true);
            if (!is_array($data)) {
                parse_str($input, $data);
            }
            fclose($rs);
        }
        return $data;
    }
}
