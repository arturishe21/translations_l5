var obj = {};@foreach($data as $phrase => $translate)obj['{{ str_replace(array("\r\n", "\r", "\n"), '', $phrase) }}'] = '{{isset($translate[$lang]) ?  str_replace(array("\r\n", "\r", "\n"), '', $translate[$lang]) : ''  }}';@endforeach
window.__t = function(phrase){ if (typeof obj[phrase] != "undefined") { return obj[phrase]; } else { return phrase; }}

