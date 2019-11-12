<?php
include("dbconfig.php");
class Tool{

public function GetName($pers_cod){
    $sql = "select pers_nam from pers_mn where Comp_cod!='NPG' and pers_cod='".$pers_cod."'";
    //$v=$this->debug_to_alert($sql);
    $rs = $pdo2->query($sql);
    
    $result_arr = $rs->fetchAll();
    $rs= null;
    for ($i = 0; $i < count($result_arr); $i++) {
          //   echo "<div>" . $result_arr[$i]['ipconfig_name'] . "<br>" . $result_arr[$i]['ipconfig_ip']. "<br>" . $result_arr[$i]['ipconfig_add'] . "<br>" . $result_arr[$i]['ipconfig_updata']  . "</div>";
            return $result_arr[$i]['PERS_NAM'];
          }
}
public function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}
public static function debug_to_alert($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>alert('".$output."');</script>";
}
}