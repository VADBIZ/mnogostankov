$(document).ready(function() {
	$('.date').datetimepicker({
		language: document.cookie.match(new RegExp('language=([^;]+)'))[1],
		pickTime: false
	});

	$('.datetime').datetimepicker({
		language: document.cookie.match(new RegExp('language=([^;]+)'))[1],
		pickDate: true,
		pickTime: true
	});

	$('.time').datetimepicker({
		language: document.cookie.match(new RegExp('language=([^;]+)'))[1],
		pickDate: false
	});
});