var v = new Date().getTime();

function loadScript(files) {
	var fileName;
	for (var i = 0; i < files.length; i++) {
		fileName = files[i];
		var script = document.createElement('script');
		script.src = fileName + '?v=' + v;
		document.body.appendChild(script);
	}
}

function loadStyle(files) {
	var fileName;
	for (var i = 0; i < files.length; i++) {
		fileName = files[i];
		var style = document.createElement('link');
		style.rel = 'stylesheet';
		style.type = 'text/css';
		style.href = fileName + '?v=' + v;
		document.head.appendChild(style);
	}
}