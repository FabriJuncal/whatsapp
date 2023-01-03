<?php

namespace App\Http\Livewire;

use App\Models\Contact;
use Livewire\Component;

class ChatComponent extends Component
{

    public $search;
    public $contactChat;
    public $chat;

    /*
        PROPIEDAD COMPUTADAS:
        Las propiedades computadas son un tipo de propiedad de un objeto que se calcula en tiempo de ejecución a partir de otras propiedades
        del objeto o valores externos. Se definen con una función y tienen un valor dinámico que puede cambiar según las condiciones.
        Son comunes en lenguajes orientados a objetos y se utilizan para acceder fácilmente a valores dinámicos de manera consistente.
        Ejemplo: un objeto que representa a una persona con propiedades "edad" y "años de jubilación", donde la propiedad "años de jubilación"
        es una propiedad computada que se calcula a partir de la edad y una regla de jubilación establecida.

    */
    public function getContactsProperty()
    {

        /*
            El SQL equivalente de la siguiente consulta con el ORM de Laravel es:

            SELECT *
            FROM contacts
            WHERE user_id = {auth()->id()}
            AND (
                name LIKE '%{$this->search}%'
                OR EXISTS (
                    SELECT *
                    FROM users
                    WHERE users.id = contacts.user_id
                    AND email LIKE '%{$this->search}%'
                )
            )

            Si la consulta no obtiene registros se retorna un Array vacio para evitar error con respecto a la directiva "@forelse" del frontend
        */
        return Contact::where('user_id', auth()->id())
                ->when($this->search, function($query){
                    $query->where(function($query){
                        $query->where('name', 'like', '%'.$this->search.'%')
                            ->orWhereHas('user', function($query){
                                $query->where('email', 'like', '%'.$this->search.'%');
                            });
                    });
                })
                ->get() ?? [];
    }

    public function open_chat_contact(Contact $contact)
    {
        $chat = auth()->user()->chats()
            ->whereHas('users', function($query) use ($contact){
                $query->where('user_id', $contact->contact_id);
            })
            ->has('users', 2)
            ->first();

        if($chat){
            $this->chat = $chat;
        }else{
            $this->contactChat = $contact;
        }
    }

    public function render()
    {

        // view('livewire.chat-component') => Muestra la vista del componente "chat" de Livewire
        // ->layout('[Ruta Layout]') => Se utiliza para asignar un Layouts a un componente de Livewire.
        //      Parametro => Recibe como parametro la ruta del layout que se quiere utilizar, en este caso se utilizó "layouts.chat"
        return view('livewire.chat-component')->layout('layouts.chat');
    }
}
