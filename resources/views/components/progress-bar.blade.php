@php
    $progress = min(100, max(0, $value));
    $gradient = $progress === 100 
        ? '#1B7A46' 
        : 'linear-gradient(90deg, #AF1424, #D62E3C)';
@endphp

<div style="width: 100%; background: #EFEDEB; border-radius: 20px; height: 6px; overflow: hidden;">
    <div style="width: {{ $progress }}%; height: 100%; border-radius: 20px; background: {{ $gradient }}; transition: width .25s ease;"></div>
</div>