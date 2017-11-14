<?php

namespace App\Http\Controllers;

use App\DhcpServer;
use App\Http\Requests\CreateDhcpServerRequest;
use Illuminate\Support\Facades\Hash;

class DhcpServerController extends Controller
{
    public function index()
    {
        $dhcpServers = DhcpServer::orderBy('id','asc')->get();
        return view("dhcp_servers.index", compact('dhcpServers'));
    }

    public function create()
    {
        return view("dhcp_servers.create");
    }

    public function store(CreateDhcpServerRequest $request)
    {
        $password = str_random(16);

        $dhcpServer = new DhcpServer([
            'name' => $request->input('name'),
            'username' => str_random(16),
            'password' => Hash::make($password),
        ]);
        $dhcpServer->save();

        return redirect()->action('DhcpServerController@index')->with('status',"Server username is {$dhcpServer->username} and password is $password - copy this password, as you won't be able to view it again!");
    }
}
