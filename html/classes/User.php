<?php
defined('AUTH') or die('Restricted access');
// Статус 0 - не активирован; 1 - активный; 13 - заблокирован;

class User
{
	public $options = array(
		'surname' => true,
		'birth_date' => false,
		'company' => false,
		'phone' => false,
		'image' => true,
		'about' => false,
		'country' => false,
		'city' => false,
		'address' => false,
		'options' => false
		);


    public function profile($_id)
    {
 		global $db;

		if(isset($this->users_arr[$_id])) return $this->users_arr[$_id];

		$sql = '';
		$sql .= ($this->options['surname']) ? ', p.surname' : '';
		$sql .= ($this->options['birth_date']) ? ', p.birth_date' : '';
		$sql .= ($this->options['company']) ? ', p.company' : '';
		$sql .= ($this->options['phone']) ? ', p.phone' : '';
		$sql .= ($this->options['image']) ? ', p.image' : '';
		$sql .= ($this->options['about']) ? ', p.about' : '';
		$sql .= ($this->options['country']) ? ', p.country' : '';
		$sql .= ($this->options['city']) ? ', p.city' : '';
		$sql .= ($this->options['address']) ? ', p.address' : '';
		$sql .= ($this->options['options']) ? ', p.options' : '';	

		$stmt = $db->prepare("
			SELECT u.id, u.email, u.status, p.name ".$sql."
			FROM com_account_users u
			JOIN com_account_profile p
			ON p.user_id = u.id
			WHERE u.id = :id AND (u.status != 0 OR u.status != 13)
			LIMIT 1;
		");
		$stmt->execute(array('id' => $_id));

		if($stmt->rowCount() == 0) return false;

		$this->users_arr[$_id] = $stmt->fetch();

		if(isset($this->users_arr[$_id]['image']) && $this->users_arr[$_id]['image'] == 1)
		{
			$floor_id = 1000 * floor($_id/1000);
			$path = '/files/account/'.$floor_id.'/'.$_id.'/';
			$this->users_arr[$_id]['thumbnail'] = '<img data-id="'.$_id.'" class="account_thumbnail" src="'.$path.'thumbnail.jpg">';
			$this->users_arr[$_id]['photo'] = '<img data-id="'.$_id.'" class="account_thumbnail" src="'.$path.'photo.jpg">';					
		}
		else
		{
			$this->users_arr[$_id]['image'] = false;
		}

    	return $this->users_arr[$_id];
    }


    public function setOption($_arr)
    {
		$this->users_arr[$_id] = array_merge($this->users_arr[$_id], $_arr);
    }

    private $users_arr = array();
}

?>