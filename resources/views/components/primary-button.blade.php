<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2.5 bg-[#1B4F72] hover:bg-[#154360] active:bg-[#10344d] border border-transparent rounded-xl font-semibold text-xs text-white tracking-wider focus:outline-none focus:ring-2 focus:ring-[#1B4F72] focus:ring-offset-2 transition ease-in-out duration-150 shadow-xs']) }}>
    {{ $slot }}
</button>
