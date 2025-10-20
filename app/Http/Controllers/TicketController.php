<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    //
    public function index(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // defensive: ensure we have an authenticated user
        if (! $user) {
            return response()->json(["message" => "Unauthenticated"], 401);
        }

        // allow client to control page size, with sane defaults and limits
    $perPage = (int) $request->query('per_page', 5);
        if ($perPage <= 0) {
            $perPage = 15;
        }
        // cap to prevent abuse
        $perPage = min($perPage, 100);

    $tickets = $user->tickets()->paginate($perPage);

        return response()->json($tickets);
    }

    public function store(Request $request)
    {
        $validatedRequest = $request->validate([
            'issue' => 'required|max:255',
            'priority' => 'required',
        ]);
        $ticket = Ticket::create([
            'issue' => $validatedRequest['issue'],
            'priority' => $validatedRequest['priority'],
            'user_id' => Auth::user()->id,
            'state' => 'open'
        ]);

        return $ticket;

    }

    public function show(Ticket $ticket) {

        if($ticket->user_id !== Auth::user()->id) {
            abort(Response::HTTP_FORBIDDEN, 'you do not have access to this ticket');
        }

        $ticket->load('replies');
        return response()->json($ticket);
    }

    public function reopen(Ticket $ticket) {
        if($ticket->user_id !== Auth::user()->id) {
            abort(Response::HTTP_FORBIDDEN, 'you do not have access to this ticket');
        }

        if($ticket->state !== 'closed'){
                return response()->json([
                    'message' => 'Cannot reopen an open ticket'
                ],Response::HTTP_BAD_REQUEST);
        }
        $ticket->state = 'awaiting';
        $ticket->save();

        return response()->json([
        'message' => 'Ticket reopened successfully.',
        'ticket' => $ticket,
    ]);
    }

    public function getByState(string $state, Request $request) {

        $possible_states = ['open', 'closed', 'awaiting'];
        if(!in_array($state, $possible_states)) {
            return response()->json([
                'message' => 'specfied state does not exist'
            ],Response::HTTP_BAD_REQUEST);
        }
        $userId = Auth::user()->id;

        $perPage = $request->query('per_page', 5);
        $tickets = Ticket::where('user_id', $userId)
                        ->where('state', $state)
                        ->orderBy('created_at', 'desc')
                        ->paginate($perPage);
                        
        return response()->json($tickets);
    }
}
