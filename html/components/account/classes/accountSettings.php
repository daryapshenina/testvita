<?php
defined('AUTH') or die('Restricted access');
include_once $_SERVER['DOCUMENT_ROOT'].'/db.php';

class accountSettings
{
	public $registration_allow = 0; // Регистрация разрешена
	public $shop_allow = 1;
	public $ads_allow = 0;	
    protected static $instance;

    function __construct() 
    {
    	global $db;

    	$query = $db->query("SELECT settings FROM com_account_settings WHERE id = 1");
        $result = $query->fetchColumn();
		$r_obj = unserialize($result);

        if($r_obj)
        {
            foreach($r_obj as $key => $value)
            {
                $this->$key = $value;
            }
        }
    }

    public static function getInstance() 
    {
        if (self::$instance === null) 
        {
            self::$instance = new self;   
        }
 
        return self::$instance;
    }
  
	public function __sleep()
	{
		return array(
		'registration_allow',
		'shop_allow',
		'ads_allow',		
		);
	}
}



?>