<?php

namespace aybu\db;
use \PDO;

class mysqlDB
{
    private $MYSQL_HOST = "localhost";
    private $MYSQL_USER='root'; // mysql kullanıcı adı
    private $MYSQL_PASS='';  // mysql şifresi
    private $MYSQL_DB='aybu'; // database adı
    private $CHARSET = "UTF8";
    private $COLLATION = "utf8_general_ci";
    private $pdo = null;
    private $stmt = null;

    private function ConnectDB(){ //database bağlantısı
        $SQL = "mysql:host=".$this->MYSQL_HOST.";dbname=".$this->MYSQL_DB;
    
        try{
            $this->pdo = new PDO($SQL,$this->MYSQL_USER,$this->MYSQL_PASS);
            $this->pdo->exec("SET NAMES '".$this->CHARSET."' COLLATE '".$this->COLLATION."'");
            $this->pdo->exec("SET CHARACTER SET '".$this->CHARSET."'");
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
        }
        catch(PDOException $e){
            die("PDO ile veritabanına ulaşılamadı.".$e->getMessage());
        }
    }
    
    function __construct(){ //bağlantıyı aç
        $this->ConnectDB();
    }

    function createDB($query){ //veritabanı oluşturmak için
        $myDB = $this->pdo->query($query.' CHARACTER SET '.$this->CHARSET.' COLLATE '.$this->COLLATION);
        return $myDB;
    }
    function TableOperations($query){ //tablo operasyonları için
        $myTable = $this->pdo->query($query);
        return $myTable;
    }
    function copyTable($query){
        $myCopiedTable = $this->pdo->query($query);
        
        if($myCopiedTable){
            $copyContext = $this->pdo->query("INSERT INTO football.teams SELECT * FROM aybu.teams");
            return $myCopiedTable;
        }
    }
    function Maintenance(){ //tabloların bakımı için
        $myTable = $this->pdo->query("SHOW TABLES");
        $myTable->setFetchMode(PDO::FETCH_NUM);
        if($myTable){
            foreach($myTable as $items){
                $check = $this->pdo->query("CHECK TABLE ".$items[0]);
                $analyze = $this->pdo->query("ANALYZE TABLE ".$items[0]);
                $repair = $this->pdo->query("REPAIR TABLE ".$items[0]);
                $optimize = $this->pdo->query("OPTIMIZE TABLE ".$items[0]);
                if($check && $analyze && $repair && $optimize){
                    echo $items[0]." tablosunun bakımını yapıldı.<br>";
                }
                else{
                    echo "Bir hata oluştu.";
                }
            }
            
        }
    }

    private function myQuery($query,$params=null){ //diğer metodlardaki tekrarlı verileri bitirmek için kullanılan metod
        if(is_null($params)){
            $this->stmt = $this->pdo->query($query);
        }else{
            $this->stmt = $this->pdo->prepare($query);
            $this->stmt->execute($params);
        }

        return $this->stmt;
    }
    function Limit($query,$p1=1,$p2=null){
        $this->stmt = $this->pdo->prepare($query);
        $this->stmt->bindParam(1,$p1,PDO::PARAM_INT);
        if(!is_null($p2))
        $this->stmt->bindParam(2,$p2,PDO::PARAM_INT);

        $this->stmt->execute();
        return $this->stmt->fetchAll();
    }
    function getDatas($query,$params=null){ //çoklu satır verilerini çekmek için
        try{
            return $this->myQuery($query,$params)->fetchAll();
        }
        catch(PDOException $e){
            die($e->getMessage());
        }
    }
    function getData($query,$params=null){ //tek satır veri çekmek  için
        try{
            return $this->myQuery($query,$params)->fetch();
        }
        catch(PDOException $e){
            die($e->getMessage());
        }
    }
    function getColumnData($query,$params=null){ //tek satırın sütun verisini çekmek için nokta veri alışı
        try{
            return $this->myQuery($query,$params)->fetchColumn();
        }
        catch(PDOException $e){
            die($e->getMessage());
        }
    }
    function Insert($query,$params=null){ //kayıt eklemek için
        try{
            $this->myQuery($query,$params);
            return $this->pdo->lastInsertId();
        }
        catch(PDOException $e){
            die($e->getMessage());
        }
    }
    function Update($query,$params=null){ //kayıt güncellemek için
        try{
            return $this->myQuery($query,$params)->rowCount();
        }
        catch(PDOException $e){
            die($e->getMessage());
        }
    }
    function Delete($query,$params=null){ //kayıt silmek için
        $this->Update($query,$params);
    }
    function getAbouts($memberabout,$memberid){ //Hakkında kısmına veri çekmek için
        
        $about_datas = $this->getData("SELECT * FROM memberabout WHERE MemberID = '$memberid'");

        if(is_null($about_datas->$memberabout)){
          return "";
        }else{
          return $about_datas->$memberabout;
        }
      }
    function __destruct(){ //bağlantıyı kapat
        $this->pdo=null;
    }
}


?>