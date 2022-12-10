<?php

use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Route::resource() => establece un conjunto de rutas predefinidas para un recurso determinado.
//                      Esto permite que se genere automáticamente un conjunto completo de rutas que se utilizan
//                      comúnmente para operaciones CRUD (crear, leer, actualizar y eliminar) en un recurso determinado.
// 1er Parametro => 1ra parte de la ruta a la cual luego se va a acceder a los métodos del CRUD. Ej: contacts/show, contacts/create, etc.
// 2do Parametro => Controlador que debe tener los 7 métodos CRUD de manera obligaria o sino arrojará un error.
//                  Los 7 métodos CRUD son: index, create, store, show, edit, update, destroy

// ->names('contacts') => Se le asigna en plural el nombre de "contacts" a las rutas, es decir empezaran con la palabra "contacts".
//                        Por ejemplo: contacts.index, contacts.show, etc.
Route::middleware('auth')->resource('contacts', ContactController::class)->names('contacts');
