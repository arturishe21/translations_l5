var obj = {!! json_encode($translates)  !!};
window.__t = function(phrase){
if (typeof obj[phrase] != "undefined") {
return obj[phrase]; } else {

fetch('/{{$lang}}/auto_translate?phrase=' + phrase)
	.then(response => response.text())
	.then(result => phrase = result)

return phrase;
}}
