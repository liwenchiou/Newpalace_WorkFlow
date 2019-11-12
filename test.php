<?php
class Notif_Chat
{
    public $User_id;

    public function __construct($ChatUser)
    {
        //連接帳號資料表，檢查帳密是否正確
        // $db_server = "sql200.web.youp.ga:3306"; //資料庫主機位置
        $db_user = 'sa'; //資料庫的使用帳號
        $db_password = 'esum'; //資料庫的使用密碼
        $db_name = 'NPG'; //資料庫名稱
        //PDO的連接語法
        try {
            //$pdo = new PDO("mysql:host=$db_server;dbname=$db_name", $db_user, $db_password);
            //測試用
            $pdo = new PDO("sqlsrv:Server=192.168.2.111,49227;Database=NPG", $db_user, $db_password);
            //設定為utf8編碼，必要設定
            $pdo->query('SET NAMES "utf8"');
            date_default_timezone_set('Asia/Taipei');
        } catch (PDOException $e) {
            // 資料庫連結失敗
            echo '資料庫連線錯誤，請和技術人員聯繫';
        }
        $sql = "select WorkFlow_ChatUser_UserID from WorkFlow_ChatUser where WorkFlow_ChatUser_PersCod='" . $ChatUser . "'";
        $rs = $pdo->query($sql);
        $Need_User_Name_tmp = $rs->fetchAll();
        $Need_User_Name = Trim($Need_User_Name_tmp[0]['WorkFlow_ChatUser_UserID']);
        $rs = null;
        @$this->$User_id = $Need_User_Name;
    }
    public function Trigger_Chat($ChatUser)
    {
        $useragent = 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)';
        @$payload = 'payload={"text": "有新的加班申請單待審核，請點選以下連結進行簽核 http://srv.newpalace.com.tw/Newpalace_WorkFlow/WorkFlow.php?ChatUser=' . $ChatUser . '", "dsm_uids": [' . $this->$User_id . '],"token":"Jr94Va5TgFY1HL6i8o7Y1mVocskMKSJuMFtZKOdHbJIuPQtt2JzotUOZqQW4KTwU" }';
        //note 不可用https:// chat不正常
        // $url = 'http://newp.synology.me/webapi/entry.cgi?api=SYNO.Chat.External&method=chatbot&version=2&token=%22Jr94Va5TgFY1HL6i8o7Y1mVocskMKSJuMFtZKOdHbJIuPQtt2JzotUOZqQW4KTwU%22';
        //$url = "http://cloud.newpalace.com.tw/webapi/entry.cgi?api=SYNO.Chat.External&method=chatbot&version=2&token=%22Z9lZdfmxkY86rFyBBP9ULCN2b1pMQeMp5DGx48bhIv9B6gy2S8w9dsqBsRwZeBe0%22";
        $url="http://cloud.newpalace.com.tw/webapi/entry.cgi?api=SYNO.Chat.External&method=chatbot&version=2&token=%22qfvRBOL9NAREVJcTIdlLBB7zdxkMLfriCI6Bx2sHjJEQJKdWiXOmLVEJmSU2vZD0%22";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent); //set our user agent
        curl_setopt($ch, CURLOPT_POST, true); //set how many paramaters to post
        curl_setopt($ch, CURLOPT_URL, $url); //set the url we want to use
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_exec($ch); //execute and get the results
        curl_close($ch);
    }
    public function Trigger_Chat_Reback($ChatUser)
    {
        $useragent = 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)';
        @$payload = 'payload={"text": "有新的加班申請單審核進度，請點選以下連結進行確認 http://srv.newpalace.com.tw/Newpalace_WorkFlow/OverTime.php?ChatUser=' . $ChatUser . '", "dsm_uids": [' . $this->$User_id . '],"token":"Jr94Va5TgFY1HL6i8o7Y1mVocskMKSJuMFtZKOdHbJIuPQtt2JzotUOZqQW4KTwU" }';
        //note 不可用https:// chat不正常
        // $url = 'http://newp.synology.me/webapi/entry.cgi?api=SYNO.Chat.External&method=chatbot&version=2&token=%22Jr94Va5TgFY1HL6i8o7Y1mVocskMKSJuMFtZKOdHbJIuPQtt2JzotUOZqQW4KTwU%22';
        $url = "http://cloud.newpalace.com.tw/webapi/entry.cgi?api=SYNO.Chat.External&method=chatbot&version=2&token=%22Z9lZdfmxkY86rFyBBP9ULCN2b1pMQeMp5DGx48bhIv9B6gy2S8w9dsqBsRwZeBe0%22";
        $url="http://cloud.newpalace.com.tw/webapi/entry.cgi?api=SYNO.Chat.External&method=chatbot&version=2&token=%22qfvRBOL9NAREVJcTIdlLBB7zdxkMLfriCI6Bx2sHjJEQJKdWiXOmLVEJmSU2vZD0%22";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent); //set our user agent
        curl_setopt($ch, CURLOPT_POST, true); //set how many paramaters to post
        curl_setopt($ch, CURLOPT_URL, $url); //set the url we want to use
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_exec($ch); //execute and get the results
        curl_close($ch);
    }
}
