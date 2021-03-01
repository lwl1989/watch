<?php
namespace App\Library;

use Yurun\Util\Chinese;

class StrParse
{
    /***
     * 將其他類型轉換爲字符串
     * @param array $data
     * @param array $except
     * @return array
     */
    public static function strval(array $data, array $except = []): array
    {
        foreach ($data as $key => &$v) {
            if (array_key_exists($key, $except)) {
                $v = $except[$key]($v);
                continue;
            }
            if (is_array($v)) {
                $v = self::strval($v, $except);
            } elseif (is_object($v)) {
                $v = json_decode(json_encode($v), true);
                $v = (object)self::strval($v, $except);
            } elseif (is_bool($v)) {
                $v = (true === $v ? 'true' : 'false');
            } else {
                $v = strval($v);
            }

            unset($v);
        }

        return $data;
    }

    /**
     * 將表情符號替換成文本
     * @param $text
     * @param $replace [符號]
     * @return string
     */
    public static function emoji(string $text, string $replace = '[符號]'): string
    {
        $text = json_encode($text);
        preg_match_all("/(\\\\ud83c\\\\u[0-9a-f]{4})|(\\\\ud83d\\\u[0-9a-f]{4})|(\\\\u[0-9a-f]{4})/", $text, $matchs);
        if (!isset($matchs[0][0])) {
            return json_decode($text, true);
        }

        $emoji = $matchs[0];
        foreach ($emoji as $ec) {
            $hex = substr($ec, -4);
            $data = self::isemoji($hex);
            if (!empty($data)) {
                $text = str_replace($ec, $replace, $text);
            }
            if (strlen($ec) == 6) {
                if ($hex >= '2600' and $hex <= '27ff') {
                    $text = str_replace($ec, $replace, $text);
                }
            } else {
                if ($hex >= 'dc00' and $hex <= 'dfff') {
                    $text = str_replace($ec, $replace, $text);
                }
            }
        }

        return json_decode($text, true);
    }

    /**
     * 判斷是否爲表情
     * @param $hex
     * @return bool
     */
    public static function isemoji($hex): bool
    {
        return ($hex == 0x0);
    }
    /**
     *
     * 將UNICODE編碼後的內容進行解碼，編碼後的內容格式為：圖片
     * @param $name
     * @return string
     */
    public static function unicodeEncode($name)
    {
        $name = iconv('UTF-8', 'UCS-2', $name);
        $len = strlen($name);
        $str = '';
        for ($i = 0; $i < $len - 1; $i = $i + 2) {
            $c = $name[$i];
            $c2 = $name[$i + 1];
            if (ord($c) > 0) {    // 兩個字節的文字
                $str .= '\u' . base_convert(ord($c), 10, 16) . base_convert(ord($c2), 10, 16);
            } else {
                $str .= $c2;
            }
        }
        return $str;
    }

    /**
     * 將UNICODE編碼後的內容進行解碼，編碼後的內容格式為：\u56fe\u7247
     * @param $name
     * @return string
     */
    public static function unicodeDecode($name)
    {
        $pattern = '/([\w]+)|(\\\u([\w]{4}))/i';
        preg_match_all($pattern, $name, $matches);
        if (!empty($matches)) {
            $name = '';
            for ($j = 0; $j < count($matches[0]); $j++) {
                $str = $matches[0][$j];
                if (strpos($str, '\\u') === 0) {
                    $code = base_convert(substr($str, 2, 2), 16, 10);
                    $code2 = base_convert(substr($str, 4), 16, 10);
                    $c = chr($code) . chr($code2);
                    $c = iconv('UCS-2', 'UTF-8', $c);
                    $name .= $c;
                } else {
                    $name .= $str;
                }
            }
        }
        return $name;
    }


    /**
     * 符號轉義
     * @param string 中文字符串
     * @return string 轉換後的json格式查詢字符串
     */
    public static function symbolEscape($str): string
    {
        $str = trim(json_encode($str), '"');

        return str_replace('\\', '\\\\', $str);
    }

    /**
     * 將字符 \r\n \n \t 替換爲真實的字符
     * @param $str
     * @return string
     */
    public static function specialStrDecode($str): string
    {
        return str_replace(
            ['\r\n', '\n', '\t'],
            ["\r\n", "\n", "\t"],
            $str
        );
    }

    /**
     * 將字符 \r\n \n \t 替換爲空字符串
     * @param $str
     * @return string
     */
    public static function specialStrEncode($str): string
    {
        return str_replace(
            ["\r\n", "\n", "\t", '&nbsp;'],
            ['', '', '', ' '],
            $str
        );
    }


    /**
     * 解析json字符串
     * @param $string
     * @param bool $assoc
     * @return mixed
     */
    public static function parseJsonDecode($string, $assoc = true)
    {
        return json_decode($string, $assoc);
    }

    /**
     * 格式化成json
     * @param $data
     * @return false|string
     */
    public static function parseJsonEncode($data)
    {
        return json_encode($data);
    }

    /**
     * 獲取指定長度的文本 並移除HTML標籤
     * @param string $string
     * @param int $subLen
     * @return string
     */
    public static function getStrInHtml(string $string, int $subLen = 30)
    {
        $string = strip_tags($string);
        if ($string !== 0 and !empty($string)) {
            $len = mb_strlen($string);
            if ($len < $subLen) {
                return $string;
            }
            $string = trim(str_replace('\n', '', self::emoji($string, '')));
            $string = str_replace("\\\\n", '', $string);
            return mb_substr($string, 0, $subLen, 'utf-8');
        }
        return '';
    }


    /**
     * 下劃線轉駝峯
     * @param string $unCame
     * @param string $separator
     * @return string
     */
    public static function underToCame(string $unCame, string $separator = '_'): string
    {
        $unCame = $separator . str_replace($separator, " ", strtolower($unCame));
        return ltrim(str_replace(" ", "", ucwords($unCame)), $separator);
    }

    /**
     * 駝峯轉下劃線
     * @param string $camelCaps
     * @param string $separator
     * @return string
     */
    public static function CameToUnder(string $camelCaps, string $separator = '_')
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
    }


    /**
     * 將unicode轉換成字符
     * @param int $unicode
     * @return string UTF-8字符
     **/
    public static function unicode2Char($unicode) : string
    {
        if($unicode < 128)     return chr($unicode);
        if($unicode < 2048)    return chr(($unicode >> 6) + 192) .
            chr(($unicode & 63) + 128);
        if($unicode < 65536)   return chr(($unicode >> 12) + 224) .
            chr((($unicode >> 6) & 63) + 128) .
            chr(($unicode & 63) + 128);
        if($unicode < 2097152) return chr(($unicode >> 18) + 240) .
            chr((($unicode >> 12) & 63) + 128) .
            chr((($unicode >> 6) & 63) + 128) .
            chr(($unicode & 63) + 128);
        return false;
    }

    /**
     * 將字符轉換成unicode
     * @param string $char 必須是UTF-8字符
     * @return int
     **/
    public static function char2Unicode($char) : int
    {
        switch (strlen($char)){
            case 1 : return ord($char);
            case 2 : return (ord($char{1}) & 63) |
                ((ord($char{0}) & 31) << 6);
            case 3 : return (ord($char{2}) & 63) |
                ((ord($char{1}) & 63) << 6) |
                ((ord($char{0}) & 15) << 12);
            case 4 : return (ord($char{3}) & 63) |
                ((ord($char{2}) & 63) << 6) |
                ((ord($char{1}) & 63) << 12) |
                ((ord($char{0}) & 7)  << 18);
            default :
                trigger_error('Character is not UTF-8!', E_USER_WARNING);
                return false;
        }
    }

    /**
     * 全角轉半角
     * @param $str
     * @return string
     */
    public static function sbc2Dbc($str)
    {
        $arr = array('０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',
            '５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',
            'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E',
            'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J',
            'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O',
            'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T',
            'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y',
            'Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd',
            'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i',
            'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n',
            'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's',
            'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',
            'ｙ' => 'y', 'ｚ' => 'z',
            '（' => '(', '）' => ')', '〔' => '[', '〕' => ']', '【' => '[',
            '】' => ']', '〖' => '[', '〗' => ']', '“' => '"', '”' => '"',
            '｛' => '{', '｝' => '}', '《' => '<',
            '》' => '>', '％' => '%', '＋' => '+', '—' => '-', '－' => '-', '～' => '-',
            '：' => ':', '。' => '.', '、' => ',', '，' => '.',
            '；' => ',', '？' => '?', '！' => '!', '…' => '-', '‖' => '|',
            '’' => '`', '‘' => '`', '｜' => '|', '〃' => '"',
            '　' => ' ', '＄' => '$', '＠' => '@', '＃' => '#', '＾' => '^', '＆' => '&', '＊' => '*',
            '＂' => '"');

        return strtr($str, $arr);
    }

    /**
     * 寬鬆的字符串比對
     * @param string $str
     * @param string $str1
     * @return bool
     */
    public static function judgeStrLoosest(string $str, string $str1) : bool
    {
        if(empty($str) && empty($str1)) {
            return true;
        }
        if(empty($str1)) {
            return false;
        }
        $arr = Chinese::toTraditional($str);
        $arr1 = Chinese::toTraditional($str1);
        if(is_array($arr) and is_array($arr1) and count($arr) > 0 and count($arr) == count($arr1)) {

            foreach ($arr as $k=>$v) {
                if(!isset($arr1[$k]) or strtolower(self::sbc2Dbc($arr1[$k])) != strtolower(self::sbc2Dbc($arr[$k]))) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * 返回當前url
     * @return string
     */
    public static function getCurrentURL()
    {
        return static::getScheme().'://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
    }

    /**
     * 返回擋墻訪問的scheme
     * @return string
     */
    public static function getScheme()
    {
        if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
            return 'https';
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            return 'https';
        } elseif (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
            return 'https';
        }

        return 'http';
    }
}