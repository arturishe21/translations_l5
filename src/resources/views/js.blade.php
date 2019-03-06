var obj = {!! json_encode($translates)  !!};
window.__t = function(phrase){
if (typeof obj[phrase] != "undefined") {
return obj[phrase]; } else { return phrase;
}}
