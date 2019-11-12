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
    <title>加班單申請</title>
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
</head>
<body>
<div class="container">
<h3 class="bg-success text-center">加班申請單</h3>
<div class="container">
<?php 
echo "<a class=\"btn btn-primary\" href=\"OverTime/OverTime_Create.php?ChatUser=".$_GET['ChatUser']."\">建立加班申請單</a>";
?>

<ul>
  <li class="thead">
    <ol class="tr">
      <li>申請單號</li>
      <li>申請日期</li>
      <li>需求日期</li>
      <li>開始時間</li>
      <li>結束時間</li>
      <li>申請時數</li>
      <li>工作內容</li>
      <li>簽核狀態</li>
      <li>簽核備註</li>
    </ol>
  </li>
  <li class="tbody">

    <!-- 列出加班申請清冊 -->
  <?php
    $sql = "select * from OverTime,WorkFlow
    where OverTime.OverTime_ID=WorkFlow.REQUEST_ID
    and OverTime.OverTime_NEED_USER='".$_GET['ChatUser']."'
    and WorkFlow_TYPE='1'
    order by OverTime_ID desc";
    $rs = $pdo->query($sql);
    $result_arr = $rs->fetchAll();
    $rs= null;
    for ($i = 0; $i < count($result_arr); $i++) {
          //   echo "<div>" . $result_arr[$i]['ipconfig_name'] . "<br>" . $result_arr[$i]['ipconfig_ip']. "<br>" . $result_arr[$i]['ipconfig_add'] . "<br>" . $result_arr[$i]['ipconfig_updata']  . "</div>";
            echo "<ol class=\"tr\">";
            echo "<li data-title=\"申請單號\">". $result_arr[$i]['OverTime_ID'] ."</li>";
            echo "<li data-title=\"申請日期\">". $result_arr[$i]['OverTime_REQUEST_DATE'] ."</li>";
            echo "<li data-title=\"需求日期\">". $result_arr[$i]['OverTime_NEED_DATE'] ."</li>";
            echo "<li data-title=\"開始時間\">". $result_arr[$i]['OverTime_NEED_TIME_1'] ."</li>";
            echo "<li data-title=\"結束時間\">". $result_arr[$i]['OverTime_NEED_TIME_2'] ."</li>";
            echo "<li data-title=\"申請時數\">". $result_arr[$i]['OverTime_NEED_TIME_3'] ."</li>";
            echo "<li data-title=\"工作內容\">". $result_arr[$i]['OverTime_NEED_COMM'] ."</li>";
            echo "<li data-title=\"簽核狀態\">". $result_arr[$i]['WorkFlow_OK_STATUS'] ."</li>";
            echo "<li data-title=\"簽核備註\">". $result_arr[$i]['WorkFlow_OK_COMM'] ."</li>";
            echo "</ol>";
          }
 
    
  ?>

  </li>
</ul>
</table>
</div>
</body>
</html>
