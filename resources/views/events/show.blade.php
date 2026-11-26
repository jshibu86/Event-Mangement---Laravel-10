
@extends('layouts.app')

@section('content')

    @include('components.datetime-picker-assets')

    <style>
        .star-picker {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            gap: .35rem;
        }

        .star-picker input {
            position: absolute;
            opacity: 0;
        }

        .star-picker label {
            font-size: 2rem;
            line-height: 1;
            color: #b7adbf;
            cursor: pointer;
        }

        .star-picker label:hover,
        .star-picker label:hover ~ label,
        .star-picker input:checked ~ label {
            color: #f2ad25;
        }

        .expense-row {
            border-bottom: 1px solid var(--line);
            padding: .55rem 0;
        }

        .expense-row:last-child {
            border: 0;
        }
    </style>

    {{-- Event Header --}}
    <div class="hero p-4 mb-4">
        <div class="d-flex justify-content-between">
            <div>
                <span class="badge text-bg-light">
                    {{ $event->status }}
                </span>

                <h2 class="mt-2 mb-1">
                    {{ $event->name }}
                </h2>

                <p class="mb-0">
                    {{ $event->event_date->format('l, d F Y') }}
                    ·
                    {{ $event->venue }}
                </p>
            </div>

            <div>
                @if ($event->status === 'verified')
                    <a
                        target="_blank"
                        class="btn btn-light me-2"
                        href="{{ route('events.pdf', [$event, 'guest']) }}"
                    >
                        Guest PDF
                    </a>

                    <a
                        target="_blank"
                        class="btn btn-outline-light"
                        href="{{ route('events.pdf', [$event, 'internal']) }}"
                    >
                        Internal PDF
                    </a>
                @else
                    <form
                        class="d-inline"
                        method="POST"
                        action="{{ route('events.verify', $event) }}"
                    >
                        @csrf

                        <button
                            class="btn btn-light"
                            onclick="return confirm('Verify and lock this event?')"
                        >
                            Verify event
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Main Event Content --}}
    <div class="row g-4">

        {{-- Agenda Timeline --}}
        <div class="col-lg-7">
            <div class="card p-4 mb-4">
                <h4>Agenda timeline</h4>

                <div class="timeline ms-3 mt-3">
                    @forelse ($event->agendaItems as $slot)
                        <div class="timeline-item ps-4 py-2">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>
                                        {{ $slot->title }}
                                    </strong>

                                    <span class="badge text-bg-light">
                                        {{ $slot->item_type }}
                                    </span>

                                    <br>

                                    <small class="text-muted">
                                        {{ $slot->start_time }}

                                        @if ($slot->end_time)
                                            – {{ $slot->end_time }}
                                        @endif

                                        · ₹{{ number_format($slot->expense_amount) }}
                                    </small>
                                </div>

                                @if ($event->status === 'draft')
                                    <form
                                        method="POST"
                                        action="{{ route('events.agenda.remove', [$event, $slot]) }}"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button class="btn btn-sm btn-link text-danger">
                                            Remove
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">
                            No moments added yet.
                        </p>
                    @endforelse
                </div>
            </div>

            {{-- Add Agenda Item --}}
            @if ($event->status === 'draft')
                <div class="card p-4">
                    <h5>Add a moment</h5>

                    <form
                        class="row g-2"
                        method="POST"
                        action="{{ route('events.agenda.add', $event) }}"
                    >
                        @csrf

                        <div class="col-md-4">
                            <select
                                class="form-select select2"
                                name="item_id"
                                required
                            >
                                <optgroup label="Games">
                                    @foreach ($games as $x)
                                        <option value="{{ $x->id }}">
                                            {{ $x->name }}
                                            · ★{{ number_format($x->average_rating, 1) }}
                                            ({{ $x->times_played }})
                                        </option>
                                    @endforeach
                                </optgroup>

                                <optgroup label="Programs">
                                    @foreach ($programs as $x)
                                        <option value="p{{ $x->id }}">
                                            {{ $x->name }}
                                            · ★{{ number_format($x->average_rating, 1) }}
                                            ({{ $x->times_played }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <input
                                class="form-control"
                                type="time"
                                name="start_time"
                                required
                            >
                        </div>

                        <div class="col-md-2">
                            <input
                                class="form-control"
                                type="time"
                                name="end_time"
                            >
                        </div>

                        <div class="col-md-2">
                            <input
                                class="form-control"
                                type="number"
                                step=".01"
                                name="expense_amount"
                                placeholder="Cost"
                            >
                        </div>

                        <div class="col-md-2">
                            <button class="btn btn-primary w-100">
                                Add
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>

        {{-- Budget and Rating --}}
        <div class="col-lg-5">

            {{-- Budget Pulse --}}
            <div class="card p-4 mb-4">
                <h4>Budget pulse</h4>

                <div class="d-flex justify-content-between">
                    <span>Agenda costs</span>

                    <strong>
                        ₹{{ number_format($event->agenda_total) }}
                    </strong>
                </div>

                <div class="d-flex justify-content-between">
                    <span>Extra costs</span>

                    <strong>
                        ₹{{ number_format($event->expenses->sum('amount')) }}
                    </strong>
                </div>

                @if ($event->expenses->isNotEmpty())
                    <div class="mt-2 mb-2">
                        <small class="text-muted text-uppercase">
                            Extra expense details
                        </small>

                        @foreach ($event->expenses as $expense)
                            <div class="expense-row d-flex justify-content-between">
                                <span>
                                    <strong>
                                        {{ $expense->title }}
                                    </strong>

                                    @if ($expense->category)
                                        <small class="d-block text-muted">
                                            {{ $expense->category }}
                                        </small>
                                    @endif
                                </span>

                                <span class="text-end">
                                    <strong>
                                        ₹{{ number_format($expense->amount) }}
                                    </strong>

                                    @if ($url = $expense->mediaUrl('bills'))
                                        <a
                                            href="{{ $url }}"
                                            target="_blank"
                                            rel="noopener"
                                            class="d-block small"
                                        >
                                            View bill
                                        </a>
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <hr>

                <div class="d-flex justify-content-between fs-5">
                    <strong>Grand total</strong>

                    <strong>
                        ₹{{ number_format($event->grand_total) }}
                    </strong>
                </div>

                @if ($event->status === 'draft')
                    {{-- Set Budget --}}
                    <form
                        class="mt-3"
                        method="POST"
                        action="{{ route('events.budget', $event) }}"
                    >
                        @csrf

                        <label>Budget cap</label>

                        <div class="input-group">
                            <input
                                class="form-control"
                                type="number"
                                step=".01"
                                name="total_budget"
                                value="{{ $event->budget?->total_budget }}"
                            >

                            <button class="btn btn-outline-primary">
                                Save
                            </button>
                        </div>
                    </form>

                    {{-- Add Extra Expense --}}
                    <form
                        class="row g-2 mt-2"
                        method="POST"
                        action="{{ route('events.expenses', $event) }}"
                        enctype="multipart/form-data"
                    >
                        @csrf

                        <div class="col-6">
                            <input
                                class="form-control"
                                name="title"
                                placeholder="Extra expense name"
                                required
                            >
                        </div>

                        <div class="col-3">
                            <input
                                class="form-control"
                                name="category"
                                placeholder="Category"
                            >
                        </div>

                        <div class="col-3">
                            <input
                                class="form-control"
                                type="number"
                                step=".01"
                                name="amount"
                                placeholder="₹"
                                required
                            >
                        </div>

                        <div class="col-12">
                            <input
                                class="form-control form-control-sm @error('bill') is-invalid @enderror"
                                type="file"
                                name="bill"
                                accept=".jpg,.jpeg,.png,.pdf"
                            >
                            @error('bill')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Optional bill (JPG, PNG or PDF, max 5 MB)</small>
                        </div>

                        <div class="col-12">
                            <button class="btn btn-sm btn-outline-primary">
                                Add expense
                            </button>
                        </div>
                    </form>
                @endif
            </div>

            {{-- Rate the Event --}}
            <div class="card p-4">
                <h4>Rate the day</h4>

                <form
                    method="POST"
                    action="{{ route('events.rate', $event) }}"
                >
                    @csrf

                    <select
                        class="form-select select2 mb-3"
                        name="event_agenda_item_id"
                        required
                    >
                        <option value="">
                            Choose an agenda item
                        </option>

                        @foreach ($event->agendaItems as $slot)
                            <option value="{{ $slot->id }}">
                                {{ $slot->title }}
                            </option>
                        @endforeach
                    </select>

                    <p class="mb-1 mt-2">
                        Your rating
                    </p>

                    <div class="star-picker mb-3">
                        <input
                            id="star5"
                            type="radio"
                            name="rating_value"
                            value="5"
                            checked
                        >
                        <label for="star5">★</label>

                        <input
                            id="star4"
                            type="radio"
                            name="rating_value"
                            value="4"
                        >
                        <label for="star4">★</label>

                        <input
                            id="star3"
                            type="radio"
                            name="rating_value"
                            value="3"
                        >
                        <label for="star3">★</label>

                        <input
                            id="star2"
                            type="radio"
                            name="rating_value"
                            value="2"
                        >
                        <label for="star2">★</label>

                        <input
                            id="star1"
                            type="radio"
                            name="rating_value"
                            value="1"
                        >
                        <label for="star1">★</label>
                    </div>

                    <textarea
                        class="form-control mb-2"
                        name="feedback"
                        placeholder="What landed well?"
                    ></textarea>

                    <button class="btn btn-primary">
                        Submit rating
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Agenda Edit Link --}}
    <script>
        document.querySelectorAll('form[action*="/agenda/"]').forEach(form => {
            if (
                form.querySelector(
                    'input[name="_method"][value="DELETE"]'
                )
            ) {
                const edit = document.createElement('a');

                edit.href = form.action + '/edit';
                edit.className = 'btn btn-sm btn-link text-primary me-1';
                edit.textContent = 'Edit';

                form.parentNode.insertBefore(edit, form);
            }
        });
    </script>

@endsection
