<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Contactos
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <form action="{{ route('contacts.update', $contact) }}" method="POST" class="bg-white rounded-lg shadow p-6">
        {{-- La directiva @csrf genera un token de seguridad que se envía junto con cada solicitud POST a la aplicación,
            y el servidor verifica que este token sea válido antes de procesar la solicitud. De esta manera, se asegura que
            todas las solicitudes POST enviadas a la aplicación provengan de una fuente confiable y autorizada. --}}
            @csrf

            {{-- como es un formulario de actualización de registro se utiliza el Método "PUT" con esta directiva de Laravel,
                ya que el atributo "method" de HTML acepta "GET" y "POST" --}}
            @method('PUT')

            <x-jet-validation-errors class="mb-4"/>

            <div class="mb-4">
                <x-jet-label class="mb-1">
                    Nombre de contacto
                </x-jet-label>

                <x-jet-input type="text"
                            name="name"
                            value="{{ old('name', $contact->name) }}" {{-- Se define que el valor por defecto del campo "name" es el Nombre del Contacto ($contact->name) --}}
                            class="w-full"
                            placeholder="Ingrese el nombre del contacto." />

            </div>

            <div class="mb-4">
                <x-jet-label class="mb-1">
                    Correo electronico
                </x-jet-label>

                <x-jet-input type="email"
                name="email"
                value="{{ old('email',  $contact->user->email) }}" {{-- Se define que el valor por defecto del campo "email" es el Email del Contacto ($contact->email) --}}
                class="w-full"
                placeholder="Ingrese el correo electronico." />
            </div>

            <div class="flex justify-end">
                <x-jet-button>
                    Actualizar contacto
                </x-jet-button>
            </div>
        </form>
    </div>
</x-app-layout>
