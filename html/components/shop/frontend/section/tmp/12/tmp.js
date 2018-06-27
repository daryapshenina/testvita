DAN_ready(function()
{
	var items = document.getElementsByClassName('section_item');

	for(var i = 0; i < items.length; i++)
	{
		var item = items[i];

		item.addEventListener('mouseenter', function() {
			this.style.minHeight = this.offsetHeight + 'px';
			this.getElementsByClassName('section_item_more')[0].style.display = 'block';
			this.children[0].style.position = 'absolute';
			this.children[0].classList.add('section_item_hover');
		});

		item.addEventListener('mouseleave', function() {
			this.style.minHeight = '';
			this.getElementsByClassName('section_item_more')[0].style.display = 'none';
			this.children[0].style.position = 'static';
			this.children[0].classList.remove('section_item_hover');
		});
	}
});

function item_quantity(_id, _n)
{
	var q = document.getElementById("shop_item_num_" + _id);
	q.value = parseInt(q.value) + _n;

	if(q.value < 1)
		q.value = 1;
}
