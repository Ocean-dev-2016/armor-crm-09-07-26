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
        // #region agent log
        $armorDbgT0 = microtime(true);
        $armorDbgPortsTried = array();
        // #endregion
        foreach ($this->db_ports as $port) {
          // #region agent log
          $armorDbgPortT0 = microtime(true);
          // #endregion
          $this->myconn = @mysqli_connect(
            $this->db_host,
            $this->db_user,
            $this->db_pass,
            $this->db_name,
            $port
          );
          // #region agent log
          $armorDbgPortsTried[] = array(
            'port' => (int) $port,
            'ok' => $this->myconn ? 1 : 0,
            'ms' => round((microtime(true) - $armorDbgPortT0) * 1000, 1)
          );
          // #endregion
          if ($this->myconn) {
            $this->con = true;
            // #region agent log
            if (!isset($GLOBALS['armor_dbg_timing'])) {
              $GLOBALS['armor_dbg_timing'] = array();
            }
            $GLOBALS['armor_dbg_timing']['db_connect'] = array(
              'hypothesisId' => 'A',
              'total_ms' => round((microtime(true) - $armorDbgT0) * 1000, 1),
              'ports' => $armorDbgPortsTried,
              'host' => $this->db_host
            );
            // #endregion
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
