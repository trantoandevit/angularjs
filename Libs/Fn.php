<?php

use Libs\Bootstrap;
use Libs\SQL\EntitySet;

function url($path = '', $params = array())
{
    $app = Bootstrap::getInstance();
    $url = $app->rewriteBase . $path;
    $sep = '?';
    foreach ($params as $k => $v)
    {
        $url .= "{$sep}{$k}={$v}";
        $sep = '&';
    }
    return $url;
}

function urlAbsolute($path = '', $params = array())
{
    $protocol = empty($_SERVER['HTTPS']) ? 'http' : 'https';
    $host = $_SERVER['HTTP_HOST'];
    $port = $_SERVER['SERVER_PORT'] == 80 || $_SERVER['SERVER_PORT'] == 443 ? '' : ':' . $_SERVER['SERVER_PORT'];
    return "{$protocol}://{$host}{$port}/" . static::url($path, $params);
}

function arrData($arr, $key, $default = null)
{
    return isset($arr[$key]) ? $arr[$key] : $default;
}

/**
 * Chuyển mảng về tham số get
 * @param array $arr
 * @return string
 */
function encodeForm($arr)
{
    $ret = '';
    foreach ($arr as $k => $v)
    {
        $ret .= $ret ? "&{$k}={$v}" : "{$k}={$v}";
    }
    return $ret;
}

define('XPATH_STRING', 1);
define('XPATH_ARRAY', 2);
define('XPATH_DOM', 3);

/**
 * 
 * @param SimpleXmlElement $dom
 * @param type $xpath
 * @param type $method
 * @return SimpleXmlElement
 */
function xpath($dom, $xpath, $method = 1)
{
    if ($dom instanceof SimpleXMLElement == false)
    {
        switch ($method)
        {
            case XPATH_STRING:
                return '';
            case XPATH_ARRAY:
                return array();
            case XPATH_DOM:
                return null;
        }
    }

    $r = $dom->xpath($xpath);
    switch ($method)
    {
        case XPATH_ARRAY:
            return $r;
        case XPATH_DOM:
            return $r[0];
        case XPATH_STRING:
        default:
            return count($r) ? (string) $r[0] : null;
    }
}

/**
 * Lấy config từ thư mục Config/
 * @param string $fileName
 * @return mixed
 */
function getConfig($fileName)
{
    $fullPath = BASE_DIR . '/Config/' . $fileName;
    if (strpos($fileName, '.config.php') !== false)
    {
        require $fullPath;
        if (!isset($exports))
        {
            throw new Exception($fullPath . ' phải có biến $exports');
        }
        return $exports;
    }
    else if (strpos($fileName, '.xml') !== false)
    {
        $exports = array();
        $dom = simplexml_load_file($fullPath);
        if (!$dom)
        {
            return $exports;
        }
        foreach ($dom as $field)
        {
            $exports[strval($field->id)] = strval($field->value);
        }

        return $exports;
    }

    throw new Exception($fullPath . ' không hợp lệ (chỉ hỗ trợ .config.php hoặc .xml)');
}

function validateDate($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

function replace_bad_char($str)
{
    $str = stripslashes($str);
    $str = str_replace("&", '&amp;', $str);
    $str = str_replace('<', '&lt;', $str);
    $str = str_replace('>', '&gt;', $str);
    $str = str_replace('"', '&#34;', $str);
    $str = str_replace("'", '&#39;', $str);

    return $str;
}

/**
 * Lấy dữ liệu từ array
 * @param array $array
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function fetch_array($array, $key, $default = NULL)
{
    return isset($array[(string) $key]) ? $array[(string) $key] : $default;
}

//end func replace_bad_char

function create_single_xml_node($name, $value, $cdata = FALSE)
{
    $node = '<' . $name . '>';
    $node .= ($cdata) ? '<![CDATA[' . $value . ']]>' : $value;
    $node .= '</' . $name . '>';

    return $node;
}

//end func create_single_xml_node

function hidden($name, $value = '')
{
    if (strpos($value, '"') !== FALSE)
    {
        return '<input type="hidden" name="' . $name . '" id="' . $name . '" value=\'' . $value . '\' />';
    }
    else
    {
        return '<input type="hidden" name="' . $name . '" id="' . $name . '" value="' . $value . '" />';
    }
}

function page_calc(&$v_start, &$v_end)
{
    //Luu dieu kien loc
    $v_page = isset($_POST['sel_goto_page']) ? replace_bad_char($_POST['sel_goto_page']) : 1;
    $v_rows_per_page = isset($_POST['sel_rows_per_page']) ? replace_bad_char($_POST['sel_rows_per_page']) : _CONST_DEFAULT_ROWS_PER_PAGE;

    $v_start = $v_rows_per_page * ($v_page - 1) + 1;
    $v_end = $v_start + $v_rows_per_page - 1;
}

function get_controller_url($module = NULL, $app = NULL)
{
    if (empty($app))
    {
        $app = $this->app_name;
    }
    if (empty($module))
    {
        $module = $this->module_name;
    }

    if (file_exists('.htaccess'))
    {
        return SITE_ROOT . $app . '/' . $module . '/';
    }
    return SITE_ROOT . 'index.php?url=' . $app . '/' . $module . '/';
}

function is_id_number($id)
{
    return (preg_match('/^\d*$/', trim($id)) == 1);
}

function get_post_var($html_object_name, $default_value = '', $is_replace_bad_char = TRUE)
{
    $var = isset($_POST[$html_object_name]) ? $_POST[$html_object_name] : $default_value;

    if ($is_replace_bad_char && !is_array($var))
    {
        return replace_bad_char($var);
    }

    return $var;
}

function get_request_var($html_object_name, $default_value = '', $is_replace_bad_char = TRUE)
{
    $var = isset($_REQUEST[$html_object_name]) ? $_REQUEST[$html_object_name] : $default_value;

    if ($is_replace_bad_char && !is_array($var))
    {
        return replace_bad_char($var);
    }

    return $var;
}

function get_filter_condition($arr_html_object_name = array())
{
    $arr_filter = array();
    foreach ($arr_html_object_name as $v_html_object_name)
    {
        $arr_filter[$v_html_object_name] = get_request_var($v_html_object_name);
    }

    return $arr_filter;
}

function get_role($task_code)
{
    return trim(preg_replace('/[A-Z0-9_]*[:]+/', '', $task_code));
}

function xml_remove_declaration($xml_string)
{
    return trim(preg_replace('/\<\?xml(.*)\?\>/', '', $xml_string));
}

function xml_add_declaration($xml_string, $utf8_encoding = TRUE)
{
    $xml_string = xml_remove_declaration($xml_string);

    if ($utf8_encoding)
    {
        return '<?xml version="1.0" encoding="UTF-8"?>' . $xml_string;
    }

    return '<?xml version="1.0" standalone="yes"?>' . $xml_string;
}

function get_xml_value($dom, $xpath)
{
    $r = $dom ? $dom->xpath($xpath) : array();
    if (isset($r[0]))
    {
        return strval($r[0]);
    }

    return NULL;
}


/**
 * Tính số ngày chênh lệch giữa 2 ngày
 * @param string $begin_date Ngay bat dau, dang in yyyy-mm-dd
 * @param string $end_date Ngay ket thuc, dang yyyy-mm-dd
 * @return Int
 */
function days_diff($begin_date_yyyymmdd, $end_date_yyyymmdd)
{
    $b = date_create($begin_date_yyyymmdd);
    $e = date_create($end_date_yyyymmdd);

    $interval = date_diff($b, $e);
    return intval($interval->format('%R%a'));
}

function is_past_date($date_yyyymmdd)
{
    $today = Date('y-m-d');
    return days_diff($date_yyyymmdd, $today) > 0;
}

function is_future_date($date_yyyymmdd)
{
    $today = Date('y-m-d');
    return days_diff($date_yyyymmdd, $today) < 0;
}

/**
 * Lay gia tri cho boi dau hieu mau
 *
 * @param string $html_content Xau can lay
 * @param string dau hieu bat dau $bp
 * @param string dau hieu ket thuc $ep
 * @return string xau thu duoc
 */
function get_value_by_pattern($html_content, $bp, $ep)
{
    preg_match("/$bp(.+)$ep/eUim", $html_content, $arr_matches);
    if (count($arr_matches) >= 1)
    {
        return ($arr_matches[1]);
    }
    else
    {
        return '';
    }
}

/**
 * Xoa het cac ky tu dieu khien trong doan html text
 *
 * 		o Dau xuong dong
 * 		o Dong moi
 * 		o Cac dau cach thua
 *
 * @param string $text Xau ky tu vao
 * @return string Xau thu duoc sau khi da xoa het ky tu dieu khien
 * @author Ngo Duc Lien
 */
function delete_control_characters($text)
{
    $ret_text = preg_replace(
            array(
        '/\s+/u'      // Any space(s)
        , '/^\s+/u'      // Any space(s) at the beginning
        , '/\s+$/u'      // Any space(s) at the end
        , '/\n+/u'      // Any New line(s)
        , '/\r+/u'      // Any Rn(s)
            ), array(
        ' '    // ... one space
        , ''    // ... nothing
        , ''     // ... nothing
        , ''     // ... nothing
        , ''     // ... nothing
            ), $text);
    return $ret_text;
}

function parse_boolean($str)
{
    if ($str == '')
    {
        return FALSE;
    }
    switch (strtolower($str))
    {
        case 'true':
        case '1':
        case 'yes':
        case 'y':
            return TRUE;
    }

    return FALSE;
}

function toStrictBoolean($_val, $_trueValues = array('yes', 'y', 'true'), $_forceLowercase = true)
{
    if (is_string($_val))
    {
        return (in_array(
                        ($_forceLowercase ? strtolower($_val) : $_val)
                        , $_trueValues)
                );
    }
    else
    {
        return (boolean) $_val;
    }
}

function write_xml_file($v_xml_string, $v_xml_file_path)
{
    $ok = TRUE;
    $v_error_code = 0; //Khong loi; 1: loi khong welform, 2: Khong ghi duoc file
    $v_message = 'Cập nhật dữ liệu thất bại!. ';
    //Kiem tra xml welform
    $dom_flow = @simplexml_load_string($v_xml_string);
    if ($dom_flow === FALSE)
    {
        return 1;
    }

    //Ghi file
    if ($ok)
    {
        $v_dir = dirname($v_xml_file_path);
        if (!is_dir($v_dir))
        {
            @mkdir($v_dir);
        }
        $r = @file_put_contents($v_xml_file_path, $v_xml_string);
        if ($r === FALSE OR $r === 0)
        {
            return 2;
        }
    }

    return 0;
}

function check_htacces_file()
{
    if (file_exists(SERVER_ROOT . '.htaccess'))
    {
        return TRUE;
    }
    return FALSE;
}

function build_url($url)
{
    if (check_htacces_file())
    {
        return $url;
    }

    return 'index.php?url=' . $url;
}

function get_session_token()
{
    @session_start();
    return md5(session_id());
}

function validate_session_token($token)
{
    @session_start();
    return (md5(session_id()) == $token) ? true : false;
}

/**
 * 
 * @param type $src
 * @param type $signature
 * @return boolean
 * @throws Exception
 */
function verify_signature($src, $signature)
{
    if (!IS_SIGNATURE_REQUIRED)
    {
        return true;
    }
    $signature = trim($signature);

    //1. create file
    $uniq_id = uniqid();
    $sign_file = SERVER_ROOT . 'cache/cert/sign_' . $uniq_id;
    $source_file = SERVER_ROOT . 'cache/cert/source_' . uniqid();
    if (!is_dir(dirname($sign_file)) && !mkdir(dirname($sign_file), 0777, true))
    {
        throw new Exception('Không tạo được thư mục temp');
    }
    file_put_contents($sign_file, $signature);
    file_put_contents($source_file, $src);
    register_shutdown_function(function($sign_file, $source_file)
    {
        unlink($sign_file);
        unlink($source_file);
    }, $sign_file, $source_file);

    //verify
    $java_home = JAVA_HOME;
    $verifier_path = SERVER_ROOT . 'libs/ntcaServer.jar';
    if (DIRECTORY_SEPARATOR == "\\")
    {
        //window
        $command = "\"{$java_home}java\" -jar $verifier_path \"$sign_file\" \"$source_file\" \"1\"";
    }
    else
    {
        //linux
        $command = "{$java_home}java -jar $verifier_path $sign_file $source_file \"1\"";
    }

    exec($command, $cmd_output);
    if (DEBUG_MODE >= 10)
    {
        echo '<hr>' . $command . '<hr>';
    }
    if (fetch_array($cmd_output, 1) == 'Chu ky hop le' && fetch_array($cmd_output, 2) == 'Chung thu hop le')
    {
        return true;
    }
    else
    {
        return false;
    }
}

function unicode_to_composite($str)
{
    ///unicode
    $unicode = preg_split("/\,/", 'á,à,ả,ã,ạ,ă,ắ,ằ,ẳ,ẵ,ặ,â,ấ,ầ,ẩ,ẫ,ậ,é,è,ẻ,ẽ,ẹ,ê,ế,ề,ể,ễ,ệ,í,ì,ỉ,ĩ,ị,ó,ò,ỏ,õ,ọ,ô,ố,ồ,ổ,ỗ,ộ,ơ,ớ,ờ,ở,ỡ,ợ,ú,ù,ủ,ũ,ụ,ư,ứ,ừ,ử,ữ,ự,ý,ỳ,ỷ,ỹ,ỵ,đ,Á,À,Ả,Ã,Ạ,Ă,Ắ,Ằ,Ẳ,Ẵ,Ặ,Â,Ấ,Ầ,Ẩ,Ẫ,Ậ,É,È,Ẻ,Ẽ,Ẹ,Ê,Ế,Ề,Ể,Ễ,Ệ,Í,Ì,Ỉ,Ĩ,Ị,Ó,Ò,Ỏ,Õ,Ọ,Ô,Ố,Ồ,Ổ,Ỗ,Ộ,Ơ,Ớ,Ờ,Ở,Ỡ,Ợ,Ú,Ù,Ủ,Ũ,Ụ,Ư,Ứ,Ừ,Ử,Ữ,Ự,Ý,Ỳ,Ỷ,Ỹ,Ỵ,Đ');

    //unicode to hop
    $composite = preg_split("/\,/", 'á,à,ả,ã,ạ,ă,ắ,ằ,ẳ,ẵ,ặ,â,ấ,ầ,ẩ,ẫ,ậ,é,è,ẻ,ẽ,ẹ,ê,ế,ề,ể,ễ,ệ,í,ì,ỉ,ĩ,ị,ó,ò,ỏ,õ,ọ,ô,ố,ồ,ổ,ỗ,ộ,ơ,ớ,ờ,ở,ỡ,ợ,ú,ù,ủ,ũ,ụ,ư,ứ,ừ,ử,ữ,ự,ý,ỳ,ỷ,ỹ,ỵ,đ,Á,À,Ả,Ã,Ạ,Ă,Ắ,Ằ,Ẳ,Ẵ,Ặ,Â,Ấ,Ầ,Ẩ,Ẫ,Ậ,É,È,Ẻ,Ẽ,Ẹ,Ê,Ế,Ề,Ể,Ễ,Ệ,Í,Ì,Ỉ,Ĩ,Ị,Ó,Ò,Ỏ,Õ,Ọ,Ô,Ố,Ồ,Ổ,Ỗ,Ộ,Ơ,Ớ,Ờ,Ở,Ỡ,Ợ,Ú,Ù,Ủ,Ũ,Ụ,Ư,Ứ,Ừ,Ử,Ữ,Ự,Ý,Ỳ,Ỷ,Ỹ,Ỵ,Đ');

    foreach ($unicode as $key => $val)
        $ret_str[$val] = $composite[$key];

    return strtr($str, $ret_str);
}

function utf8_to_composite($str)
{
    ///unicode
    $utf8 = preg_split("/\,/", 'Ã¡,Ã ,áº£,Ã£,áº¡,Äƒ,áº¯,áº±,áº³,áºµ,áº·,Ã¢,áº¥,áº§,áº©,áº«,áº­,Ã©,Ã¨,áº»,áº½,áº¹,Ãª,áº¿,á»,á»ƒ,á»…,á»‡,Ã­,Ã¬,á»‰,Ä©,á»‹,Ã³,Ã²,á»,Ãµ,á»,Ã´,á»‘,á»“,á»•,á»—,á»™,Æ¡,á»›,á»,á»Ÿ,á»¡,á»£,Ãº,Ã¹,á»§,Å©,á»¥,Æ°,á»©,á»«,á»­,á»¯,á»±,Ã½,á»³,á»·,á»¹,á»µ,Ä‘,Ã,Ã€,áº¢,Ãƒ,áº ,Ä‚,áº®,áº°,áº²,áº´,áº¶,Ã‚,áº¤,áº¦,áº¨,áºª,áº¬,Ã‰,Ãˆ,áºº,áº¼,áº¸,ÃŠ,áº¾,á»€,á»‚,á»„,á»†,Ã,ÃŒ,á»ˆ,Ä¨,á»Š,Ã“,Ã’,á»Ž,Ã•,á»Œ,Ã”,á»,á»’,á»”,á»–,á»˜,Æ ,á»š,á»œ,á»ž,á» ,á»¢,Ãš,Ã™,á»¦,Å¨,á»¤,Æ¯,á»¨,á»ª,á»¬,á»®,á»°,Ã,á»²,á»¶,á»¸,á»´,Ä');

    //unicode to hop
    $composite = preg_split("/\,/", 'á,à,ả,ã,ạ,ă,ắ,ằ,ẳ,ẵ,ặ,â,ấ,ầ,ẩ,ẫ,ậ,é,è,ẻ,ẽ,ẹ,ê,ế,ề,ể,ễ,ệ,í,ì,ỉ,ĩ,ị,ó,ò,ỏ,õ,ọ,ô,ố,ồ,ổ,ỗ,ộ,ơ,ớ,ờ,ở,ỡ,ợ,ú,ù,ủ,ũ,ụ,ư,ứ,ừ,ử,ữ,ự,ý,ỳ,ỷ,ỹ,ỵ,đ,Á,À,Ả,Ã,Ạ,Ă,Ắ,Ằ,Ẳ,Ẵ,Ặ,Â,Ấ,Ầ,Ẩ,Ẫ,Ậ,É,È,Ẻ,Ẽ,Ẹ,Ê,Ế,Ề,Ể,Ễ,Ệ,Í,Ì,Ỉ,Ĩ,Ị,Ó,Ò,Ỏ,Õ,Ọ,Ô,Ố,Ồ,Ổ,Ỗ,Ộ,Ơ,Ớ,Ờ,Ở,Ỡ,Ợ,Ú,Ù,Ủ,Ũ,Ụ,Ư,Ứ,Ừ,Ử,Ữ,Ự,Ý,Ỳ,Ỷ,Ỹ,Ỵ,Đ');

//        foreach( $composite as $key => $val) $ret_str[ $val]= $utf8[ $key];
    foreach ($utf8 as $key => $val)
        $ret_str[$val] = $composite[$key];

    return strtr($str, $ret_str);
}

function unicode_to_nosign($str)
{
    $ret_str = Array();

    $unicode = preg_split("/\,/", 'á,à,ả,ã,ạ,ă,ắ,ằ,ẳ,ẵ,ặ,â,ấ,ầ,ẩ,ẫ,ậ,é,è,ẻ,ẽ,ẹ,ê,ế,ề,ể,ễ,ệ,í,ì,ỉ,ĩ,ị,ó,ò,ỏ,õ,ọ,ô,ố,ồ,ổ,ỗ,ộ,ơ,ớ,ờ,ở,ỡ,ợ,ú,ù,ủ,ũ,ụ,ư,ứ,ừ,ử,ữ,ự,ý,ỳ,ỷ,ỹ,ỵ,đ,Á,À,Ả,Ã,Ạ,Ă,Ắ,Ằ,Ẳ,Ẵ,Ặ,Â,Ấ,Ầ,Ẩ,Ẫ,Ậ,É,È,Ẻ,Ẽ,Ẹ,Ê,Ế,Ề,Ể,Ễ,Ệ,Í,Ì,Ỉ,Ĩ,Ị,Ó,Ò,Ỏ,Õ,Ọ,Ô,Ố,Ồ,Ổ,Ỗ,Ộ,Ơ,Ớ,Ờ,Ở,Ỡ,Ợ,Ú,Ù,Ủ,Ũ,Ụ,Ư,Ứ,Ừ,Ử,Ữ,Ự,Ý,Ỳ,Ỷ,Ỹ,Ỵ,Đ');

    $nosign = preg_split("/\,/", 'a,a,a,a,a,a,a,a,a,a,a,a,a,a,a,a,a,e,e,e,e,e,e,e,e,e,e,e,i,i,i,i,i,o,o,o,o,o,o,o,o,o,o,o,o,o,o,o,o,o,u,u,u,u,u,u,u,u,u,u,u,y,y,y,y,y,d,A,A,A,A,A,A,A,A,A,A,A,A,A,A,A,A,A,E,E,E,E,E,E,E,E,E,E,E,I,I,I,I,I,O,O,O,O,O,O,O,O,O,O,O,O,O,O,O,O,O,U,U,U,U,U,U,U,U,U,U,U,Y,Y,Y,Y,Y,D');

    foreach ($unicode as $key => $val)
        $ret_str[$val] = $nosign[$key];

    return strtr($str, $ret_str);
}

function composite_to_utf8($str)
{
    ///unicode
    $utf8 = preg_split("/\,/", 'Ã¡,Ã ,áº£,Ã£,áº¡,Äƒ,áº¯,áº±,áº³,áºµ,áº·,Ã¢,áº¥,áº§,áº©,áº«,áº­,Ã©,Ã¨,áº»,áº½,áº¹,Ãª,áº¿,á»,á»ƒ,á»…,á»‡,Ã­,Ã¬,á»‰,Ä©,á»‹,Ã³,Ã²,á»,Ãµ,á»,Ã´,á»‘,á»“,á»•,á»—,á»™,Æ¡,á»›,á»,á»Ÿ,á»¡,á»£,Ãº,Ã¹,á»§,Å©,á»¥,Æ°,á»©,á»«,á»­,á»¯,á»±,Ã½,á»³,á»·,á»¹,á»µ,Ä‘,Ã,Ã€,áº¢,Ãƒ,áº ,Ä‚,áº®,áº°,áº²,áº´,áº¶,Ã‚,áº¤,áº¦,áº¨,áºª,áº¬,Ã‰,Ãˆ,áºº,áº¼,áº¸,ÃŠ,áº¾,á»€,á»‚,á»„,á»†,Ã,ÃŒ,á»ˆ,Ä¨,á»Š,Ã“,Ã’,á»Ž,Ã•,á»Œ,Ã”,á»,á»’,á»”,á»–,á»˜,Æ ,á»š,á»œ,á»ž,á» ,á»¢,Ãš,Ã™,á»¦,Å¨,á»¤,Æ¯,á»¨,á»ª,á»¬,á»®,á»°,Ã,á»²,á»¶,á»¸,á»´,Ä');

    //unicode to hop
    $composite = preg_split("/\,/", 'á,à,ả,ã,ạ,ă,ắ,ằ,ẳ,ẵ,ặ,â,ấ,ầ,ẩ,ẫ,ậ,é,è,ẻ,ẽ,ẹ,ê,ế,ề,ể,ễ,ệ,í,ì,ỉ,ĩ,ị,ó,ò,ỏ,õ,ọ,ô,ố,ồ,ổ,ỗ,ộ,ơ,ớ,ờ,ở,ỡ,ợ,ú,ù,ủ,ũ,ụ,ư,ứ,ừ,ử,ữ,ự,ý,ỳ,ỷ,ỹ,ỵ,đ,Á,À,Ả,Ã,Ạ,Ă,Ắ,Ằ,Ẳ,Ẵ,Ặ,Â,Ấ,Ầ,Ẩ,Ẫ,Ậ,É,È,Ẻ,Ẽ,Ẹ,Ê,Ế,Ề,Ể,Ễ,Ệ,Í,Ì,Ỉ,Ĩ,Ị,Ó,Ò,Ỏ,Õ,Ọ,Ô,Ố,Ồ,Ổ,Ỗ,Ộ,Ơ,Ớ,Ờ,Ở,Ỡ,Ợ,Ú,Ù,Ủ,Ũ,Ụ,Ư,Ứ,Ừ,Ử,Ữ,Ự,Ý,Ỳ,Ỷ,Ỹ,Ỵ,Đ');

//        foreach( $composite as $key => $val) $ret_str[ $val]= $utf8[ $key];
    foreach ($composite as $key => $val)
        $ret_str[$val] = $utf8[$key];

    return strtr($str, $ret_str);
}

function composite_to_unicode($str)
{
    ///unicode
    $unicode = preg_split("/\,/", 'á,à,ả,ã,ạ,ă,ắ,ằ,ẳ,ẵ,ặ,â,ấ,ầ,ẩ,ẫ,ậ,é,è,ẻ,ẽ,ẹ,ê,ế,ề,ể,ễ,ệ,í,ì,ỉ,ĩ,ị,ó,ò,ỏ,õ,ọ,ô,ố,ồ,ổ,ỗ,ộ,ơ,ớ,ờ,ở,ỡ,ợ,ú,ù,ủ,ũ,ụ,ư,ứ,ừ,ử,ữ,ự,ý,ỳ,ỷ,ỹ,ỵ,đ,Á,À,Ả,Ã,Ạ,Ă,Ắ,Ằ,Ẳ,Ẵ,Ặ,Â,Ấ,Ầ,Ẩ,Ẫ,Ậ,É,È,Ẻ,Ẽ,Ẹ,Ê,Ế,Ề,Ể,Ễ,Ệ,Í,Ì,Ỉ,Ĩ,Ị,Ó,Ò,Ỏ,Õ,Ọ,Ô,Ố,Ồ,Ổ,Ỗ,Ộ,Ơ,Ớ,Ờ,Ở,Ỡ,Ợ,Ú,Ù,Ủ,Ũ,Ụ,Ư,Ứ,Ừ,Ử,Ữ,Ự,Ý,Ỳ,Ỷ,Ỹ,Ỵ,Đ');

    //unicode to hop
    $composite = preg_split("/\,/", 'á,à,ả,ã,ạ,ă,ắ,ằ,ẳ,ẵ,ặ,â,ấ,ầ,ẩ,ẫ,ậ,é,è,ẻ,ẽ,ẹ,ê,ế,ề,ể,ễ,ệ,í,ì,ỉ,ĩ,ị,ó,ò,ỏ,õ,ọ,ô,ố,ồ,ổ,ỗ,ộ,ơ,ớ,ờ,ở,ỡ,ợ,ú,ù,ủ,ũ,ụ,ư,ứ,ừ,ử,ữ,ự,ý,ỳ,ỷ,ỹ,ỵ,đ,Á,À,Ả,Ã,Ạ,Ă,Ắ,Ằ,Ẳ,Ẵ,Ặ,Â,Ấ,Ầ,Ẩ,Ẫ,Ậ,É,È,Ẻ,Ẽ,Ẹ,Ê,Ế,Ề,Ể,Ễ,Ệ,Í,Ì,Ỉ,Ĩ,Ị,Ó,Ò,Ỏ,Õ,Ọ,Ô,Ố,Ồ,Ổ,Ỗ,Ộ,Ơ,Ớ,Ờ,Ở,Ỡ,Ợ,Ú,Ù,Ủ,Ũ,Ụ,Ư,Ứ,Ừ,Ử,Ữ,Ự,Ý,Ỳ,Ỷ,Ỹ,Ỵ,Đ');

    foreach ($composite as $key => $val)
        $ret_str[$val] = $unicode[$key];

    return strtr($str, $ret_str);
}