window.addEventListener("load", function() {

	var elements = document.getElementsByClassName('mod_flat_shadow_button_container');
	var parents = [];

	Array.prototype.forEach.call(elements, function(_element) {

		var parent = null;

		parents.some(function(_parent) {
			if(_parent === _element.parentNode)
			{
				parent = _parent;
				return true;
			}
		});

		if(parent === null)
		{
			parents.push(_element.parentNode);
			parent = parents[parents.length - 1];
			parent.style.textAlign = 'center';
			parent.classList.add("row");
			parent.__childrens = [];
			parent.__height = 0;
		}

		if(_element.classList.length <= 1)
			return;

		parent.__childrens.push(_element);

	});

	var resize = function()
	{

		parents.forEach(function(_parent) {

			_parent.__height = 0;

			_parent.__childrens.forEach(function(_element) {
				_element.children[0].style.minHeight = "0px";
			});

		});

		parents.forEach(function(_parent) {

			_parent.__childrens.forEach(function(_element) {

				var elementHeight = _element.children[0].offsetHeight;

				if(elementHeight > _parent.__height)
					_parent.__height = elementHeight;

			});

		});

		parents.forEach(function(_parent) {

			_parent.__height = Math.round(_parent.__height + 0.5);
			var height = _parent.__height+"px";

			_parent.__childrens.forEach(function(_element) {
				_element.children[0].style.minHeight = height;
			});

		});

	};

	resize();
	window.addEventListener("resize", resize);

});
