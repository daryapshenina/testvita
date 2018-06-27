<?php
// получаем список товаров на ajax
defined('AUTH') or die('Restricted access');

$limit = 50;

$num_in = intval($d[5]);
if($num_in < 1) $num_in = 1;
$step = ($num_in - 1) * $limit;

$stmt = $db->query("SELECT id, title FROM com_shop_item WHERE pub = 1 ORDER BY title LIMIT ".$step.",".$limit."");

$out = '';
$pag_out = ''; // Пагинация

while($m = $stmt->fetch())
{
	//$out .= '<div class="related_list_item" onclick="f_related_item('.$m['id'].')"><span class="related_list_id">'.$m['id'].'</span>'.$m['title'].'</div>';
	$out .= '<div class="related_list_item" onclick="f_related_item('.$m['id'].')">'.$m['title'].'</div>';
}

$stmt_count = $db->query("SELECT COUNT(*) as count FROM com_shop_item WHERE pub = 1");
$sum = $stmt_count->fetchColumn();

if($sum > $limit)
{
	$num = intval($sum/$limit) + 1;
	$num_out = '';
	$end_out = '';	

	if($num_in < 5)
	{
		$start = 1;
		$end = 10;
		$start_out = '';
		if($num > 10 )
		{
			$end_out = '<div class="related_pagination_num" onclick="item_list(10)">&gt;&gt;</div>';
		}
	}
	else
	{
		$start = $num_in - 4;
		$end = $num_in + 4;

		$start_out = '<div class="related_pagination_num" onclick="item_list('.$start.')">&lt;&lt;</div>';
		
		if($end < $num)
		{
			$end_out = '<div class="related_pagination_num" onclick="item_list('.$end.')">&gt;&gt;</div>';
		}
		else
		{
			$end_out = '';			
		}
	}

	for($i = $start; $i <= $end; $i++)
	{
		if($i == $num_in){$class = " rpn_active";} else{$class = "";}
		if($i <= $num){$num_out .= '<div class="related_pagination_num'.$class.'" onclick="item_list('.$i.')">'.$i.'</div>';}		
	}
	
	$pag_out = '
	<div class="related_pagination_container">
		'.$start_out.$num_out.$end_out.'
	</div>
	';
}

echo '<div class="related_container">
	'.$out.'
	</div>
	'.$pag_out.'
	';

exit;
?>