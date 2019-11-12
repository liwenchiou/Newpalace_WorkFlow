<?php
require_once('../includes.php');
require("../test.php");
// echo $_GET['ChatUser'].'<br>';
// echo $_GET['WFID'].'<br>';
// echo $_GET['type'].'<br>';

// 核准
if ($_GET['type'] == "1") {
    // echo "核准";
    // 寫入WorkFlow_detail資料庫
    $sql = "INSERT INTO WorkFlow_Detail("
        . "[WorkFlow_ID]
  ,[WorkFlow_Detail_USER]
  ,[WorkFlow_Detail_STATUS]
  ,[WorkFlow_Detail_DateTime]"
        . ") "
        . "VALUES("
        . "'" . $_GET['WFID'] . "'"
        . ",'" . $_GET['ChatUser'] . "'"
        . ",'核准'"
        . ",'" . date("Y-m-d H:i:s") . "'"
        . ")";
    $statement = $pdo->exec($sql);

    //看有沒有下一個
    //取得加班單號
    $sql = "select [REQUEST_ID]
    from [WorkFlow]
    where WorkFlow_ID='" . $_GET['WFID'] . "'";
    $rs = $pdo->query($sql);
    $REQUEST_ID_tmp = $rs->fetchAll();
    $REQUEST_ID = Trim($REQUEST_ID_tmp[0]['REQUEST_ID']);
    $rs = null;
    //取得申請人列表及tag
    $sql = "select [OverTime_NEED_USER],[OverTime_TO_List],[OverTime_TO_Tag]
    from [WorkFlow],[OverTime]
    where [WorkFlow].REQUEST_ID=[OverTime].OverTime_ID
    and [WorkFlow_TYPE]='1'
    and [WorkFlow].REQUEST_ID='" . $REQUEST_ID . "'";
    $rs = $pdo->query($sql);
    $NEED_USER_tmp = $rs->fetchAll();
    $NEED_USER = Trim($NEED_USER_tmp[0]['OverTime_NEED_USER']);
    $rs = null;
    $over_tag = Trim($NEED_USER_tmp[0]['OverTime_TO_Tag']);
    $person = explode(",", Trim($NEED_USER_tmp[0]['OverTime_TO_List']));
    $next = $over_tag + 1;
   
    if ($next < count($person)) {
        if ($person[$next] != "N") {
            $sql = "UPDATE OverTime SET OverTime_TO_Tag=?, OverTime_TO=? WHERE OverTime_ID=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$next, $person[$next], $REQUEST_ID]);
            $t = new Notif_Chat($person[$next]);
            $tt = $t->Trigger_Chat($person[$next]);
            echo "<script> alert('加班申請已核准');parent.location.href=\"../WorkFlow.php?ChatUser=" . $_GET['ChatUser'] . "\"; </script>";
            exit;
        }
    }
    $sql = "UPDATE WorkFlow SET WorkFlow_OK_STATUS=?, WorkFlow_OK_PERSON=?, WorkFlow_OK_DATE=? WHERE WorkFlow_ID=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['核准', $_GET['ChatUser'], date("Y/m/d"), $_GET['WFID']]);
        if ($statement == 1) {
            //取得加班單號
            $sql = "select [REQUEST_ID]
            from [WorkFlow]
            where WorkFlow_ID='" . $_GET['WFID'] . "'";
            $rs = $pdo->query($sql);
            $REQUEST_ID_tmp = $rs->fetchAll();
            $REQUEST_ID = Trim($REQUEST_ID_tmp[0]['REQUEST_ID']);
            $rs = null;
            //取得申請人
            $sql = "select [OverTime_NEED_USER]
            from [WorkFlow],[OverTime]
            where [WorkFlow].REQUEST_ID=[OverTime].OverTime_ID
            and [WorkFlow_TYPE]='1'
            and [WorkFlow].REQUEST_ID='" . $REQUEST_ID . "'";
            $rs = $pdo->query($sql);
            $NEED_USER_tmp = $rs->fetchAll();
            $NEED_USER = Trim($NEED_USER_tmp[0]['OverTime_NEED_USER']);
            $rs = null;
            $t = new Notif_Chat($NEED_USER);
            $tt = $t->Trigger_Chat_Reback($NEED_USER);
            echo "<script> alert('加班申請已核准');parent.location.href=\"../WorkFlow.php?ChatUser=" . $_GET['ChatUser'] . "\"; </script>";
        } else {
            // echo "<script> alert('加班申請失敗，請重新申請或來電資訊部');parent.location.href=\"../OverTime.php?ChatUser=".$_GET['ChatUser']."\"; </script>";
            echo $sql;
        }
    // 退回
} else if ($_GET['type'] == "2") {
    //  echo "退回";
    // 寫入WorkFlow_detail資料庫
    $sql = "INSERT INTO WorkFlow_Detail("
        . "[WorkFlow_ID]
  ,[WorkFlow_Detail_USER]
  ,[WorkFlow_Detail_STATUS]
  ,[WorkFlow_Detail_DateTime]
  ,[WorkFlow_Detail_Comm]"
        . ") "
        . "VALUES("
        . "'" . $_GET['WFID'] . "'"
        . ",'" . $_GET['ChatUser'] . "'"
        . ",'退回'"
        . ",'" . date("Y-m-d H:i:s") . "'"
        . ",'" . $_GET['comm'] . "'"
        . ")";
    $statement = $pdo->exec($sql);

    // 更新 WorkFlow 資料庫
    $sql = "UPDATE WorkFlow SET WorkFlow_OK_STATUS=?, WorkFlow_OK_PERSON=?, WorkFlow_OK_DATE=?, WorkFlow_OK_COMM=? WHERE WorkFlow_ID=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['退回', $_GET['ChatUser'], date("Y/m/d"), $_GET['comm'], $_GET['WFID']]);

    if ($statement == 1) {

        //取得加班單號
        $sql = "select [REQUEST_ID]
    from [WorkFlow]
    where WorkFlow_ID='" . $_GET['WFID'] . "'";
        $rs = $pdo->query($sql);
        $REQUEST_ID_tmp = $rs->fetchAll();
        $REQUEST_ID = Trim($REQUEST_ID_tmp[0]['REQUEST_ID']);
        $rs = null;
        //取得申請人
        $sql = "select [OverTime_NEED_USER]
    from [WorkFlow],[OverTime]
    where [WorkFlow].REQUEST_ID=[OverTime].OverTime_ID
    and [WorkFlow_TYPE]='1'
    and [WorkFlow].REQUEST_ID='" . $REQUEST_ID . "'";
        $rs = $pdo->query($sql);
        $NEED_USER_tmp = $rs->fetchAll();
        $NEED_USER = Trim($NEED_USER_tmp[0]['OverTime_NEED_USER']);
        $rs = null;
        $t = new Notif_Chat($NEED_USER);
        $tt = $t->Trigger_Chat_Reback($NEED_USER);

        echo "<script> alert('加班申請已退回');parent.location.href=\"../WorkFlow.php?ChatUser=" . $_GET['ChatUser'] . "\"; </script>";
    } else {
        // echo "<script> alert('加班申請失敗，請重新申請或來電資訊部');parent.location.href=\"../OverTime.php?ChatUser=".$_GET['ChatUser']."\"; </script>";
        echo "系統異常，請來電資訊部!!" . "<br>";
        echo $sql;
    }
}
