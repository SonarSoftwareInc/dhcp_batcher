@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Add DHCP Server</div>
                    <div class="panel-body">
                        @if (session('errors'))
                            <div class="alert alert-danger">
                                @foreach(json_decode(session('errors')) as $error)
                                    <div>{{$error[0]}}</div>
                                @endforeach
                            </div>
                        @endif
                        <form method="post" action="{{action("DhcpServerController@store")}}">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" aria-describedby="name" name="name" placeholder="Enter a descriptive name" value="{{old('name')}}">
                                <small id="emailHelp" class="form-text text-muted">This name is used for easy identification in the list, and can be anything you like.</small>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
