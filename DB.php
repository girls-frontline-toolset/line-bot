<?php

class DB
{
    private $hostname;
    private $database;
    private $username;
    private $password;
    private $conn;
    protected $tableName;

    public function __construct()
    {
        include_once("DBInfo.php");
        $info = new DBInfo();

        $this->hostname = $info->hostname;
        $this->database = $info->database;
        $this->username = $info->username;
        $this->password = $info->password;
    }

    public function getConnection()
    {
        $this->conn = mysqli_connect($this->hostname, $this->username, $this->password, $this->database);
        return $this->conn;
    }

    public function close()
    {
        mysqli_close($this->conn);

    }

    function getDataByAssocList($sql)
    {

        $con =$this->getConnection();
        mysqli_set_charset($con,"utf8");
        $rs = mysqli_query($con,$sql) or die (mysqli_error($this->conn));
        if (mysqli_num_rows($rs) < 0) {
            return null;
        }
        $list = mysqli_fetch_all($rs, MYSQLI_ASSOC);
        mysqli_free_result($rs);
        $this->close();
        return $list;
    }

    function getDataByIndexList($sql)
    {

        $con =$this->getConnection();
        mysqli_set_charset($con,"utf8");
        $rs = mysqli_query($con , $sql) or die (mysqli_error($this->conn));
        if (mysqli_num_rows($rs) < 0) {
            return null;
        }
        $list = mysqli_fetch_all($rs, MYSQLI_NUM);
        mysqli_free_result($rs);
        $this->close();
        return $list;
    }

    function runSql($sql,$delimiter = ';'){
        $con =$this->getConnection();

        $sqlArray = explode($delimiter,$sql);

        foreach ($sqlArray as $data){
            mysqli_set_charset($con,"utf8");
            if($delimiter != ';'){
                $data .= ")";
            }

            $rs = mysqli_query($con , $data);
            //echo mysqli_error($this->conn);
            //echo $data;
            //echo "<br><br><br><br><br>";
        }
        //$rs = mysqli_query($con , $sql) or die (mysqli_error($this->conn));
        $this->close();
    }

    function runAndCheckAffected($sql)
    {
        $con = $this->getConnection();
        mysqli_set_charset($con,"utf8");
        mysqli_query($con, $sql) or die(mysqli_error($this->conn));
        $returnValue = (mysqli_affected_rows($this->conn) > 0);

        $this->close();
        return $returnValue;
    }


    public function updateLineBotLang($groupId,$lang){
        $sql = "UPDATE linebotlang set lang = '".addslashes($lang)."' where groupId = '".addslashes($groupId)."' ";
        $this->runAndCheckAffected($sql);
    }

    public function insertLineBotLang($groupId,$lang){
        $sql = "insert into linebotlang values(null,'".addslashes($groupId)."','".addslashes($lang)."')";
        $this->runAndCheckAffected($sql);
    }


    public function getLineBotLang($groupId){
        $sql = "SELECT lang FROM linebotlang where groupId = '".addslashes($groupId)."' ";
        $rs = $this->getDataByAssocList($sql);
        return $rs;
    }

}


