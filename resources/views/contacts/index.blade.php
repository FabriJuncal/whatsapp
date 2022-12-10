<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Contactos
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <div class="flex justify-end mb-4">
            <a href="{{ route('contacts.create') }}" class="btn btn-blue">Crear Curso</a>

        </div>

        {{-- Si el usuario logeado tiene Contactos Mostramos la Lista, Sino mostramos el Alet --}}
        @if ($contacts->count())
            <p>aquí se mostrará tu lista de contactos</p>
        @else
            <div class="flex w-full overflow-hidden bg-white rounded-lg shadow-md dark:bg-gray-800">
                <div class="flex items-center justify-center w-12 bg-blue-500">
                    <svg class="w-6 h-6 text-white fill-current" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 3.33331C10.8 3.33331 3.33337 10.8 3.33337 20C3.33337 29.2 10.8 36.6666 20 36.6666C29.2 36.6666 36.6667 29.2 36.6667 20C36.6667 10.8 29.2 3.33331 20 3.33331ZM21.6667 28.3333H18.3334V25H21.6667V28.3333ZM21.6667 21.6666H18.3334V11.6666H21.6667V21.6666Z" />
                    </svg>
                </div>

                <div class="px-4 py-2 -mx-3">
                    <div class="mx-3">
                        <span class="font-semibold text-blue-500 dark:text-blue-400">Ups!!!</span>
                        <p class="text-sm text-gray-600 dark:text-gray-200">Usted no tiene contactos</p>
                    </div>
                </div>
            </div>
        @endif

    </div>
</x-app-layout>
