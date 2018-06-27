<?php
defined('AUTH') or die('Restricted access');


class adsSectionItems
{
	private $SQL_select = '';
	private $SQL_where = '';
	private $SQL_ordering = ' ORDER BY date_c DESC';
	private $SQL_limit = 'LIMIT 0, 100';
	private $SQL_arr = array();


	// Выполнить запрос
	public function getItems()
	{
		global $db;

		$stmt = $db->prepare("
			SELECT i.id, i.user_id, i.title, i.image, i.date_c ".$this->SQL_select."
			FROM com_ads_item i
			WHERE 1 ".$this->SQL_where."
			".$this->SQL_ordering."
			".$this->SQL_limit."
		");

		$stmt->execute($this->SQL_arr);
		return $stmt->fetchAll();
	}


	public function setContent()
	{
		$this->SQL_select .= ', i.content';
	}


	public function setLimit($_start = 0, $_rows = 100)
	{
		$this->SQL_limit = 'LIMIT '.intval($_start).', '.intval($_rows);
	}


	public function setSection($_section = 0)
	{
		if($_section)
		{
			$this->SQL_where .= 'AND section = :section ';
			$this->SQL_arr['section'] = intval($_section);
		} 
	}


	public function setPub($_pub = 1)
	{
		$this->SQL_select .= ', i.pub';
		if($_pub == 1) $this->SQL_where .= 'AND pub = 1 ';
		if($_pub === 0) $this->SQL_where .= 'AND pub = 0 ';	
	}


	public function setUser($_user_id)
	{
		$this->SQL_where .= 'AND user_id = :user_id ';
		$this->SQL_arr['user_id'] = intval($_user_id);
	}
}

?>