<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class MessageController extends Controller
{
    /**
     * Get user's conversations
     */
    public function getConversations(Request $request)
    {
        $user = $request->user();

        $query = Conversation::query();

        if ($user->isPatient()) {
            $query->where('patient_id', $user->patient->id);
        } elseif ($user->isMedecin()) {
            $query->where('medecin_id', $user->medecin->id);
        }

        $conversations = $query->with(['patient.user', 'medecin.user', 'messages' => function($q) {
                $q->latest()->limit(1);
            }])
            ->orderByDesc('last_message_at')
            ->get();

        return response()->json($conversations);
    }

    /**
     * Get conversation messages
     */
    public function getMessages(Request $request, $conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);

        // Verify access
        $user = $request->user();
        if ($user->isPatient() && $conversation->patient->user_id !== $user->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        if ($user->isMedecin() && $conversation->medecin->user_id !== $user->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $messages = Message::where('conversation_id', $conversationId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->paginate(50);

        // Decrypt messages
        $messages->getCollection()->transform(function ($message) {
            try {
                $message->message = Crypt::decryptString($message->message_encrypted);
            } catch (\Exception $e) {
                $message->message = '[Message chiffré]';
            }
            return $message;
        });

        // Mark as read
        Message::where('conversation_id', $conversationId)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return response()->json($messages);
    }

    /**
     * Send message
     */
    public function sendMessage(Request $request, $conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);

        // Verify access
        $user = $request->user();
        if ($user->isPatient() && $conversation->patient->user_id !== $user->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        if ($user->isMedecin() && $conversation->medecin->user_id !== $user->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $request->validate([
            'message' => 'required|string|max:2000',
            'attachments' => 'nullable|array'
        ]);

        // Encrypt message
        $encryptedMessage = Crypt::encryptString($request->message);

        $message = Message::create([
            'conversation_id' => $conversationId,
            'sender_id' => $user->id,
            'message_encrypted' => $encryptedMessage,
            'attachments' => $request->attachments
        ]);

        // Update conversation
        $conversation->update([
            'last_message_at' => now(),
            'is_active' => true
        ]);

        // Decrypt for response
        $message->message = $request->message;

        return response()->json([
            'message' => 'Message envoyé',
            'data' => $message
        ], 201);
    }

    /**
     * Create conversation (between patient and medecin after first consultation)
     */
    public function createConversation(Request $request)
    {
        $request->validate([
            'medecin_id' => 'required|exists:medecins,id',
            'appointment_id' => 'nullable|exists:appointments,id'
        ]);

        $user = $request->user();
        if (!$user->isPatient()) {
            return response()->json(['message' => 'Seuls les patients peuvent créer une conversation'], 403);
        }

        $patientId = $user->patient->id;

        // Check if conversation already exists
        $conversation = Conversation::where('patient_id', $patientId)
            ->where('medecin_id', $request->medecin_id)
            ->first();

        if ($conversation) {
            return response()->json([
                'message' => 'Conversation existante',
                'conversation' => $conversation
            ]);
        }

        $conversation = Conversation::create([
            'patient_id' => $patientId,
            'medecin_id' => $request->medecin_id,
            'appointment_id' => $request->appointment_id,
            'is_active' => true
        ]);

        return response()->json([
            'message' => 'Conversation créée',
            'conversation' => $conversation
        ], 201);
    }

    /**
     * Get unread count
     */
    public function getUnreadCount(Request $request)
    {
        $user = $request->user();

        $query = Conversation::query();

        if ($user->isPatient()) {
            $query->where('patient_id', $user->patient->id);
        } elseif ($user->isMedecin()) {
            $query->where('medecin_id', $user->medecin->id);
        }

        $conversationIds = $query->pluck('id');

        $unreadCount = Message::whereIn('conversation_id', $conversationIds)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json(['unread_count' => $unreadCount]);
    }
}
