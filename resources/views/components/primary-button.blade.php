<button {{ $attributes->merge([
    'type' => 'submit',
    'class' =>
        'inline-flex items-center justify-center px-4 py-2 
         bg-gradient-to-r from-indigo-600 to-indigo-700 
         hover:from-indigo-700 hover:to-indigo-800 
         border border-transparent rounded-lg font-semibold text-sm 
         text-indigo-700 uppercase tracking-widest 
         focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 
         transition ease-in-out duration-150 shadow-lg hover:shadow-xl'
]) }}>
    {{ $slot }}
</button>
