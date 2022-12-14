<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ChatComponent extends Component
{
    public function render()
    {

        // view('livewire.chat-component') => Muestra la vista del componente "chat" de Livewire
        // ->layout('[Ruta Layout]') => Se utiliza para asignar un Layouts a un componente de Livewire.
        //      Parametro => Recibe como parametro la ruta del layout que se quiere utilizar, en este caso se utilizó "layouts.chat"
        return view('livewire.chat-component')->layout('layouts.chat');
    }
}
