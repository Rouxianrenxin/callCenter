@extends ('app')
  
@section ('content')
<?php
use App\User;
use App\PersonCampaign;
use App\Campaign;
use App\Question;
  Session::flash('backUrl', Request::fullUrl());
  $url = Session::get('backUrl');
?>
   
  @foreach ($resultado  as $rows) 
        @foreach ($rows as $row => $descriptions)
          <?php
            $columna[]=$row;      
          ?>
          @endforeach  
        @endforeach 
        <?php
        $columna2=array_unique($columna);
          $t=sizeof($columna2); 
      ?>
  <div style="margin:150px auto; width:600px">
    <table class="table table-striped"> 
      <tr>
      @foreach ($columna2 as $columna2)
        <th>
          <strong>
            {{$columna2}}
          </strong>
        </th>
      @endforeach   
      </tr>
      @foreach ($resultado  as $rows) 
        <tr>
          @foreach ($rows as $name => $descriptions)
            <td>  
              {{$descriptions}}     
            </td>     
            @endforeach 
        </tr>
      @endforeach 
    </table>
  </div>
   
@stop