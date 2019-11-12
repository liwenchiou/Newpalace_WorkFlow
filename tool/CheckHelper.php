<?PHP
include 'dbconfig.php';
include '../test.php';
if ($_GET['OverTime_ID'] && $_GET['type'] == '0') {
    // echo '啟用';
    $sql = "select * from OverTime where OverTime_ID='" . $_GET['OverTime_ID'] . "'";
    $rs = $pdo->query($sql);
    $result_arr = $rs->fetchAll();
    $status_tmp = $result_arr[0]['OverTime_Check'] == '0' ? '1' : '0';
    $sql = "UPDATE OverTime SET OverTime_Check ='" . $status_tmp . "' WHERE OverTime_ID ='" . $_GET['OverTime_ID'] . "'";
    // echo $sql;
    $statement = $pdo->exec($sql);
    if ($statement == 1) {
        echo "<script>alert('回寫入資料庫!!'); location.href = '../OverTime/OverTime_Check.php'</script>";
    } else {
        echo "<script>alert('狀態改變失敗!!'); location.href = '../OverTime/OverTime_Check.php'</script>";
    }
} else if ($_GET['OverTime_ID'] && $_GET['type'] == '1') {
    $sql = "select * from OverTime where OverTime_ID='" . $_GET['OverTime_ID'] . "'";
    $rs = $pdo->query($sql);
    $result_arr = $rs->fetchAll();
    $status_tmp = $result_arr[0]['OverTime_Check'] == '0' ? '1' : '0';
    $sql = "UPDATE OverTime SET OverTime_Check ='" . $status_tmp . "' WHERE OverTime_ID ='" . $_GET['OverTime_ID'] . "'";
    // echo $sql;
    $statement = $pdo->exec($sql);

    // 更新 WorkFlow 資料庫
    $sql = "UPDATE WorkFlow SET WorkFlow_OK_STATUS=?, WorkFlow_OK_DATE=?, WorkFlow_OK_COMM=? WHERE REQUEST_ID=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['退回',  date("Y/m/d"), '人事退回', $_GET['OverTime_ID']]);

    //取得申請人
    $sql = "select [OverTime_NEED_USER]
    from [WorkFlow],[OverTime]
    where [WorkFlow].REQUEST_ID=[OverTime].OverTime_ID
    and [WorkFlow_TYPE]='1'
    and [WorkFlow].REQUEST_ID='" . $_GET['OverTime_ID'] . "'";
    $rs = $pdo->query($sql);
    $NEED_USER_tmp = $rs->fetchAll();
    $NEED_USER = Trim($NEED_USER_tmp[0]['OverTime_NEED_USER']);
    $rs = null;
    $t = new Notif_Chat($NEED_USER);
    $tt = $t->Trigger_Chat_Reback($NEED_USER);

    echo "<script> alert('加班申請已退回');parent.location.href = '../OverTime/OverTime_Check.php'</script>";
}
