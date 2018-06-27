<?php
// DAN 2012
// выводит содержимое раздела - центральная часть
defined('AUTH') or die('Restricted access');

echo'
<div align="center">
	<div class="quote_block" >
		<div class="quote_top"></div>
		<div class="quote_middle">'.$quote.'</div>
		<div class="quote_bottom">
			<div class="quote_bottom_left">
				<div class="quote_prop_2">
					<div class="quote_prop_2">Голосование:</div>
					<a href="#" onclick="vote(\''.$id.'\',\'plus\')" class="zp_bt" title="Голосовать &quot;ЗА&quot;" >
						<img border="0" src="http://'.$site.'/components/quote/frontend/tmp/images/za_bt.png" />
					</a>
					<div id="votestatus_'.$id.'" class="quote_prop_vb">
						<div class="quote_prop_2_left" style="width: '.$toolbar_plus.'%">
							<div class="rt_vb" title="Рейтинг (% голосов за)" >'.$rating.'%</div>						
							<div class="quote_prop_votingbar" title="голосов за" >'.$avp.'</div>
							<div class="rt_vb" title="Голосов за" >'.$vote_plus.'</div>							
						</div>
						<div class="quote_prop_2_right" style="width: '.$toolbar_minus.'%">
							<div class="rt_vb" title="голосов против"></div>					
							<div class="quote_prop_votingbar" title="голосов против" >'.$avm.'</div>
							<div class="rt_vb" title="Голосов за" >'.$vote_minus.'</div>						
						</div>
					</div>
					<a href="#" onclick="vote(\''.$id.'\',\'minus\')"  class="zp_bt" title="Голосовать &quot;ПРОТИВ&quot;" >
						<img border="0" src="http://'.$site.'/components/quote/frontend/tmp/images/protiv_bt.png" />
					</a>
				</div>			
			</div>
			<div class="quote_bottom_right"><a href="http://'.$site.'/quote/author/'.$author_id.'">'.$author.'</a></div>			
		</div>
	</div>	
</div>
';

?>