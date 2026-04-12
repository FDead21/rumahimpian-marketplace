@auth
<div style="display: flex; align-items: center; gap: 10px; padding-right: 12px;">
    <div style="text-align: right; display: none;" class="md:block">
        <p style="font-size: 11px; color: #9ca3af; line-height: 1;">
            {{ auth()->user()->role === 'ADMIN' ? 'Platform Overview' : 'Your Performance' }}
        </p>
    </div>
    <a href="{{ route('property.home') }}" target="_blank"
       style="display: inline-flex; align-items: center; gap: 6px; background: transparent; border: 1px solid #374151; color: #d1d5db; padding: 5px 12px; border-radius: 8px; font-size: 12px; font-weight: 500; text-decoration: none; white-space: nowrap;">
        <svg xmlns="http://www.w3.org/2000/svg" style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
        </svg>
        Open Website
    </a>
</div>
@endauth