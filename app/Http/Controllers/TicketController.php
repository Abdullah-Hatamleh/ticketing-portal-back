<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
            'issue' => 'required|max:255|string',
            'priority' => 'required',
            'comment' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'categories' => 'array| nullable'
        ]);

        // Log::info($validatedRequest['categories']);
        //Handling file upload
        $path = null;
        if($request->hasFile('attachment')){
            $path = $request->file('attachment')->store('attachments', 'public');
            $path = "http://" . Config('app.url') . "/storage" . "/" . $path;
        }

        $ticket = Ticket::create([
            'issue' => $validatedRequest['issue'],
            'priority' => $validatedRequest['priority'],
            'comment' => $validatedRequest['comment'],
            'user_id' => Auth::user()->id,
            'state' => 'open',
            'attachment' => $path,
            'categories' => $validatedRequest['categories'] ?? null
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

    public function reopen(Ticket $ticket, Request $request) {
        if($ticket->user_id !== Auth::user()->id) {
            abort(Response::HTTP_FORBIDDEN, 'you do not have access to this ticket');
        }

        if($ticket->state !== 'closed'){
                return response()->json([
                    'message' => 'Cannot reopen an open ticket'
                ],Response::HTTP_BAD_REQUEST);
        }
        $validatedRequest = $request->validate([
            'comment' => 'required|string'
        ]);
        Reply::create([
            'ticket_id' => $ticket->id,
            'comment' => $validatedRequest['comment'],
            'user_id' => Auth::user()->id
        ]);

        $ticket->state = 'open';
        $ticket->save();
        $ticket->load('replies');

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
        $tickets = Ticket::with('replies')
                        ->where('user_id', $userId)
                        ->where('state', $state)
                        ->orderBy('updated_at', 'desc')
                        ->paginate($perPage);

        return response()->json($tickets);
    }
}
