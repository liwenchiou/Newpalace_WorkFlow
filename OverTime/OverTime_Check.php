<?php
require_once('../includes.php');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script>
        function check(o) {
            var timeReg = /^([01][0-9]|2[0-3])[0-5][0-9]$/;
            var t = o.value;
            if (t.match(timeReg)) {
                t = t.substr(0, 2) + ":" + t.substr(2, 2);
                o.value = t;
            }
            if (document.getElementById("need_time1").value != "" && document.getElementById("need_time2").value != "") {
                var t1 = document.getElementById("need_time1").value.split(":");
                var t2 = document.getElementById("need_time2").value.split(":");
                if (t1[1] != 0 && t1[1] != 30) {
                    alert("需求時間起 輸入錯誤，請輸入整點或是30分")
                    document.getElementById("need_time1").value = "";
                    document.getElementById("need_time1").focus();
                    return false;
                }
                if (t2[1] != 0 && t2[1] != 30) {
                    alert("需求時間迄 輸入錯誤，請輸入整點或是30分")
                    document.getElementById("need_time2").value = "";
                    document.getElementById("need_time2").focus();
                    return false;
                }

                var sum = ((t2[0] - t1[0]) * 60) + (t2[1] - t1[1]);
                document.getElementById("need_time3").value = sum;
            } else {
                return false;
            }
        }
    </script>
</head>

<body>

    <div class="container">
        <h3 class="bg-success text-center">加班申請列表</h3>
        <table id="tbl" class="table table-striped table-bordered table-hover">
            <tr style="text-align:center;">
                <td><b>申請人</b></td>
                <!-- <td><b>申請日期</b></td> -->
                <td><b>需求日期</b></td>
                <td><b>上班/下班</b></td>
                <td><b>上班/下班</b></td>
                <td><b>上班/下班</b></td>
                <td><b>申請時數</b></td>
                <td><b>工作內容</b></td>
                <td><b>簽核人</b></td>
                <td><b>建檔狀態</b></td>
            </tr>
            <?php
            // $sql = "SELECT b.OverTime_ID,b.OverTime_NEED_USER,b.OverTime_REQUEST_DATE,b.OverTime_NEED_DATE,b.OverTime_NEED_TIME_1,b.OverTime_NEED_TIME_2,b.OverTime_NEED_TIME_3"
            //     . " ,b.OverTime_NEED_COMM,a.WorkFlow_ID,b.OverTime_Check"
            //     . " FROM [NPG].[dbo].[WorkFlow] a,[NPG].[dbo].[OverTime] b"
            //     . "  where a.WorkFlow_OK_STATUS='核准'"
            //     . "  and a.REQUEST_ID=b.OverTime_ID";
            $sql = "SELECT b.OverTime_ID,b.OverTime_NEED_USER,b.OverTime_REQUEST_DATE,b.OverTime_NEED_DATE,b.OverTime_NEED_TIME_1,b.OverTime_NEED_TIME_2,b.OverTime_NEED_TIME_3"
                . ",b.OverTime_NEED_COMM,a.WorkFlow_ID,b.OverTime_Check"
                . ",c.on_time_1,c.off_time_1,c.on_time_2,c.off_time_2,c.on_time_3,c.off_time_3"
                . " FROM [NPG].[dbo].[WorkFlow] a,[NPG].[dbo].[OverTime] b,[NPG].[dbo].[Attendance_Abnormal] c"
                . " where a.WorkFlow_OK_STATUS='核准'"
                . " and a.REQUEST_ID=b.OverTime_ID"
                . " and b.OverTime_NEED_USER=c.pers_cod"
                . " and b.OverTime_Check='0'"
                . " and convert(date,b.OverTime_NEED_DATE,111)=convert(date,c.attnddat,111)";
            $rs = $pdo->query($sql);
            $result_arr = $rs->fetchAll();
            $rs = null;
            for ($i = 0; $i < count($result_arr); $i++) {
                $sql2 = "SELECT [WorkFlow_Detail_USER]"
                    . " FROM [NPG].[dbo].[WorkFlow_Detail]"
                    . " where [WorkFlow_ID]='" . $result_arr[$i]['WorkFlow_ID'] . "'";
                $rs2 = $pdo->query($sql2);
                $result_arr2 = $rs2->fetchAll();
                $rs2 = null;
                $OK_man = '';
                for ($j = 0; $j < count($result_arr2); $j++) {
                    $sql = "select pers_nam from pers_mn where Comp_cod!='NPG' and pers_cod='" . $result_arr2[$j]['WorkFlow_Detail_USER'] . "'";
                    $rs = $pdo2->query($sql);
                    $Need_User_Name_tmp = $rs->fetchAll();
                    $Need_User_Name = Trim($Need_User_Name_tmp[0]['PERS_NAM']);
                    $OK_man .= $Need_User_Name . ',';
                }
                $sql = "select pers_nam from pers_mn where Comp_cod!='NPG' and pers_cod='" . $result_arr[$i]['OverTime_NEED_USER'] . "'";
                $rs = $pdo2->query($sql);
                $Need_User_Name_tmp = $rs->fetchAll();
                $Need_User_Name = Trim($Need_User_Name_tmp[0]['PERS_NAM']);
                echo '<tr style="text-align:center;">';
                echo '<td><b>' . $Need_User_Name . '</b></td>';
                // echo '<td><b>' . $result_arr[$i]['OverTime_REQUEST_DATE'] . '</b></td>';
                echo '<td><b>' . $result_arr[$i]['OverTime_NEED_DATE'] . '</b></td>';
                echo '<td><b>' . $result_arr[$i]['on_time_1'] . '/' . $result_arr[$i]['off_time_1'] . '</b></td>';
                echo '<td><b>' . $result_arr[$i]['on_time_2'] . '/' . $result_arr[$i]['off_time_2'] . '</b></td>';
                echo '<td><b>' . $result_arr[$i]['on_time_3'] . '/' . $result_arr[$i]['off_time_3'] . '</b></td>';
                echo '<td><b>' . $result_arr[$i]['OverTime_NEED_TIME_3'] . '</b></td>';
                echo '<td><b>' . $result_arr[$i]['OverTime_NEED_COMM'] . '</b></td>';
                echo '<td><b>' . $OK_man . '</b></td>';
                $status = $result_arr[$i]['OverTime_Check'] == '0' ? '未建檔' : '已建檔';
                if ($status == '未建檔') {
                    $status_button = '<input type="button" value="' . $status . '"  onclick="javascript:location.href=\'../tool/CheckHelper.php?type=0&&OverTime_ID=' . $result_arr[$i]['OverTime_ID'] . '\'">';
                } else {
                    $status_button = '已建檔';
                }
                echo '<td>' . $status_button;
                echo  '<input type="button" value="人事退回"  onclick="javascript:location.href=\'../tool/CheckHelper.php?type=1&&OverTime_ID=' . $result_arr[$i]['OverTime_ID'] . '\'">' . '</td>';
                echo '</tr>';
            }
            function GetName($pers_cod)
            {
                require_once('../tool/dbconfig.php');
                $sql = "select pers_nam from pers_mn where Comp_cod!='NPG' and pers_cod='" . $pers_cod . "'";
                echo $sql;
                $rs = $pdo2->query($sql);
                $result_arr = $rs->fetchAll();
                $rs = null;
                for ($i = 0; $i < count($result_arr); $i++) {
                    //   echo "<div>" . $result_arr[$i]['ipconfig_name'] . "<br>" . $result_arr[$i]['ipconfig_ip']. "<br>" . $result_arr[$i]['ipconfig_add'] . "<br>" . $result_arr[$i]['ipconfig_updata']  . "</div>";
                    return $result_arr[$i]['PERS_NAM'];
                }
            }
            ?>
        </table>
    </div>
</body>

</html>