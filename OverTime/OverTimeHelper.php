<?php
require("../tool/dbconfig.php");
require("../test.php");
if($_POST&&$_GET['ChatUser']){
    // echo "申請人".$_GET['ChatUser'].'<br>';
    // echo "需求日期".$_POST['need_date'].'<br>';
    // echo "開始時間".$_POST['need_time1'].'<br>';
    // echo "結束時間".$_POST['need_time2'].'<br>';
    // echo "申請時數".$_POST['need_time3'].'<br>';
    // echo "工作內容".$_POST['need_comm'].'<br>';
    // echo "送簽對象".$_POST['to_select'].'<br>';
    
    $sql = "INSERT INTO OverTime("
    ."[OverTime_ON_TIME_1]
      ,[OverTime_OFF_TIME_1]
      ,[OverTime_ON_TIME_2]
      ,[OverTime_OFF_TIME_2]
      ,[OverTime_ON_TIME_3]
      ,[OverTime_OFF_TIME_3]
      ,[OverTime_WORK_NOS]
      ,[OverTime_REAL_NOS]
      ,[OverTime_NEED_DATE]
      ,[OverTime_NEED_TIME_1]
      ,[OverTime_NEED_TIME_2]
      ,[OverTime_NEED_TIME_3]
      ,[OverTime_NEED_TYPE]
      ,[OverTime_NEED_COMM]
      ,[OverTime_NEED_USER]
      ,[OverTime_REQUEST_DATE]
      ,[OverTime_TO]
      ,[OverTime_TO_List]
      ,[OverTime_TO_Tag]
      ,[OverTime_Check]
      "
    .") "
    . "VALUES("
    ."''"
    .",''"
    .",''"
    .",''"
    .",''"
    .",''"
    .",''"
    .",''"
    .",'".$_POST['need_date']."'"
    .",'".$_POST['need_time1']."'"
    .",'".$_POST['need_time2']."'"
    .",'".$_POST['need_time3']."'"
    .",'補休'"
    .",'".$_POST['need_comm']."'"
    .",'".$_GET['ChatUser']."'"
    .",'".date("Y/m/d")."'"
    .",'".$_POST['to_select']."'"
    .",'".$_POST['to_select'].",".$_POST['to_select2']."'"
    .",'0','0'"
    .")";
    $statement = $pdo->exec($sql);

    // 寫入WorkFlow資料庫
    $REQUEST_ID=$pdo->lastInsertId();
    $sql = "INSERT INTO WorkFlow("
    ."[REQUEST_ID]
      ,[WorkFlow_TYPE]
      ,[WorkFlow_DATE]
      ,[WorkFlow_COMP]
      ,[WorkFlow_CLASS]
      ,[WorkFlow_USER]
      ,[WorkFlow_OK_STATUS]
      "
    .") "
    . "VALUES("
    ."'".$REQUEST_ID."'"
    .",'1'"
    .",'".date("Y/m/d")."'"
    .",''"
    .",''"
    .",'".$_GET['ChatUser']."'"
    .",'待簽核'"
    .")";
    $statement = $pdo->exec($sql);
    if ($statement == 1) {
      
      $t=new Notif_Chat($_POST['to_select']);
      $tt=$t->Trigger_Chat($_POST['to_select']);
        echo "<script> alert('加班申請已送出');parent.location.href=\"../OverTime.php?ChatUser=".$_GET['ChatUser']."\"; </script>";
    } else {
        // echo "<script> alert('加班申請失敗，請重新申請或來電資訊部');parent.location.href=\"../OverTime.php?ChatUser=".$_GET['ChatUser']."\"; </script>";
        echo "系統異常，請來電資訊部!!"."<br>";
        echo $sql;
    }
}

