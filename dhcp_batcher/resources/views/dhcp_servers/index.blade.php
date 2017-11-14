@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">DHCP Servers</div>

                    <div class="panel-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif
                        <a href="{{action("DhcpServerController@create")}}" class="button btn btn-primary">Add Server</a>
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">Username</th>
                                <th scope="col">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($dhcpServers as $key => $dhcpServer)
                            <tr>
                                <th scope="row">{{$key}}</th>
                                <td>{{$dhcpServer->name}}</td>
                                <td>{{$dhcpServer->username}}</td>
                                <td>
                                    <a href="{{action("DhcpServerController@resetPassword",['dhcp_server' => $dhcpServer->id])}}" class="button btn btn-primary">Reset Password</a>
                                    <a class="button btn btn-danger">Delete</a>
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
