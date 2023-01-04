<?php

namespace App\Http\Livewire;

use App\Models\Chat;
use App\Models\Contact;
use Livewire\Component;

class ChatComponent extends Component
{

    public $search;
    public $contactChat;
    public $chat;
    public $bodyMessage;

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

        // Obtiene el chat en el que el usuario actual tiene una conversación con el contacto especificado
        /*SQL Equivalente:

            SELECT * FROM chats
                INNER JOIN chat_user ON chat_user.chat_id = chats.id
                WHERE chat_user.user_id = :current_user_id
                AND EXISTS (SELECT *
                            FROM chat_user
                            WHERE chat_user.chat_id = chats.id
                            AND chat_user.user_id = :contact_id)
                AND (SELECT COUNT(*)
                        FROM chat_user
                        WHERE chat_user.chat_id = chats.id) = 2
                LIMIT 1
        */
        $chat = auth()->user()->chats()
            ->whereHas('users', function($query) use ($contact){
                // Filtra los chats que tienen al usuario especificado como participante
                $query->where('user_id', $contact->contact_id);
            })
            // Solo selecciona chats que tienen exactamente dos participantes
            ->has('users', 2)
            // Obtiene el primer chat que cumpla con los criterios
            ->first();

        // Si se encontró un chat existente, lo asigna a la propiedad de la clase
        if($chat){
            $this->chat = $chat;
        }else{
            // Si no se encontró un chat existente, asigna el contacto a la propiedad de la clase
            $this->contactChat = $contact;
        }
    }

    public function sendMessage()
    {
        // Valida que el cuerpo del mensaje no esté vacío
        $this->validate([
            'bodyMessage' => 'required'
        ]);

        // Si no hay una conversación existente, crea una nueva y agrega a los usuarios actuales
        if(!$this->chat){
            // SQL Equivalente:
            // INSERT INTO chats (id, created_at, updated_at) VALUES (:id, :created_at, :updated_at)
            $this->chat = Chat::create();

            /*
                El método attach en Laravel es utilizado para agregar registros a una tabla pivote de una relación de muchos a muchos.
                En este caso, $this->chat->users() es una instancia del constructor de consultas de Laravel para la relación users en el modelo Chat.
                El método attach toma una matriz de ID de usuarios y agrega una fila para cada uno de ellos en la tabla pivote, estableciendo la relación
                entre el chat y el usuario especificado.

                Por ejemplo, si $this->chat representa un chat con ID 10 y [auth()->user()->id, $this->contactChat->contact_id] es una matriz de dos ID de usuarios,
                attach agregaría las siguientes filas a la tabla pivote:

                    user_id | chat_id
                    --------+--------
                    1     |   10
                    2     |   10

                Esto establecería una relación entre el chat con ID 10 y los usuarios con ID 1 y 2.
            */

            // SQL Equivalente:
            // INSERT INTO chat_user (user_id, chat_id) VALUES (:user_id, :chat_id)
            $this->chat->users()->attach([auth()->user()->id, $this->contactChat->contact_id]);
        }

        // Crea un nuevo mensaje en la conversación actual y asigna el autor como el usuario actual
        // SQL Equivalente:
        // INSERT INTO messages (id, body, user_id, chat_id, created_at, updated_at) VALUES (:id, :body, :user_id, :chat_id, :created_at, :updated_at)
        $this->chat->messages()->create([
            'body' => $this->bodyMessage,
            'user_id' => auth()->user()->id
        ]);

        // Resetea los campos de mensaje y contacto para una nueva entrada
        $this->reset('bodyMessage', 'contactChat');
    }

    public function render()
    {

        // view('livewire.chat-component') => Muestra la vista del componente "chat" de Livewire
        // ->layout('[Ruta Layout]') => Se utiliza para asignar un Layouts a un componente de Livewire.
        //      Parametro => Recibe como parametro la ruta del layout que se quiere utilizar, en este caso se utilizó "layouts.chat"
        return view('livewire.chat-component')->layout('layouts.chat');
    }
}
