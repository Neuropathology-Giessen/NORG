@extends('layouts.app')

@section('content')
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    @if (Auth::user()->role == 'Arzt' || Auth::user()->role == 'Sekretariat')
        <script>
            window.location = "/sampleList";
        </script>
    @endif

    <div>
        <table class="table table-hover text-center">
            <thead>
                <tr>
                    <th scope="col">Tank Name</th>
                    <th scope="col">Model</th>
                    <th scope="col">Hinzugefügt am</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($activeTanks as $activeTank)
                    <tr>
                        <th scope="row">{{ $activeTank->tank_name }}</th>
                        <th scope="row">{{ $activeTank->modelname }}</th>
                        <th scope="row">{{ $activeTank->created_at }}</th>                        
                        <form onsubmit="return confirm('Sicher das dieser Tank Entfernt werden soll?');" method="POST"
                            action="{{ Url('/tankDestroy') }}">
                            @csrf

                            @php
                            $sample = $allSamplesinTank;
                            $group = $sample->where('pos_tank_nr', $activeTank->tank_name);
                            @endphp

                            @if ($group->count() == 0)
                            <th>
                                <button type="submit" class="btn btn-outline-secondary"> Tank Entfernen
                                    <input value="{{ $activeTank->id }}"name="tank_id" hidden>
                                </button>
                                <th>
                                    <button type="button" class="btn btn-success" disabled>Der Tank ist leer</button>
                                </th>
                            </th>
                            @else
                            <th>
                                <button type="submit" class="btn btn-outline-secondary" disabled> Tank Entfernen
                                </button>
                            </th>
                            <th>
                                <button type="button" class="btn btn-danger" disabled>Es sind noch Porben im
                                    Tank</button>
                            </th>
                            @endif

                </form>
                </td> {{-- sollte auf gleiches zugreifen wie in Home --}}
            </tr>
        @endforeach
    </tbody>
</table>
</div>

<div id="liveAlertPlaceholder"></div>
<form action="{{ url('/addTank') }}" method="POST">
@csrf
<div class="col">
    <div class="col">
        <label class="form-label">Tank Name</label>
        <input required type="text" class="form-control" name="tank_name">
        @error('tank_name')
            {{ $message }}
        @enderror
        <br>
    </div>
    <div class="col">
        <label for="MaterialSelect" class="form-label">Model</label>
        <select required id="MaterialSelect" class="form-select" name="modelname">
            <option disabled selected value> -- Bitte Modeltype wählen durch anklicken -- </option>
            @foreach ($tankModel as $tank)
                <option>{{ $tank->modelname }}</option>
            @endforeach
        </select>
        @error('modelname')
            {{ $message }}
        @enderror
        <br>
    </div>
    <div class="col">
        Datum
    </div>
    <div class="col">
        <p><b>
                <script>
                    document.write(new Date().toLocaleDateString())
                </script>
            </b></p>
    </div>
    <div class="col">
        <div class="form-check">
            <input required class="form-check-input" type="checkbox" id="gridCheck">
            <label class="form-check-label" for="gridCheck">
                Daten überprüft?
            </label>
        </div>
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-primary">Tank hinzufügen</button>
    </div>
</div>
</form>

<hr>

<h3 class="col-md-8 fs-4">Anlage eines neuen Tank Modeltypen</h3>
<form action="{{ url('/addTankmodel') }}" method="POST">
@csrf
<div class="col">
    <div class="col">
        <label class="form-label">Modeltype</label>
        <input required type="text" class="form-control" name="modelname">
        @error('modelname')
            {{ $message }}
        @enderror
        <br>
    </div>
    <div class="col">
        <label class="form-label">Manufacturer</label>
        <input required type="integer" class="form-control" name="manufacturer">
        @error('manufacturer')
            {{ $message }}
        @enderror
        <br>
    </div>
    <div class="col">
        <label class="form-label">Number of Container</label>
        <input required type="integer" class="form-control" name="number_of_inserts">
        @error('number_of_inserts')
            {{ $message }}
        @enderror
        <br>
    </div>
    <div class="col">
        <label class="form-label">Number of tubes</label>
        <input required type="integer" class="form-control" name="number_of_tubes">
        @error('number_of_tubes')
            {{ $message }}
        @enderror
        <br>
    </div>
    <div class="col">
        <label class="form-label">Number of samples</label>
        <input required type="integer" class="form-control" name="number_of_samples">
        @error('number_of_samples')
            {{ $message }}
        @enderror
        <br>
    </div>
    <div class="col">
        Datum
    </div>
    <div class="col">
        <p><b>
                <script>
                    document.write(new Date().toLocaleDateString())
                </script>
            </b></p>
    </div>
    <div class="col">
        <div class="form-check">
            <input required class="form-check-input" type="checkbox" id="gridCheck">
            <label class="form-check-label" for="gridCheck">
                Daten überprüft?
            </label>
        </div>
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-primary">Modeltypen hinzufügen</button>
    </div>
</div>
</form>
@endsection
