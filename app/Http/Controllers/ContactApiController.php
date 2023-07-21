<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;
use App\Rules\InvalidEmail;
use Illuminate\Validation\Rule;

class ContactApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contacts = auth()->user()->contacts()->paginate();
        return response()->json($contacts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validamos los datos enviados por HTTP desde el formulario
        $request->validate([
            'name' => 'required', // Obligatorio
            'email' => [
                'required',       // Obligatorio
                'email',          // Debe tener formato de correo electrónico
                'exists:users',   // Debe existir en la tabla de usuarios de la base de datos
                Rule::notIn([auth()->user()->email]), // El correo electrónico no puede ser igual al correo electrónico del usuario autenticado en la aplicación
                new InvalidEmail  // Regla Personalizada en el archivo: "app\Rules\InvalidEmail.php". Esta Regla valida que el email ingresado no pertenezca ya a un Contacto
            ]
        ]);

        // Se busca el usuario en la BD con el correo electronico enviado desde el formulario
        $user = User::where('email', $request->email)->first();

        // Hace un INSERT en la tabla "Contact" con los datos obtenidos de la tabla "User" filtrado por el Correo Electrónico

        /*
            El SQL Equivalente sería:

            INSERT INTO contacts (name, user_id, contact_id)
            VALUES (<$request->name>, <auth()->id()>, <$user->id>)
        */

        $contact = Contact::create([
            'name' => $request->name,
            'user_id' => auth()->id(),
            'contact_id' => $user->id
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Contacto agregado exitosamente',
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact)
    {
        return response()->json([
            'status' => true,
            'data' => $contact
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contact $contact)
    {
        // Validamos los datos enviados por HTTP desde el formulario
        $request->validate([
            'name' => 'required', // Obligatorio
            'email' => [
                'required',       // Obligatorio
                'email',          // Debe tener formato de correo electrónico
                'exists:users',   // Debe existir en la tabla de usuarios de la base de datos
                Rule::notIn([auth()->user()->email]), // El correo electrónico no puede ser igual al correo electrónico del usuario autenticado en la aplicación
                new InvalidEmail($contact->user->email)  // Regla Personalizada en el archivo: "app\Rules\InvalidEmail.php". Esta Regla valida que el email ingresado no pertenezca ya a un Contacto
            ]
        ]);

        // Se busca el usuario en la BD con el correo electronico enviado desde el formulario
        $user = User::where('email', $request->email)->first();

        // Hace un UPDATE en la tabla "Contacts" filtrado por el ID de Contacto
        /*
            El SQL Equivalente sería:

            UPDATE contacts
            SET name = [$request->name],
                contact_id = [$user->id]
            WHERE id = [$contact->id]
        */
        $contact->update([
            'name' => $request->name,
            'contact_id' => $user->id
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Contacto actualizado exitosamente',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contact $contact)
    {
        // Hace un DELETE en la tabla "Contacts" filtrado por el ID de Contacto

        /*
            El SQL Equivalente sería:

            DELETE FROM contacts
            WHERE id = $contact->id
        */
        $contact->delete();

        return response()->json([
            'status' => true,
            'message' => 'Contacto eliminado exitosamente',
        ], 200);
    }
}
