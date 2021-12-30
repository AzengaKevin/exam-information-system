<div>

    <x-feedback />

    <div class="card shadow-sm">
        <div class="card-body">
            @if ($messages->count())
            <div class="accordion accordion-flush" id="messagesAccordion">

                @foreach ($messages as $message)             
                <div class="accordion-item bg-white">
                    <h2 class="accordion-header" id="message-heading-{{ $loop->iteration }}">
                        <button class="accordion-button bg-white d-inline-flex gap-2" type="button" data-bs-toggle="collapse" data-bs-target="#message-collapse-{{ $loop->iteration }}"
                            aria-expanded="true" aria-controls="message-collapse-{{ $loop->iteration }}">
                            <span>{{ $user->is($message->sender) ? $message->recipient->name : $message->sender->name }}</span>
                            @if ($user->is($message->sender))
                            <span class="badge bg-primary">Sent</span>
                            @else
                            <span class="badge bg-success">Received</span>
                            @endif
                        </button>
                    </h2>
                    <div id="message-collapse-{{ $loop->iteration }}" class="accordion-collapse collapse @if($loop->first) show @endif" aria-labelledby="message-heading-{{ $loop->iteration }}"
                        data-bs-parent="#messagesAccordion">
                        <div class="accordion-body">{{ $message->content }}</div>
                    </div>
                </div>
                @endforeach

                <div class="mt-3">
                    {{ $messages->links() }}
                </div>

            </div>
            @else
            <p class="lead m-0">No message sent or received yet </p>
            @endif
        </div>
    </div>

    <x-modals.messages.upsert :messageId="$messageId" :users="$users" />
</div>