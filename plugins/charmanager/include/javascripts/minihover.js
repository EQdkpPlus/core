wmtt = null;

document.onmousemove = updateWMTT;

function updateWMTT(e) {
	x = (document.all) ? window.event.x + document.body.scrollLeft : e.pageX;
	y = (document.all) ? window.event.y + document.body.scrollTop  : e.pageY;
	if (wmtt != null) {
		wmtt.style.left = (x + 20) + "px";
		wmtt.style.top 	= (y + 20) + "px";
	}
}

function showWMTT(id) {
	wmtt = document.getElementById(id);
	wmtt.style.display = "block"
}

function hideWMTT() {
	wmtt.style.display = "none";
}