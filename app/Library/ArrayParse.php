<?php

namespace App\Library;

use App\Exceptions\ErrorConstant;
use Illuminate\Support\Str;
use Illuminate\Validation\Factory;
use Illuminate\Validation\ValidationException;

class ArrayParse
{
    /**
     * 從一個數組內copy出想要的key
     * @param array $keys
     * @param array $sourceArray
     * @return array
     */
    public static function arrayCopy(array $keys, array $sourceArray): array
    {
        $arr = [];
        foreach ($keys as $key) {
            if ($sourceArray !== null && array_key_exists($key, $sourceArray)) {
                $arr[$key] = '';

                if (!is_null($sourceArray[$key])) {
                    $arr[$key] = $sourceArray[$key];
                }
            }
        }
        return $arr;
    }

    /**
     * 對比參數
     * @param array $params
     * @param array $diffArray
     * @return array
     * @throws \Exception
     */
    public static function checkParamsArray(array $params, array $diffArray = []): array
    {
        if (empty($diffArray)) {
            $diffArray = $_POST;
        }
        $diff = array_diff($params, array_keys($diffArray));
        if (count($diff) > 0) {
            throw new \Exception('params lost ' . implode(',', $diff), ErrorConstant::PARAMS_LOST);
        }
        $response = [];
        foreach ($params as $param) {
            if ($diffArray[$param] === null) {
                $diffArray[$param] = '';
            }
            $response[$param] = $diffArray[$param];
        }
        if (isset($response['mobile'])) {
            $response['mobile'] = ltrim($response['mobile'], '0 ');
        }
        return $response;
    }

    /**
     * 移除所有包含時間的字段
     * @param array $params
     * @param $timeField
     * @return array
     */
    public static function diffTime(array $params, string $timeField = '_time'): array
    {
        $arr = [];
        foreach ($params as $key => $value) {
            if (strpos($key, $timeField) === false) {
                $arr[$key] = $value;
            }
        }
        return $arr;
    }

    /**
     * 獲取所有時間字段
     * @param array $params
     * @param string $timeField
     * @return array
     */
    public static function getTime(array $params, string $timeField = '_time'): array
    {
        $arr = [];
        foreach ($params as $key => $value) {
            if (strpos($key, $timeField) !== false) {
                $arr[$key] = $value;
            }
        }
        return $arr;
    }

    /**
     * 替換所有時間字段
     * @param array $params
     * @param string $timeField
     * @return array
     */
    public static function replaceTime(array $params, string $timeField = '_time'): array
    {

//        foreach ($params as $key => $value) {
//            if(is_array($value)) {
//                $params[$key] = self::replaceTime($value, $timeField);
//                continue;
//            }
//            if(is_string($value)) {
//                if (strpos($key, $timeField) !== false and !empty($value)) {
//                    if(strpos( $value,'-') !== false && strrpos($value, "\n") === false) {
//                        $params[$key] = strtotime($value);
//                    }
//                }
//            }
//        }
        return $params;
    }

    /**
     * 二維數組根據key進行排序
     * @param array $array
     * @param string $key
     * @param string $order
     */
    public static function multiSortArray(array &$array, string $key, $order = 'desc')
    {
        usort($array, function ($one, $two) use ($key, $order) {
            if ($one[$key] > $two) {
                return $order == 'desc' ? -1 : 1;
            }
            return $order == 'desc' ? 1 : -1;
        });
    }

    /**
     * 字符串化
     * @param array $array
     * @param array $except
     * @return array
     */
    public static function strval(array $array, array $except = [])
    {
        return StrParse::strval($array, $except);
    }

    /**
     * 覆蓋合並並且替換
     * @param array $arr1
     * @param array $arr2
     * @return array
     */
    public static function arrayMergeCover(array $arr1, array $arr2): array
    {
        $diffKey = array_diff_key($arr2, $arr1);
        if (!empty($diffKey)) {
            foreach ($diffKey as $key) {
                $arr1[$key] = $arr2[$key];
                unset($arr2[$key]);
            }
        }

        foreach ($arr1 as $key => $value) {
            if (is_array($value)) {
                $arr1[$key] = self::arrayMergeCover($arr1[$key], $arr2[$key]);
            } else {
                $arr1[$key] = isset($arr2[$key]) ? $arr2[$key] : $arr1[$key];
            }
        }

        return $arr1;
    }

    /**
     * 合並並且移除空的字符下標
     * @param array $arr1
     * @param array $arr2
     * @return array
     */
    public static function arrayMergeRemove(array $arr1, array $arr2): array
    {
        $diffKey = array_diff_key($arr1, $arr2);
        if (!empty($diffKey)) {
            throw new \InvalidArgumentException('參數個數錯誤:' . json_encode($diffKey), ErrorConstant::SYSTEM_ERR);
        }

        foreach ($arr1 as $key => $value) {
            if (is_array($value)) {
                $arr1[$key] = self::arrayMergeRemove($arr1[$key], $arr2[$key]);
            } else {
                if ($arr2[$key] !== '') {
                    $arr1[$key] = $arr2[$key];
                } else {
                    unset($arr1[$key]);
                }
            }
        }

        return $arr1;
    }


    public static function coverGet(array $data) : array
    {

        if(isset($data['cover'])) {
            return self::setCoverUrl($data);
        }

        foreach ($data as &$value) {
            if(is_array($value)) {
                $value = self::setCoverUrl($value);
            }
            unset($value);
        }

        return $data;
    }

    private static function setCoverUrl(array $data)
    {
        if($data['cover'] === '') {
            $data['cover_url'] = '';
            return $data;
        }
        if(is_string($data['cover'])) {

            if(isset($data['cover'][0])) {
                if($data['cover'][0] == '{') {
                    $data['cover'] = json_decode($data['cover'], true);
                } else {
                    $data['cover_url'] = FileSystem::getFileShowUrl($data['cover']);
                }
            }
        }
        if(is_array($data['cover']) or $data['cover'] instanceof  \Iterator) {
            $data['cover_url'] = [];
            foreach ($data['cover'] as $key=>$value) {
                $data['cover_url'][$key] = empty($value) ? '' :FileSystem::getFileShowUrl($value);
            }
        }
        return $data;
    }

    public static function encryptData(array $data, array $keys = [], int $viewLength = 4, string $pos = 'suf') : array
    {
        if (empty($keys)) {
            $keys = ['id_number','residence','passport','email','source','source_id'];
        }

        foreach ($data as $index => $item) {
            if (is_array($item)) {
                foreach ($item as $key => $value) {
                    if (in_array($key, $keys) && !empty($value) && !is_array($value)) {
                        $data[$index][$key] = self::_encrypt($value, $viewLength, $pos);
                    }

                    if (isset($value['third']) && is_array($value['third'])) {
                        $data[$index][$key]['third']= self::encryptData($value['third'], $keys, $viewLength, $pos);
                    }
                }
            } else {
                if (in_array($index, $keys) && !empty($item) && !is_array($item)) {
                    $data[$index] = self::_encrypt($item, $viewLength, $pos);
                }

                if (isset($item['third']) && is_array($item['third'])) {
                    $data[$index]['third']= self::encryptData($item['third'], $keys, $viewLength, $pos);
                }
            }
        }

        return $data;
    }

    /**
     * @param string $value
     * @param int $viewLength
     * @param string $pos  sub|pre
     * @return string
     */
    private static function _encrypt(string $value, int $viewLength = 4, string $pos = 'suf') : string
    {
        $len = mb_strlen($value);

        if($len > $viewLength) {
            if($pos == 'pre') {
                return mb_substr($value, 0, $viewLength).Random::randomString($len-$viewLength, '*');
            }else{
                return Random::randomString($len-$viewLength, '*').mb_substr($value, $len-$viewLength);
            }
        }

        if($len > 1) {
            if($pos == 'pre') {
                return  mb_substr($value, 0, 1).'****';
            }else {
                return '****' . mb_substr($value, 0, 1);
            }
        }

        return '****';
    }

    /**
     * Get a validation factory instance.
     *
     * @return \Illuminate\Contracts\Validation\Factory
     */
    private static function _getValidationFactory()
    {
        return app(Factory::class);
    }

    /**
     * @param array $array
     * @param array $rules
     * @return array
     * @throws \Exception
     */
    public static function validationArray(array $array, array $rules)
    {
        try {
            $validation = self::_getValidationFactory();

            $validation->make($array, $rules)->validate();

            return request()->only(collect($rules)->keys()->map(function ($rule) {
                return Str::contains($rule, '.') ? explode('.', $rule)[0] : $rule;
            })->unique()->toArray());
        } catch (ValidationException $e) {
            throw new \Exception('params error'. $e->getMessage(), ErrorConstant::PARAMS_ERROR);
        }
    }
}