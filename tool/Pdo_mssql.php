<?php

/*
*    Author: AthrunSoft
*    class.pdomysql.php 0.01 2012-4-15
*/
class pdomssql  {
    public static $dbtype = 'mssql';
    public static $dbhost = '192.168.2.111';
    public static $dbport = '49227';
    public static $dbname = 'NPG';
    public static $dbuser = 'sa';
    public static $dbpass = 'esum';
    public static $charset = 'UTF-8';
    public static $stmt = null;
    public static $DB = null;
    public static $connect = true; //是否長連接
   public static $debug = false;
    private static $parms = array();

    /**
     * 構造函數
     */
    // public function __construct() {
    //     self::connect();
    //     self::$DB->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
    //     self::$DB->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
    //     self::execute('SET NAMES ' . self::$charset);
    // }
    // /**
    //  *析構函數
    //  */
    // public function __destruct() {
    //     self::close();
    // }


    /*********************基本方法開始*********************/
    /**
     * 作用:連結資料庫
     */
    public function connect() {
        try {
            $pdo = new PDO("sqlsrv:Server=192.168.2.111,49227;Database=NPG", $this->$dbuser, $this->$dbpass);
            $pdo->query('SET NAMES "utf8"');
            date_default_timezone_set('Asia/Taipei');
        }
        catch (PDOException $e) {
            die("Connect Error Infomation:" . $e->getMessage());
        }
    }

    /**
     *關閉資料連接
     */
    public function close() {
        self::$DB = null;
    }

    /**
     * 對字串進行轉義
     */
    public function quote($str) {
        return self::$DB->quote($str);
    }

    /**
     * 返回一個日期時間字符
     */
    public function now() {
        return date("Y-m-d H:i:s");
    }
    /**
     * 作用:獲取當前庫的所有表名
     * 返回:當前庫的所有表名
     * 類型:陣列
     */
    public function getTablesName() {
        self::$stmt = self::$DB->query('SHOW TABLES FROM ' . self::$dbname);
        $result = self::$stmt->fetchAll(PDO::FETCH_NUM);
        self::$stmt = null;
        return $result;
    }

    /**
     * 作用:獲取資料表裡的欄位
     * 返回:表字段結構
     * 類型:陣列
     */
    public function getFields($table) {
        self::$stmt = self::$DB->query("DESCRIBE $table");
        $result = self::$stmt->fetchAll(PDO::FETCH_ASSOC);
        self::$stmt = null;
        return $result;
    }

    /**
     * 作用:獲得最後INSERT的主鍵ID
     * 返回:最後INSERT的主鍵ID
     * 類型:數字
     */
    public function getLastId() {
        return self::$DB->lastInsertId();
    }

    /**
     *事務開始
     */
    public function autocommit() {
        self::$DB->beginTransaction();
    }

    /**
     *事務提交
     */
    public function commit() {
        self::$DB->commit();
    }

    /**
     *交易復原
     */
    public function rollback() {
        self::$DB->rollback();
    }

    /**
     * 作用:執行INSERTUPDATEDELETE
     * 返回:執行語句影響行數
     * 類型:數字
     */
    public function execute($sql) {
        self::getPDOError($sql);
        return self::$DB->exec($sql);
    }

    /**
     * 獲取要操作的資料
     * 返回:合併後的SQL語句
     * 類型:字串
     */
    private function getCode($table, $args) {
        $code = '';
        if (is_array($args)) {
            foreach ($args as $k => $v) {
                if ($v == '') {
                    continue;
                }
                $code .= "`$k`='$v',";
            }
        }
        $code = substr($code, 0, -1);
        return $code;
    }

    /**
     * 執行具體SQL操作
     * 返回:運行結果
     * 類型:陣列或數位
     */
    private function _fetch($sql, $type) {
        $result = array();
        self::$stmt = self::$DB->query($sql);
        self::getPDOError($sql);
        self::$stmt->setFetchMode(PDO::FETCH_ASSOC);
        switch ($type) {
            case '0':
                $result = self::$stmt->fetch();
                break;
            case '1':
                $result = self::$stmt->fetchAll();
                break;
            case '2':
                if ($sql) {
                    $result = self::$stmt->fetchColumn();
                } elseif (self::$stmt) {
                    $result = self::$stmt->rowCount();
                } else {
                    $result = 0;
                }
                break;
        }
        self::$stmt = null;
        return $result;
    }

    /**
     * 分頁計算
     */
    private function count($pagesize, &$page, &$pagecount, &$recordcount) {
        //獲取記錄行數,計算頁數,以及重新檢查當前頁碼
        $pagecount = ceil($recordcount / $pagesize);
        if ($pagecount > 0) {
            if ($page > $pagecount) {
                $page = $pagecount;
            } elseif ($page < 1) {
                $page = 1;
            }
        } else {
            $page = 1;
        }
    }
    /*********************基本方法結束*********************/


    /*********************Sql操作方法開始*********************/
    /**
     * 作用:插入資料
     * 返回:表內記錄
     * 類型:陣列
     * 參數:$db->insert('$table',array('title'=>'Zxsv'))
     */
    public function add($table, $args) {
        $sql = "INSERT INTO `$table` SET ";
        $code = self::getCode($table, $args);
        $sql .= $code;
        return self::execute($sql);
    }

    /**
     * 修改資料
     * 返回:記錄數
     * 類型:數字
     * 參數:$db->update($table,array('title'=>'Zxsv'),array('id'=>'1'),$where ='id=3');
     */
    public function update($table, $args, $where) {
        $code = self::getCode($table, $args);
        $sql = "UPDATE `$table` SET ";
        $sql .= $code;
        $sql .= " Where $where";
        return self::execute($sql);
    }

    /**
     * 作用:刪除資料
     * 返回:表內記錄
     * 類型:陣列
     * 參數:$db->delete($table,$condition = null,$where ='id=3')
     */
    public function delete($table, $where) {
        $sql = "DELETE FROM `$table` Where $where";
        return self::execute($sql);
    }

    /**
     * 作用:獲取單行資料
     * 返回:表內第一條記錄
     * 類型:陣列
     * 參數:$db->fetOne($table,$condition = null,$field = '*',$where ='')
     */
    public function fetOne($table, $field = '*', $where = false) {
        $sql = "SELECT {$field} FROM `{$table}`";
        $sql .= ($where) ? " WHERE $where" : '';
        return self::_fetch($sql, $type = '0');
    }

    /**
     * 作用:獲取單行資料
     * 返回:表內第一條記錄
     * 類型:陣列
     * 參數:select * from table where id='1'
     */
    public function getOne($sql) {
        return self::_fetch($sql, $type = '0');
    }
    /**
     * 作用:獲取首行首列資料
     * 返回:首行首列欄位值
     * 類型:值
     * 參數:select `a` from table where id='1'
     */
    public function scalar($sql, $fieldname) {
        $row = self::_fetch($sql, $type = '0');
        return $row[$fieldname];
    }
    /**
     * 獲取記錄總數
     * 返回:記錄數
     * 類型:數字
     * 參數:$db->fetRow('$table',$condition = '',$where ='');
     */
    public function fetRowCount($table, $field = '*', $where = false) {
        $sql = "SELECT COUNT({$field}) AS num FROM `$table`";
        $sql .= ($where) ? " WHERE $where" : '';
        return self::_fetch($sql, $type = '2');
    }

    /**
     * 獲取記錄總數
     * 返回:記錄數
     * 類型:數字
     * 參數:select count(*) from table
     */
    public function getRowCount($sql) {
        return self::_fetch($sql, $type = '2');
    }
    /**
     * 作用:獲取所有資料
     * 返回:表內記錄
     * 類型:二維陣列
     * 參數:$db->fetAll('$table',$condition = '',$field = '*',$orderby = '',$limit = '',$where='')
     */
    public function fetAll($table, $field = '*', $orderby = false, $where = false) {
        $sql = "SELECT {$field} FROM `{$table}`";
        $sql .= ($where) ? " WHERE $where" : '';
        $sql .= ($orderby) ? " ORDER BY $orderby" : '';
        return self::_fetch($sql, $type = '1');
    }
    /**
     * 作用:獲取分頁資料
     * 返回:表內記錄
     * 類型:二維陣列
     */
    public function fetPageAll($table, $field = '*', $where = false, $orderby = false,
        $pagesize, &$page, &$pagecount, &$recordcount) {
        $sql = "SELECT {$field} FROM `{$table}`";
        $sql .= ($where) ? " WHERE $where" : '';
        return self::getPageAll($sql, $orderby, $pagesize, $page, $pagecount, $recordcount);
    }
    /**
     * 作用:獲取所有資料
     * 返回:表內記錄
     * 類型:二維陣列
     * 參數:select * from table
     */
    public function getAll($sql) {
        return self::_fetch($sql, $type = '1');
    }

    /**
     * 作用:獲取分頁資料
     * 返回:表內記錄
     * 類型:二維陣列
     */
    public function getPageAll($sql, $orderby = false, $pagesize, &$page, &$pagecount,
        &$recordcount) {
        $sqlcount = "select count(1) as `recordcount` from ($sql) as t;";
        $recordcount = self::scalar($sqlcount, 'recordcount');
        self::count($pagesize, $page, $pagecount, $recordcount);
        $start = ($page - 1) * $pagesize;
        $sql .= ($orderby) ? " ORDER BY $orderby" : '';
        $sql .= " limit $start,$pagesize";
        return self::_fetch($sql, $type = '1');
    }
    /*********************Sql操作方法結束*********************/

    /*********************Pram操作方法開始*********************/
    /**
     * 作用:獲取單行資料
     * 返回:表內第一條記錄
     * 類型:陣列
     */
    public function pramGetOne($sql, $input_parameters) {
        return self::_pramfetch($sql, $input_parameters, $type = '0');
    }
    /**
     * 作用:獲取所有資料
     * 返回:表內記錄
     * 類型:二維陣列
     */
    public function pramGetAll($sql, $input_parameters) {
        return self::_pramfetch($sql, $input_parameters, $type = '1');
    }
    /**
     * 作用:執行帶參數SQL操作
     * 返回:執行語句影響行數
     * 類型:數字
     */
    public function pramExecute($sql, $input_parameters) {
        return self::_pramfetch($sql, $input_parameters, $type = '2');
    }
    /**
     * 作用:獲取首行首列資料
     * 返回:首行首列欄位值
     * 類型:值
     */
    public function pramScalar($sql, $input_parameters, $fieldname) {
        $row = self::_pramfetch($sql, $input_parameters, $type = '0');
        return $row[$fieldname];
    }
    /**
     * 執行帶參數SQL操作
     * 返回:運行結果
     * 類型:陣列或數位
     */
    private function _pramfetch($sql, $input_parameters, $type) {
        $result = array();
        self::$stmt = self::$DB->prepare($sql);
        self::getPDOError($sql);
        self::$stmt->execute($input_parameters);
        self::getSTMTError($sql);
        self::$stmt->setFetchMode(PDO::FETCH_ASSOC);
        switch ($type) {
            case '0':
                $result = self::$stmt->fetch();
                break;
            case '1':
                $result = self::$stmt->fetchAll();
                break;
            case '2':
                if (self::$stmt) {
                    $result = self::$stmt->rowCount();
                } else {
                    $result = 0;
                }
                break;
        }
        self::$stmt = null;
        return $result;
    }
    /*********************Pram操作方法結束*********************/


    /*********************Proc操作方法開始*********************/
    /**
     * 添加參數
     */
    public function pramadd($parameter, $variable, $data_type, $length) {
        array_push(self::$parms, array($parameter, $variable, $data_type, $length));
    }
    /**
     * 清除所有參數
     */
    public function pramclear() {
        self::$parms = array();
    }
    /**
     * 作用:獲取單行資料
     * 返回:表內第一條記錄
     * 類型:陣列
     */
    public function procGetOne($sql) {
        return self::_procfetch($sql, $type = '0');
    }
    /**
     * 作用:獲取所有資料
     * 返回:表內記錄
     * 類型:二維陣列
     */
    public function procGetAll($sql) {
        return self::_procfetch($sql, $type = '1');
    }
    /**
     * 作用:執行一個存儲過程
     * 返回:執行語句影響行數
     * 類型:數字
     */
    public function procExecute($sql) {
        return self::_procfetch($sql, $type = '2');
    }
    /**
     * 作用:獲取out型的return
     */
    public function getReturn() {
        return self::scalar("select @ireturn AS iReturn", "iReturn");
    }
    /**
     * 執行帶參數SQL操作
     * 返回:運行結果
     * 類型:陣列或數位
     */
    private function _procfetch($sql, $type) {
        $result = array();
        self::$stmt = self::$DB->prepare($sql);
        self::getPDOError($sql);
        foreach (self::$parms as $pram) {
            self::$stmt->bindParam($pram[0], $pram[1], $pram[2], $pram[3]);
        }
        self::$stmt->execute();
        self::getSTMTError($sql);
        self::$stmt->setFetchMode(PDO::FETCH_ASSOC);
        switch ($type) {
            case '0':
                $result = self::$stmt->fetch();
                break;
            case '1':
//                do{
//                    $result[] = self::$stmt->fetchAll();
//                } while (self::$stmt->nextRowset());
                $result = self::$stmt->fetchAll();
                self::$stmt->closeCursor();
                break;
            case '2':
                if (self::$stmt) {
                    $result = self::$stmt->rowCount();
                } else {
                    $result = 0;
                }
                break;
        }
        self::$stmt = null;
        return $result;
    }
    
    /**
     * 權為測試使用,目前沒有辦法獲取到OUT參數.
     * 另外在一存講過程中若有rowset,同時還有OUT參數時,
     * 即使$db->scalar("select @Rcount AS Rcount", "Rcount")這种方式仍然獲到不到,
     * 執行時會出現下面的錯誤:
     * Cannot execute queries while other unbuffered queries are active. Consider using PDOStatement::fetchAll(). Alternatively, if your code is only ever going to run against mysql, you may enable query buffering by setting the PDO::MYSQL_ATTR_USE_BUFFERED_QUERY attribute.
     */
    public function procExecOut() {
        //$colour = 'red';
        self::$stmt = self::$DB->prepare('CALL puree_fruit(?)');
        self::$stmt->bindParam(1, $colour,PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT, 4000);
        self::$stmt->execute();
        print("After pureeing fruit, the colour is: $colour");
    }
    /*********************Proc操作方法結束*********************/


    /*********************錯誤處理開始*********************/

    /**
     * 設置是否為調試模式
     */
    public function setDebugMode($mode = true) {
        return ($mode == true) ? self::$debug = true : self::$debug = false;
    }

    /**
     * 捕獲PDO錯誤資訊
     * 返回:出錯資訊
     * 類型:字串
     */
    private function getPDOError($sql) {
        self::$debug ? self::errorfile($sql) : '';
        if (self::$DB->errorCode() != '00000') {
            $info = (self::$stmt) ? self::$stmt->errorInfo() : self::$DB->errorInfo();
            echo (self::sqlError('mySQL Query Error', $info[2], $sql));
            exit();
        }
    }
    private function getSTMTError($sql) {
        self::$debug ? self::errorfile($sql) : '';
        if (self::$stmt->errorCode() != '00000') {
            $info = (self::$stmt) ? self::$stmt->errorInfo() : self::$DB->errorInfo();
            echo (self::sqlError('mySQL Query Error', $info[2], $sql));
            exit();
        }
    }

    /**
     * 寫入錯誤日志
     */
    private function errorfile($sql) {
        echo $sql . '<br />';
        $errorfile = _ROOT . './dberrorlog.php';
        $sql = str_replace(array("n", "r", "t", "  ", "  ", "  "), array(" ", " ",
            " ", " ", " ", " "), $sql);
        if (!file_exists($errorfile)) {
            $fp = file_put_contents($errorfile, "<?PHP exit('Access Denied'); ?>n" . $sql);
        } else {
            $fp = file_put_contents($errorfile, "n" . $sql, FILE_APPEND);
        }

    }

    /**
     * 作用:運行錯誤資訊
     * 返回:運行錯誤資訊和SQL語句
     * 類型:字元
     */
    private function sqlError($message = '', $info = '', $sql = '') {
        $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml">';
        $html .= '<head><title>mySQL Message</title><style type="text/css">body {margin:0px;color:#555555;font-size:12px;background-color:#efefef;font-family:Verdana} ol {margin:0px;padding:0px;} .w {width:800px;margin:100px auto;padding:0px;border:1px solid #cccccc;background-color:#ffffff;} .h {padding:8px;background-color:#ffffcc;} li {height:auto;padding:5px;line-height:22px;border-top:1px solid #efefef;list-style:none;overflow:hidden;}</style></head>';
        $html .= '<body><div class="w"><ol>';
        if ($message) {
            $html .= '<div class="h">' . $message . '</div>';
        }
        $html .= '<li>Date: ' . date('Y-n-j H:i:s', time()) . '</li>';
        if ($info) {
            $html .= '<li>SQLID: ' . $info . '</li>';
        }
        if ($sql) {
            $html .= '<li>Error: ' . $sql . '</li>';
        }
        $html .= '</ol></div></body></html>';
        return $html;
    }
    /*********************錯誤處理結束*********************/
}

?>