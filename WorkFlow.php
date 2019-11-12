<?php
require_once 'includes.php';
if(!$_GET['ChatUser']){
  echo "<script>"."alert('登入方式錯誤，請使用Chat登入!!');"."</script>";
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>電子簽核平台</title>
    <style>
*{
  padding: 0;
  margin: 0;
  list-style: none;
  box-sizing: border-box;
}
ul{
  width: 95%;
  display: block;
  margin: 1em auto;
  border-collapse: collapse;
}
.thead{display: table-header-group;}
.tr{display: table-row;}
.tbody{display: table-row-group;}
.thead li, .tr li{
  display: table-cell;
  padding: 5px;
  border: 1px solid #aaa;
}
.thead li{
  text-align: center;
  font-weight: bold;
  background: #e6f9ff;
}
ol:nth-child(even){
  background: rgba(#6cffd1,.2);
}

@media only screen and (max-width:768px){
  .thead{
    display: none;
  }
  .tr{
    display: block;
    border: #ddd 1px solid;
    margin-bottom: 5px;
  }
  .tr li{
    display: inline-block;
    width: 95%;
    border: none;
  }
  .tr li:before{
    content: attr(data-title);
    display: inline-block;
    width: auto;
    min-width: 20%;
    font-weight: 900;
    padding-right: 1rem;
  }
}
    </style>
   <script> 
   function reback(ChatUser,WFID){
    var str = window.prompt("請輸入退回原因","") 
    if(str){
      window.location.href = 'WorkFlow/WorkFlowHelper.php?ChatUser='+ChatUser+'&&WFID='+WFID+'&&type=2&&comm='+str;
    }else{
      return false;
    }
   }
</script> 
</head>
<body>
<div class="container">
<h3 class="bg-success text-center">電子簽核平台</h3>
<div class="container">

<ul>
  <li class="thead">
    <ol class="tr">
      <li>申請單號</li>
      <li>申請人</li>
      <li>申請日期</li>
      <li>需求日期</li>
      <li>開始時間</li>
      <li>結束時間</li>
      <li>申請時數</li>
      <li>工作內容</li>
      <li>簽核狀態</li>
    </ol>
  </li>
  <li class="tbody">

    <!-- 列出加班申請清冊 -->
  <?php
    $sql = "select * from OverTime join WorkFlow on OverTime.OverTime_ID=WorkFlow.REQUEST_ID
    and WorkFlow_TYPE='1'
    and OverTime_TO='".$_GET['ChatUser']."'
    and WorkFlow_OK_STATUS='待簽核'";
    $rs = $pdo->query($sql);
    $result_arr = $rs->fetchAll();
    $rs= null;
    for ($i = 0; $i < count($result_arr); $i++) {
          //   echo "<div>" . $result_arr[$i]['ipconfig_name'] . "<br>" . $result_arr[$i]['ipconfig_ip']. "<br>" . $result_arr[$i]['ipconfig_add'] . "<br>" . $result_arr[$i]['ipconfig_updata']  . "</div>";
          $sql="select pers_nam from pers_mn where Comp_cod!='NPG' and pers_cod='".$result_arr[$i]['OverTime_NEED_USER']."'";
          $rs = $pdo2->query($sql);
          $Need_User_Name_tmp= $rs->fetchAll();
          $Need_User_Name=Trim($Need_User_Name_tmp[0]['PERS_NAM']);
          $rs= null;
            echo "<ol class=\"tr\">";
            echo "<li data-title=\"申請單號\">". $result_arr[$i]['OverTime_ID'] ."</li>";
            echo "<li data-title=\"申請人\">". $Need_User_Name ."</li>";
            echo "<li data-title=\"申請日期\">". $result_arr[$i]['OverTime_REQUEST_DATE'] ."</li>";
            echo "<li data-title=\"需求日期\">". $result_arr[$i]['OverTime_NEED_DATE'] ."</li>";
            echo "<li data-title=\"開始時間\">". $result_arr[$i]['OverTime_NEED_TIME_1'] ."</li>";
            echo "<li data-title=\"結束時間\">". $result_arr[$i]['OverTime_NEED_TIME_2'] ."</li>";
            echo "<li data-title=\"申請時數\">". $result_arr[$i]['OverTime_NEED_TIME_3'] ."</li>";
            echo "<li data-title=\"工作內容\">". $result_arr[$i]['OverTime_NEED_COMM'] ."</li>";
            echo "<li data-title=\"簽核狀態\">"
            ."<a class=\"btn btn-primary\" href=\"WorkFlow/WorkFlowHelper.php?ChatUser=".$_GET['ChatUser']."&&WFID=".$result_arr[$i]['WorkFlow_ID']."&&type=1\" onclick=\" return confirm('核准確認')\">核准</a>"
            // ."<a class=\"btn btn-danger\" href=\"WorkFlow/WorkFlowHelper.php?ChatUser=".$_GET['ChatUser']."&&WFID=".$result_arr[$i]['WorkFlow_ID']."&&type=2\" onclick=\" return reback() \">退回</a>"
            ."<a class=\"btn btn-danger\" onclick=\"reback('".$_GET['ChatUser']."','".$result_arr[$i]['WorkFlow_ID']."') \">退回</a>"
            ."</li>";
            echo "</ol>";
          }
    
  ?>

  </li>
</ul>
</table>
</div>
</body>
</html>
