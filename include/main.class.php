<?php
require_once __DIR__ . '/app.config.loader.php';

class Database
{
	protected $db_host;
	protected $db_user;
	protected $db_pass;
	protected $db_name;
	protected $db_ports = array(3307, 3306);
	protected $con 	= false;

	public function __construct()
	{
		$config = armor_get_app_config();
		$this->db_host = isset($config['db_host']) ? $config['db_host'] : 'localhost';
		$this->db_user = isset($config['db_user']) ? $config['db_user'] : '';
		$this->db_pass = isset($config['db_pass']) ? $config['db_pass'] : '';
		$this->db_name = isset($config['db_name']) ? $config['db_name'] : '';
		$this->db_ports = isset($config['db_ports']) ? $config['db_ports'] : array(3307, 3306);
	}
	
	
     public function connect()   
    {  
      
      if(!$this->con)
      {
        $lastErr = '';
        foreach ($this->db_ports as $port) {
          $this->myconn = @mysqli_connect(
            $this->db_host,
            $this->db_user,
            $this->db_pass,
            $this->db_name,
            $port
          );
          if ($this->myconn) {
            $this->con = true;
            return true;
          }
          $lastErr = mysqli_connect_error();
        }
        die('Connect Error: ' . $lastErr);
        return false;
      }
      else
      {
        return true;
      }
    }
      
 public function disconnect()    
      {   
          if($this->con)
          { 
              if(@mysqli_close($this->myconn))
              { 
                  $this->con = false;
                  // echo "disconnet";
                  return true;
              }
              else
              { 
                  return false;
              }
          }
      }
      
 public function getDBName()   
      {   
          $dbData = $this->db_host.",".$this->db_user.",".$this->db_pass.",".$this->db_name;
          return $dbData;
      }
    //--------------------------- DB -------------------------------//


}
?>
