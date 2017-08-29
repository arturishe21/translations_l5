var obj = {};@foreach($data as $phrase => $translate)obj['{{$phrase}}'] = '{{$translate[$lang] or ''}}';@endforeach
window.__t = function(phrase){ if (typeof obj[phrase] != "undefined") { return obj[phrase]; } else { return ''; }}

