<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Se recupera todos los registros de la tabla "Contact", lo pagina y se envía el array a la vista.
        $contacts = auth()->user()->contacts()->paginate();
        return view('contacts.index', compact('contacts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('contacts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $contact => Recibe como parametro un ID y lo relaciona automaticamente con el modelo Contact y de este modo se obtiene todo el registro relacionado con este ID en la tabla Contact
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact)
    {
        //
        return view('contacts.show', compact('contact'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $contact => Recibe como parametro un ID y lo relaciona automaticamente con el modelo Contact y de este modo se obtiene todo el registro relacionado con este ID en la tabla Contact
     * @return \Illuminate\Http\Response
     */
    public function edit(Contact $contact)
    {
        //
        return view('contacts.edit', compact('contact'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $contact => Recibe como parametro un ID y lo relaciona automaticamente con el modelo Contact y de este modo se obtiene todo el registro relacionado con este ID en la tabla Contact
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contact $contact)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $contact => Recibe como parametro un ID y lo relaciona automaticamente con el modelo Contact y de este modo se obtiene todo el registro relacionado con este ID en la tabla Contact
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contact $contact)
    {
        //
    }
}
