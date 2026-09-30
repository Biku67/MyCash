@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-slate-300 focus:border-[#1B4F72] focus:ring-[#1B4F72] rounded-xl shadow-xs']) }}>
