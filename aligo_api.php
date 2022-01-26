
<?


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
        if (!isset($_SESSION['aligo_token'])) {
            //$_SESSION['aligo_token'] = $this->create_token();
            $this->set_session('aligo_token', $this->create_token());
        }
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

        // 리턴 JSON 문자열 확인
        //print_r($ret . PHP_EOL);

        // JSON 문자열 배열 변환
        $retArr = json_decode($ret);

        // 결과값 출력
        //print_r($retArr);

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

        // 리턴 JSON 문자열 확인
        //print_r($ret . PHP_EOL);

        // JSON 문자열 배열 변환
        $retArr = json_decode($ret);

        // 결과값 출력
        //print_r($retArr);


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

        // 리턴 JSON 문자열 확인
        //print_r($ret . PHP_EOL);

        // JSON 문자열 배열 변환
        $retArr = json_decode($ret);

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

        // 리턴 JSON 문자열 확인
        print_r($ret . PHP_EOL);

        // JSON 문자열 배열 변환
        $retArr = json_decode($ret);

        // 결과값 출력
        print_r($retArr);
    }


    public function send_alimtalk($receiver_1, $name)
    {

        $_apiURL    =    'https://kakaoapi.aligo.in/akv10/alimtalk/send/';
        $_hostInfo  =    parse_url($_apiURL);
        $_port      =    (strtolower($_hostInfo['scheme']) == 'https') ? 443 : 80;
        $_variables =    array(
            'apikey'      =>  $this->_apikey,
            'userid'      =>  $this->_userid,
            'token'       =>  $_SESSION['aligo_token'],
            'senderkey'   =>  $this->_sender_key,
            'tpl_code'    => 'TF_7500',
            'sender'      => $this->_phonenumber,
            //'senddate'    => date("YmdHis", strtotime("+10 minutes")),
            //'senddate'    => date("YmdHis", strtotime("+11 minutes")),
            'receiver_1'  => $receiver_1,
            'subject_1'   => '알림톡',
            'message_1'   => "템플릿 메세지 내용 카피",
            'button_1'    => '템플릿 등록 버튼 내용 입력' // 템플릿에 버튼이 없는경우 제거하시기 바랍니다.
            //'receiver_2'  => '첫번째 알림톡을 전송받을 휴대폰 번호',
            //'recvname_2'  => '첫번째 알림톡을 전송받을 사용자 명',
            //'subject_2'   => '첫번째 알림톡을 제목',
            //'message_2'   => '첫번째 템플릿내용을 기초로 작성된 전송할 메세지 내용',
            //'button_2'    => '{"button":[{"name":"테스트 버튼","linkType":"DS"}]}' // 템플릿에 버튼이 없는경우 제거하시기 바랍니다.
        );



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

        // 리턴 JSON 문자열 확인
        print_r($ret . PHP_EOL);

        // JSON 문자열 배열 변환
        $retArr = json_decode($ret);
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

        // 리턴 JSON 문자열 확인
        print_r($ret . PHP_EOL);

        // JSON 문자열 배열 변환
        $retArr = json_decode($ret);

        // 결과값 출력
        print_r($retArr);

        /*
        code : 0 성공, 나머지 숫자는 에러
        message : 결과 메시지
        */
    }


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
