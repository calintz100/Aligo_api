<?php

class Kakao_Aligo
{

    private $_apikey = ""; //'발급받은 API 키'
    private $_userid = ""; //사용중이신 알리고 id
    private $_plusid = ""; // 카카오채널 아이디(@포함)
    private $_phonenumber = ""; //카카오채널 알림받는 관리자 핸드폰 번호
    private $_sender_key = "";



    public function __construct()
    { //생성자

        //토큰이 없으면 생성
        //if (!isset($_SESSION['aligo_token'])) {
        //$_SESSION['aligo_token'] = $this->create_token();
        set_session('aligo_token', $this->create_token());
        //}
    }

    public function create_token()
    {

        $_apiURL = 'https://kakaoapi.aligo.in/akv10/token/create/30/s/';
        $_hostInfo = parse_url($_apiURL);
        $_port = (strtolower($_hostInfo['scheme']) == 'https') ? 443 : 80;
        $_variables = array(
            'apikey' => $this->_apikey,
            'userid' => $this->_userid
        );

        $retArr = $this->aligo_curl($_port, $_apiURL, $_variables);

        return $retArr->token;
    }



    public function profile_auth()
    {

        $_apiURL      =    'https://kakaoapi.aligo.in/akv10/profile/auth/';
        $_hostInfo    =    parse_url($_apiURL);
        $_port          =    (strtolower($_hostInfo['scheme']) == 'https') ? 443 : 80;
        $_variables    =    array(
            'apikey'      =>  $this->_apikey,
            'userid'      =>  $this->_userid,
            'token'       =>  $_SESSION['aligo_token'],
            'plusid'      => $this->_plusid,
            'phonenumber' => $this->_phonenumber
        );


        $retArr = $this->aligo_curl($_port, $_apiURL, $_variables);


        //에러 있을경우 에러 출력
        if ($retArr->code != 0) {
            return $retArr->message;
        }


        return true;
    }


    public function get_category()
    {

        $_apiURL      =    'https://kakaoapi.aligo.in/akv10/category/';
        $_hostInfo    =    parse_url($_apiURL);
        $_port          =    (strtolower($_hostInfo['scheme']) == 'https') ? 443 : 80;
        $_variables    =    array(
            'apikey'      =>  $this->_apikey,
            'userid'      =>  $this->_userid,
            'token'       =>  $_SESSION['aligo_token']
        );

        $retArr = $this->aligo_curl($_port, $_apiURL, $_variables);

        // 결과값 출력
        //print_r($retArr);

        return $retArr;
    }


    //친구등록 심사요청 
    public function profile_add($authnum) //인증 메세지 번호입력
    {


        $cagegory_info = $this->get_category();

        $_apiURL    =    'https://kakaoapi.aligo.in/akv10/profile/add/';
        $_hostInfo  =    parse_url($_apiURL);
        $_port      =    (strtolower($_hostInfo['scheme']) == 'https') ? 443 : 80;
        $_variables    =    array(
            'apikey'      =>  $this->_apikey,
            'userid'      =>  $this->_userid,
            'token'       =>  $_SESSION['aligo_token'],
            'plusid'      => $this->_plusid,
            'authnum'    => $authnum,
            'phonenumber' => $this->_phonenumber,
            'categorycode'  => $cagegory_info[2]['code']
        );

        $retArr = $this->aligo_curl($_port, $_apiURL, $_variables);

        // 결과값 출력
        //print_r($retArr);
    }



    public function send_alimtalk_templt($receiver_1, $name, $templt_number)
    {

        //신청자명이 테스트이거나, 테스터가 포함되면 발송되지 않음!
        if (strpos($name, "테스트") !== false || strpos($name, "테스터") !== false) {
            return false;
        }


        //템플릿 리스트에서 템플릿 목록 가져오기
        $templt_list = $this->template_list();

        //사용할 템플릿 번호지정
        $templt = $templt_list->list[$templt_number];

        //템플릿 내용에서 고객명 변경
        $templt_content = $templt->templtContent;
        $templt_content = str_replace("#{고객명}", $name, $templt_content);

        $button_json = json_encode($templt->buttons, JSON_UNESCAPED_UNICODE);


        //echo $templt->templtContent;
        //echo '{"button": ' . $button_json . '}';


        $_apiURL    =    'https://kakaoapi.aligo.in/akv10/alimtalk/send/';
        $_hostInfo  =    parse_url($_apiURL);
        $_port      =    (strtolower($_hostInfo['scheme']) == 'https') ? 443 : 80;
        $_variables =    array(
            'apikey'      =>  $this->_apikey,
            'userid'      =>  $this->_userid,
            'token'       =>  $_SESSION['aligo_token'],
            'senderkey'   =>  $this->_sender_key,
            'tpl_code'    => $templt->templtCode,
            'sender'      => $this->_phonenumber,
            'receiver_1'  => $receiver_1,
            'subject_1'   => '알림톡',
            'message_1'   => $templt_content,
            'button_1'    => '{"button": ' . $button_json . '}' // 템플릿에 버튼이 없는경우 제거하시기 바랍니다.


            //'senddate'    => date("YmdHis", strtotime("+10 minutes")),
            //'senddate'    => date("YmdHis", strtotime("+11 minutes")),
            //'receiver_2'  => '첫번째 알림톡을 전송받을 휴대폰 번호',
            //'recvname_2'  => '첫번째 알림톡을 전송받을 사용자 명',
            //'subject_2'   => '첫번째 알림톡을 제목',
            //'message_2'   => '첫번째 템플릿내용을 기초로 작성된 전송할 메세지 내용',
            //'button_2'    => '{"button":[{"name":"테스트 버튼","linkType":"DS"}]}' // 템플릿에 버튼이 없는경우 제거하시기 바랍니다.
        );

        //curl로 api전송
        $ret = $this->aligo_curl($_port, $_apiURL, $_variables);
    }


    public function send_alimtalk($receiver_1, $name)
    {
        $this->send_alimtalk_templt($receiver_1, $name, 0);
    }

    public function send_alimtalk2($receiver_1, $name)
    {
        $this->send_alimtalk_templt($receiver_1, $name, 1);
    }

    public function send_alimtalk3($receiver_1, $name)
    {
        $this->send_alimtalk_templt($receiver_1, $name, 2);
    }

    public function send_alimtalk4($receiver_1, $name)
    {
        $this->send_alimtalk_templt($receiver_1, $name, 3);
    }

    public function send_alimtalk5($receiver_1, $name)
    {
        $this->send_alimtalk_templt($receiver_1, $name, 4);
    }

    public function send_alimtalk6($receiver_1, $name)
    {
        $this->send_alimtalk_templt($receiver_1, $name, 5);
    }



    public function random_alimtalk($receiver_1, $name)
    {

        //메세지 3개중 랜덤하게 하나 전송
        $rand = rand(1, 3);

        if ($rand == 1) {
            $this->send_alimtalk($receiver_1, $name);
        } else if ($rand == 2) {
            $this->send_alimtalk2($receiver_1, $name);
        } else {
            $this->send_alimtalk3($receiver_1, $name);
        }
    }



    public function template_list()
    {
        /*
        -----------------------------------------------------------------------------------
        등록된 템플릿 리스트
        -----------------------------------------------------------------------------------
        등록된 템플릿 목록을 조회합니다. 템플릿 코드가 D 나 P 로 시작하는 경우 공유 템플릿이므로 삭제 불가능 합니다.
        */

        $_apiURL        =    'https://kakaoapi.aligo.in/akv10/template/list/';
        $_hostInfo    =    parse_url($_apiURL);
        $_port            =    (strtolower($_hostInfo['scheme']) == 'https') ? 443 : 80;
        $_variables    =    array(
            'apikey'      =>  $this->_apikey,
            'userid'      =>  $this->_userid,
            'token'       =>  $_SESSION['aligo_token'],
            'senderkey'   => $this->_sender_key,
        );


        $retArr = $this->aligo_curl($_port, $_apiURL, $_variables);

        return $retArr;
    }


    public function history_list()
    {


        $_apiURL        =    'https://kakaoapi.aligo.in/akv10/history/list/';
        $_hostInfo    =    parse_url($_apiURL);
        $_port            =    (strtolower($_hostInfo['scheme']) == 'https') ? 443 : 80;
        $_variables    =    array(
            'apikey'      =>  $this->_apikey,
            'userid'      =>  $this->_userid,
            'token'       =>  $_SESSION['aligo_token']
        );

        $this->aligo_curl($_port, $_apiURL, $_variables);
    }


    public function history_detail($mid)
    {


        $_apiURL        =    'https://kakaoapi.aligo.in/akv10/history/detail/';
        $_hostInfo    =    parse_url($_apiURL);
        $_port            =    (strtolower($_hostInfo['scheme']) == 'https') ? 443 : 80;
        $_variables    =    array(
            'apikey'      =>  $this->_apikey,
            'userid'      =>  $this->_userid,
            'token'       =>  $_SESSION['aligo_token'],

            'mid'           => $mid,

        );

        $this->aligo_curl($_port, $_apiURL, $_variables);
    }


    private function aligo_curl($_port, $_apiURL, $_variables)
    {

        $oCurl = curl_init();
        curl_setopt($oCurl, CURLOPT_PORT, $_port);
        curl_setopt($oCurl, CURLOPT_URL, $_apiURL);
        curl_setopt($oCurl, CURLOPT_POST, 1);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, http_build_query($_variables));
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);

        $ret = curl_exec($oCurl);
        $error_msg = curl_error($oCurl);
        curl_close($oCurl);


        // JSON 문자열 배열 변환
        $retArr = json_decode($ret);
        return $retArr;
    }

    // 세션변수 생성
    public function set_session($session_name, $value)
    {
        global $g5;

        static $check_cookie = null;

        if ($check_cookie === null) {
            $cookie_session_name = session_name();
            if (!isset($g5['session_cookie_samesite']) && !($cookie_session_name && isset($_COOKIE[$cookie_session_name]) && $_COOKIE[$cookie_session_name]) && !headers_sent()) {
                @session_regenerate_id(false);
            }

            $check_cookie = 1;
        }

        if (PHP_VERSION < '5.3.0')
            session_register($session_name);
        // PHP 버전별 차이를 없애기 위한 방법
        $$session_name = $_SESSION[$session_name] = $value;
    }
}
