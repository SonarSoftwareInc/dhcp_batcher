@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Actions</div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                        <a href="/dhcp_servers" class="btn btn-primary btn-lg btn-block">DHCP Servers</a>
                        <a href="/logs" class="btn btn-primary btn-lg btn-block">Logs</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
