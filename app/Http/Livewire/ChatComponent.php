<?php

namespace App\Http\Livewire;

use App\Models\Chat;
use App\Models\Contact;
use App\Models\Message;
use Livewire\Component;

// Importamos la Libreria de Notificaciones
use Illuminate\Support\Facades\Notification;

class ChatComponent extends Component
{

    public $search;
    public $contactChat;
    public $chat;
    public $bodyMessage;

    // Oyentes

    // Este método se utiliza para escuchar en un canal especifico emitido por Pusher.
    public function getListeners()
    {
        // Obtenemos el ID del usuario logedo
        $user_id = auth()->user()->id;

        // Retornamos un array donde:
        // 1er Parametro / Key del Array => Es el canal de Pusher por el cual se va a escuchar.
        //                                  (Se concatena la variable "$user_id" para hacerlo dinamico para cada usuario logeado)
        // 2do Parametro / Value del Array => Es el método que se va a ejecutar cada vez que se reciba una notificación a travez del
        //                                    canal especificado en el 1er Parametro/Key del array.

       // Mas detalles:
       // echo-notification => es un evento en tiempo real que se activa cuando se recibe una notificación.
       // App.Models.User.{$user_id} => especifica que esta escucha es para un usuario específico, se especifica como "App.Models.User"
       //                               y se concatena con el id del usuario autenticado.
       // notification => Indicamos a Livewire que el evento que ejecutará una transmisión será una notificación
       // render => Es el método que se ejecutará luego de recibír una transmisión mediante el evento "notification"
        return[
            "echo-notification:App.Models.User.{$user_id},notification" => 'render'
        ];
    }

    /*
        PROPIEDAD COMPUTADAS:
        Las propiedades computadas en Laravel son aquellas que no se almacenan en la base de datos, sino que se calculan dinámicamente a partir de otras
        propiedades de la clase. Es decir, son una forma de definir una propiedad que se "deriva" de otras propiedades, y que se puede utilizar como
        cualquier otra propiedad, pero sin necesidad de almacenarla en la base de datos.

        Por ejemplo, supongamos que tenemos una clase Product que tiene una propiedad price que almacena el precio del producto.
        Podríamos definir una propiedad computada llamada discountedPrice que se calcule restando un porcentaje de descuento al precio del producto:

        class Product
        {
            public function getDiscountedPriceProperty()
            {
                return $this->price * (1 - $this->discount);
            }
        }

        Luego, podríamos acceder a la propiedad discountedPrice de una instancia de la clase Product como si fuera una propiedad normal:

        $product = Product::find(1);
        echo $product->discountedPrice;


        TENER EN CUENTA: que para utilizar una propiedad computada se debe hacer uso de la siguiente convención.

                        public function get[NOMBRE_PROPIEDAD_COMPUTADA]Property(){}

                        Por ejemplo:

                        public function getEstoEsUnaPruebaProperty(){}

                        Y se deberá acceder de la siguiente forma:

                        $this->estoEsUnaPrueba;

    */

    // Propiedad computada que se utiliza en el buscador de contactos, en la vista podemos hacer uso de esta propiedad llamando a "$this->contacts"
    public function getContactsProperty()
    {

        // Obtiene los contactos filtrando por la propiedad "search" vinculado al input del buscador del chat
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

    // Propiedad computada que se utiliza al seleccionar un contacto y mostrar los mensajes existentes en el chat. , en la vista podemos hacer uso de esta propiedad llamando a "$this->messages"
    public function getMessagesProperty()
    {
        // Se obtiene el listado de mensajes del chat
        // Se utiliza $this->chat->messages()->get() en vez de $this->chat->messages, por que si utilizamos solo $this->chat->messages, este nos mostrará una instancia del chat
        // y si usamos $this->chat->messages()->get(), ejecutará nuevamente la consulta y obtendrá la una nueva instancia del chat todo el tiempo.

        // $this->chat->messages()->get() es lo mismo que utilizar Messages::where('chat_id', $this->chat->id)->get()

        return $this->chat ? $this->chat->messages()->get() : [];
    }

    // Propiedad computada que se utiliza para obtener los chats ordenados de manera Descendente por el Mutador "last_message_at", es decir,
    // los mensajes con la fecha del mensaje mas reciente se mostrarán primeros en la lista
    public function getChatsProperty()
    {
        return auth()->user()->chats()->get()->sortByDesc('last_message_at');
    }

    // Propiedad computada que se utiliza para obtener el/los ID/s Usuario de/los Contacto/s el cual enviamos un mensaje
    public function getUsersNotificationsProperty()
    {
        return $this->chat ? $this->chat->users->where('id', '!=', auth()->id()) : [];
    }

    // =================================================================================================================================================

    // Obtiene o Crea el Chat del Contacto que se seleccionó
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

            // Resetea el campo contactChat para mostrar la imagen y el nombre de la propiedad chat
            $this->reset('bodyMessage','contactChat', 'search');
        }else{
            // Si no se encontró un chat existente, asigna el contacto a la propiedad de la clase
            $this->contactChat = $contact;

            // Resetea el campo chat para mostrar la imagen y el nombre de la propiedad contactChat
            $this->reset('bodyMessage','chat', 'search');
        }
    }

    public function open_chat(Chat $chat)
    {
        $this->chat = $chat;
        $this->reset('contactChat', 'bodyMessage');
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


        // Utilizamos el Facade "Notification" para enviar la notificación a Pusher
        // 1er parametro -> ID de los usuarios a notificar
        // 2do Parametro -> Ruta y Nombre de la Clase de la notificación creada
        Notification::send($this->getUsersNotificationsProperty(), new \App\Notifications\NewMessage);

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
