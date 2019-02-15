@extends('app')
<?php
use App\User;
use App\PersonCampaign;
use App\Campaign;
use App\Question;
use App\QuestionType;
  Session::flash('backUrl', Request::fullUrl());
  $url = Session::get('backUrl'); 
?>
@section('content')
<div class="container">
  <div class="row">
    @if($errors->any())
      <ul class="alert alert-danger">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    @endif
<h1>Crear Grupo</h1>
<a href="{{asset('/formato_crear_grupo.xlsx')}}" download class="btn btn-warning">Descargar Formato del Archivo</a>
<h3></h3>
    {!! Form::open(['url' => 'personGroup', 'method' => 'post', 'enctype' => 'multipart/form-data','files' => true]) !!}
    <div class="form-group col-md-4">
      {!! Form::label('groupName', 'Nombre del Grupo') !!}
      {!! Form::text('groupName', null, array('title' => 'si se deja vacio se tomara el nombre del archivo como nombre del grupo', 'placeholder' => 'Nombre del grupo', 'class' => 'form-control')) !!}        
    </div>
    <div class="col4" style="padding-bottom: 5px; width: 50%;">
      {!! Form::label('Select a file to upload', 'Seleciona un archivo para crear el grupo de operadores.csv .xls .xlsx', ['class' => 'control-label']) !!}
      {!! Form::file('file', ['class' => 'form-control']) !!}
    </div>

    
      {!! Form::submit('Crear Grupo', ['class' => 'btn btn-primary']) !!}
    
  </div>
</div>
  {!! Form::close() !!}
@endsection