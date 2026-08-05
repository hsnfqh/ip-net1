@php
    $accent = $accent ?? false;
    $iconColor = $accent ? '#C81E2C' : '#3D3A44';
    $iconBg = $accent ? '#FDF1F2' : '#F1F0EE';
@endphp

<div class="wms-card wms-card-hover" style="padding: 17px 18px; position: relative; overflow: hidden;">
    @if($accent)
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, #C81E2C, #7A0D18);"></div>
    @endif
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <span style="font-size: 12px; color: #75727C; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">{{ $label }}</span>
        <div style="width: 30px; height: 30px; border-radius: 8px; background: {{ $iconBg }}; display: flex; align-items: center; justify-content: center;">
            <svg class="lucide lucide-{{ $icon }}" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="{{ $iconColor }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                {!! $slot !!}
            </svg>
        </div>
    </div>
    <div style="font-family: 'Space Grotesk', sans-serif; font-size: 29px; font-weight: 700; color: #17151C; margin-top: 11px; letter-spacing: -0.3px;">{{ $value }}</div>
</div>