DAN_ready(function()
{
	var items = document.getElementsByClassName('section_item');

	for(var i = 0; i < items.length; i++)
	{
		var item = items[i];

		item.addEventListener('mouseenter', function() {
			this.style.minHeight = this.children[0].clientHeight + 'px';
			this.getElementsByClassName('section_item_char')[0].style.display = 'block';
			this.children[0].style.position = 'absolute';
			this.children[0].classList.add('section_item_hover');
		});

		item.addEventListener('mouseleave', function() {
			this.style.minHeight = '';
			this.getElementsByClassName('section_item_char')[0].style.display = 'none';
			this.children[0].style.position = 'static';
			this.children[0].classList.remove('section_item_hover');
		});
	}
});