<?php

namespace App\Http\Controllers\Property;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    
    public function agent($id)
    {
        $agent = User::where('id', $id)->where('role', 'AGENT')->firstOrFail();
        
        $properties = \App\Models\Property::where('user_id', $agent->id)
                        ->where('status', 'PUBLISHED')
                        ->latest()
                        ->paginate(9);

        return view('property.agent.show', compact('agent', 'properties'));
    }
}
