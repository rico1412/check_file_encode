<?php

if (!function_exists('get_text_file_encode'))
{
    /**
     * 获取文本文件的编码
     *
     * @param $path
     * @return string
     * @author 秦昊
     * Date: 2019-08-15 17:06
     */
    function get_text_file_encode($path)
    {
        dump($path);
        if (empty($path))
        {
            return '未知编码';
        }

        $file = new \Illuminate\Http\File($path);

        if (!is_file($file) && !file_exists($file))
        {
            return '未知编码';
        }

        $text_file = fopen($file, 'r'); //开始读取文本文件数据
        try
        {
            while ($file_data = fgetcsv($text_file))
            {
                if (isset($file_data[0]) && filled($file_data[0]))
                {
//                    return detect_encoding($file_data[0]);
                    return get_encoding($file_data[0]);
                }
            }
        } finally
        {
            fclose($text_file);
        }
    }
}

if (!function_exists('detect_encoding'))
{
    /**
     * 获取字符串编码类型
     *
     * @param $str
     * @return string               返回字符编码
     */
    function detect_encoding($str)
    {
        $list = array('UTF-8', 'GBK');
        foreach ($list as $item)
        {
            $tmp = mb_convert_encoding($str, $item, $item);
            if (md5($tmp) == md5($str))
            {
                return $item;
            }
        }
        return '遇到识别不出来的编码！';
    }
}

/**
 * 获取内容的编码
 * @param string $str
 * @return bool|mixed
 */
function get_encoding($str = "") {
    $encodings = array (
        'ASCII',
        'UTF-8',
        'GBK'
    );

//    dump(utf8_has_bom($str));
    if (utf8_has_bom($str)) $str = utf8_remove_bom($str);

//    dd(utf8_has_bom($str));

    dump($str);
    dump(mb_convert_encoding($str, 'GBK', 'GBK'));
    dump(mb_convert_encoding($str, 'UTF-8', 'UTF-8'));
    dump(mb_convert_encoding($str, 'GBK', 'UTF-8'));
    dd(mb_convert_encoding(mb_convert_encoding($str, 'GBK', 'UTF-8'), 'UTF-8', 'GBK'));

    foreach ($encodings as $encoding)
    {
        $tmp    = mb_convert_encoding(mb_convert_encoding ($str, "UTF-32", $encoding), $encoding, "UTF-32");

        dump($str);
        dump($tmp);

        if ($str === $tmp)
        {
            return $encoding;
        }
    }
    return false;
}

/**
 *
 * 检测utf-8内容是否含有BOM头信息
 * @param string $str
 */
function utf8_has_bom($str) 
{
    $chars = substr ( $str, 0, 3 );
    $bom = chr ( 0xEF ) . chr ( 0xBB ) . chr ( 0xBF );
    return $chars === $bom;
}

function utf8_remove_bom($str) 
{
    $bom = chr ( 0xEF ) . chr ( 0xBB ) . chr ( 0xBF );
    return ltrim($str, $bom);
}