var obj = {};@foreach($data as $phrase => $translate)obj['{{$phrase}}'] = '{{$translate[$lang] or ''}}';@endforeach
window.__t = function(phrase){alert(obj[phrase]);}

