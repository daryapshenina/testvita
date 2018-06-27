var DAN = {};

DAN.modal = {
	block_up: false, // Не блокировать модальное окно
	
	// Добавить блок
	add: function(_content, _width = 600, _height = 200)
	{
		// защита от повторного создания модального окна
		if (document.getElementById('dan_2_modal_black') == null)
		{
			var modal_black = document.createElement('div');
			modal_black.id = 'dan_2_modal_black';
			document.body.insertBefore(modal_black, document.body.children[0]); // черный слой
			
			var modal_white = document.createElement('div');
			modal_white.id = 'dan_2_modal_white';
			modal_white.style.maxWidth = _width + 'px';
			modal_white.style.minHeight = _height + 'px';
			modal_white.innerHTML = _content;
			modal_black.appendChild(modal_white); // белый слой

			modal_white.onclick = function(e)
			{
				e.stopPropagation();
			}
			
			// Удаляем модальное окно (клик по тёмному фону)
			modal_black.onclick = function()
			{
				DAN.modal.del();
			}		
		}
	},
	
	// Удалить блок
	del: function()
	{
		if(!this.block_up)document.body.removeChild(document.getElementById('dan_2_modal_black'));
	},
	
	// Блокирует удаление модального окна
	block: function(_b)
	{
		this.block_up = _b;
	},	
}