<?php

namespace App\Http\Controllers;

use App\Models\TipoSolicitud;
use Illuminate\Http\Request;

class TipoSolicitudController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    public function getTipos(Request $request){
        return response()->json(Tiposolicitud::all());
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
     * @param  \App\Models\TipoSolicitud  $tipoSolicitud
     * @return \Illuminate\Http\Response
     */
    public function show(TipoSolicitud $tipoSolicitud)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TipoSolicitud  $tipoSolicitud
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TipoSolicitud $tipoSolicitud)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TipoSolicitud  $tipoSolicitud
     * @return \Illuminate\Http\Response
     */
    public function destroy(TipoSolicitud $tipoSolicitud)
    {
        //
    }
}
