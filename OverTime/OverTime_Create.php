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
                    document.getElementById("need_time1").value="";
                    document.getElementById("need_time1").focus();
                    return false;
                }
                if (t2[1] != 0 && t2[1] != 30) {
                    alert("需求時間迄 輸入錯誤，請輸入整點或是30分")
                    document.getElementById("need_time2").value="";
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
<h3 class="bg-success text-center">新增加班申請</h3>
<?php 
echo "<a class=\"btn btn-primary\" href=\"../OverTime.php?ChatUser=".$_GET['ChatUser']."\">取消申請</a>";
echo "<form role=\"form\" method=\"post\" action=\"OverTimeHelper.php?ChatUser=".$_GET['ChatUser']."\">"
?>
      
        <div class="form-group">
          <label for="need_date">需求日期:</label>
          <input type="date" class="form-control" id="need_date" name="need_date" placeholder="請選擇需求日期">
        </div>
        <div class="form-group">
          <label for="need_time1">開始時間:</label>
          <input type="text" class="form-control" id="need_time1"  name="need_time1" placeholder="請輸入開始時間" onblur="check(this);">
        </div>
        <div class="form-group">
          <label for="need_time2">結束時間:</label>
          <input type="text" class="form-control" id="need_time2"  name="need_time2" placeholder="請輸入結束時間" onblur="check(this);">
        </div>
        <div class="form-group">
          <label for="need_time3">申請時數:</label>
          <input type="text" class="form-control" id="need_time3" name="need_time3" placeholder="請輸入申請時數">
        </div>
        <div class="form-group">
          <label for="need_comm">工作內容:</label>
          <input type="text" class="form-control" id="need_comm" name="need_comm" placeholder="請輸入工作內容">
        </div>
        <div class="form-group">
          <label for="to_select">簽核人一:</label>
          <select class="form-control" id="to_select" name="to_select">
          <option value="N">無</option>
          <!-- 取得簽核人選單 -->
            <?php
                require_once('../tool/dbconfig.php');
                $sql = "select pers_cod,pers_nam from pers_mn where Comp_cod='ACC' and JOB_STA in ('N','T') order by pers_cod";
                $rs = $pdo2->query($sql);
                $result_arr = $rs->fetchAll();
                $rs= null;
                for ($i = 0; $i < count($result_arr); $i++) {
                      //   echo "<div>" . $result_arr[$i]['ipconfig_name'] . "<br>" . $result_arr[$i]['ipconfig_ip']. "<br>" . $result_arr[$i]['ipconfig_add'] . "<br>" . $result_arr[$i]['ipconfig_updata']  . "</div>";
                        echo "<option value=\"".trim($result_arr[$i]['PERS_COD'])."\">".trim($result_arr[$i]['PERS_NAM'])."</option>";
                      }
            ?>
          </select>
        </div>
        <div class="form-group">
          <label for="to_select">>簽核人二:</label>
          <select class="form-control" id="to_select2" name="to_select2">
          <option value="N">無</option>
          <!-- 取得簽核人選單 -->
            <?php
                require_once('../tool/dbconfig.php');
                $sql = "select pers_cod,pers_nam from pers_mn where Comp_cod='ACC' and JOB_STA in ('N','T') order by pers_cod";
                $rs = $pdo2->query($sql);
                $result_arr = $rs->fetchAll();
                $rs= null;
                for ($i = 0; $i < count($result_arr); $i++) {
                      //   echo "<div>" . $result_arr[$i]['ipconfig_name'] . "<br>" . $result_arr[$i]['ipconfig_ip']. "<br>" . $result_arr[$i]['ipconfig_add'] . "<br>" . $result_arr[$i]['ipconfig_updata']  . "</div>";
                        echo "<option value=\"".trim($result_arr[$i]['PERS_COD'])."\">".trim($result_arr[$i]['PERS_NAM'])."</option>";
                      }
            ?>
          </select>
        </div>
        <button type="submit" class="btn btn-primary">送出</button>
      </form>
    </div>
</body>
</html>

