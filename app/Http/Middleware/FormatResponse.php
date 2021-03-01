<?php
namespace App\Http\Middleware;

use App\Library\ArrayParse;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FormatResponse
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
        /**@var $response Response**/
        $response = $next($request);

        $res = new JsonResponse();
        $res->setEncodingOptions(JSON_UNESCAPED_SLASHES);

        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);

            if ($response->getStatusCode() != 200) {
                if (!isset($data['code'])) {
                    $res->setData($this->_getData($request, ['code' => $response->getStatusCode(), 'response' => $data]));
                    return $res;
                }
            }

            if (!isset($data['code'])) {
                $res->setData($this->_getData($request, ['code' => 0, 'response' => $data]));
                return $res;
            }

            $res->setData($this->_getData($request, $data));
        } elseif (is_array($response)) {
            if (!isset($response['code'])) {
                $response = ['code' => 0, 'response' => $response];
            }

            $res->setData($this->_getData($request, $response));
        } elseif ($response instanceof BinaryFileResponse) {
            return $response;
        } elseif ($response instanceof \Symfony\Component\HttpFoundation\Response) {
            $res->setData($this->_getData($request, ['code' => 0, 'response' => $response->getContent()]));
        } else {
            $res->setData($this->_getData($request, ['code' => 0, 'response' => $response]));
        }

        return $res;
    }

    private function _getData(Request $request,array $response) : array
    {
        if(strpos($request->getRequestUri(), 'api/') !== false) {
            $response = ArrayParse::replaceTime($response);
            $response = ArrayParse::strval($response);
        }
        return $response;
    }
}
