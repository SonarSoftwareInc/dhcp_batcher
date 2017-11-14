@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Statistics</div>

                <div class="panel-body">
                    <ul>
                        <li>{{$pendingCount}} assignments pending delivery to Sonar.</li>
                        @if($pendingCount > 0)
                            <li>Oldest pending assignment is {{$oldestSpan}} minutes old.</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
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
                        <a href="/flush" class="btn btn-primary btn-lg btn-block">Flush Pending Assignments</a>
                        <a href="/dhcp_servers" class="btn btn-primary btn-lg btn-block">Manage DHCP Servers</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
