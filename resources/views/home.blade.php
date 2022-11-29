@extends('layouts.app')
@section('onDefault')
    <h2>Der Admin hat Ihnen noch keine Rolle zugewiesen</h2>
@endsection
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

    <div class="accordion accordion-flush container center_div" id="tankTable">

        @foreach ($storageTanks as $storagetank)
        @php
            $insertValue = $storagetank->tankConstruction()->number_of_inserts;
            $tubeValue   = $storagetank->tankConstruction()->number_of_tubes;
            $sampleValue = $storagetank->tankConstruction()->number_of_samples;
            $tankCapacity = $storagetank->tankConstruction()->capacity;
            $sample_nr=$samples->where('pos_tank_nr', $storagetank->tank_name)->count('pos_tank_nr');
            
            $fill = round((1 / $tankCapacity) * $samples->where('pos_tank_nr', $storagetank->tank_name)->count('pos_tank_nr') * 100);
            @endphp
            <div class="accordion-item ">
                <h2 class="accordion-header" id="tank{{ $storagetank->tank_name }}">
                    <div class="progress">
                        @switch($fill)
                            @case($fill <= 79)
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $fill }}%;"
                                    aria-valuenow="{{ $fill }}" aria-valuemin="0" aria-valuemax="100">{{ $sample_nr }}/{{ $tankCapacity }} Proben belegt
                                </div>
                            @break

                            @case($fill > 79 && $fill <= 99)
                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $fill }}%;"
                                    aria-valuenow="{{ $fill }}" aria-valuemin="0" aria-valuemax="100">{{ $sample_nr }}/{{ $tankCapacity }} Proben belegt
                                </div>
                            @break

                            @case($fill > 99)
                                <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $fill }}%;"
                                    aria-valuenow="{{ $fill }}" aria-valuemin="0" aria-valuemax="100">{{ $sample_nr }}/{{ $tankCapacity }} Proben belegt
                                </div>
                            @break
                        @endswitch

                    </div>
                    <button class="accordion-button collapsed " type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseTank{{ $storagetank->id }}" aria-expanded="false"
                        aria-controls="collapseTank{{ $storagetank->id }}">

                        @if ($samples->where('pos_tank_nr', $storagetank->tank_name)->count('pos_insert') == $tankCapacity)
                            <div class="bg-danger p-2 badge bg-primary text-wrap"> Tank {{ $storagetank->tank_name }}
                            </div>
                        @elseif ($fill > 79 && $fill <= 99)
                            <div class="bg-warning p-2 badge bg-primary text-wrap"> Tank {{ $storagetank->tank_name }}
                            </div>
                        @else
                            <div class="bg-success p-2 badge bg-primary text-wrap"> Tank {{ $storagetank->tank_name }}
                            </div>
                        @endif

                    </button>

                </h2>
                <div id="collapseTank{{ $storagetank->id }}" class="accordion-collapse collapse"
                    aria-labelledby="tank{{ $storagetank->id }}" data-bs-parent="#tankTable">
                    <div class="accordion-body " >
                        <!-- Container Logik -->
                        <div class="accordion accordion-flush" id="containerTable{{ $storagetank->id }}">
                            @for ($insert = 1; $insert <= $insertValue; $insert++)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="container{{ $insert }}">
                                        <button class="accordion-button collapsed " type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapsecontainer{{ $storagetank->id }}{{ $insert }}"
                                            aria-expanded="true" aria-controls="collapsecontainer{{ $insert }}">

                                            @if ($samples->where('pos_tank_nr', $storagetank->tank_name)->where('pos_insert', $insert)->count('pos_tube') == $tubeValue * $sampleValue)
                                                <div class="bg-danger p-2 badge bg-primary text-wrap"> Container
                                                    {{ $insert }} </div>
                                            @else
                                                <div class="bg-success p-2 badge bg-primary text-wrap"> Container
                                                    {{ $insert }} </div>
                                            @endif

                                        </button>
                                    </h2>
                                    <div id="collapsecontainer{{ $storagetank->id }}{{ $insert }}"
                                        class="accordion-collapse collapse toggle"
                                        aria-labelledby="container{{ $insert }}"
                                        data-bs-parent="#containerTable{{ $storagetank->id }}">
                                        <div class="accordion-body">
                                            <!-- Tube Logik -->

                                            <div class="accordion accordion-flush"
                                                id="insertTable{{ $storagetank->id }}{{ $insert }}">
                                                @for ($tubes = 1; $tubes <= $tubeValue; $tubes++)
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="insert{{ $tubes }}">
                                                            <button class="accordion-button " type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapseinsert{{ $storagetank->id }}{{ $insert }}{{ $tubes }}"
                                                                aria-expanded="true"
                                                                aria-controls="collapse{{ $tubes }}">
                                                                @if ($samples->where('pos_tank_nr', $storagetank->tank_name)->where('pos_insert', $insert)->where('pos_tube', $tubes)->count('pos_smpl') == $sampleValue)
                                                                    <div class="bg-danger p-2 badge bg-primary text-wrap">
                                                                        Einsatz {{ $tubes }} </div>
                                                                @else
                                                                    <div class="bg-success p-2 badge bg-primary text-wrap">
                                                                        Einsatz {{ $tubes }} </div>
                                                                @endif
                                                            </button>
                                                        </h2>
                                                        <div id="collapseinsert{{ $storagetank->id }}{{ $insert }}{{ $tubes }}"
                                                            class="accordion-collapse collapse"
                                                            aria-labelledby="insert{{ $tubes }}"
                                                            data-bs-parent="#insertTable{{ $storagetank->id }}{{ $insert }}">
                                                            <div class="accordion-body">
                                                                <!-- Sample Logik-->
                                                                @for ($sample = 1; $sample <= $sampleValue; $sample++)
                                                                    @php
                                                                        $selecetedSample = $samples
                                                                            ->where('pos_tank_nr', $storagetank->tank_name)
                                                                            ->where('pos_insert', $insert)
                                                                            ->where('pos_tube', $tubes)
                                                                            ->where('pos_smpl', $sample);
                                                                    @endphp
                                                                    <div class="btn-group">

                                                                        @if ($selecetedSample->value('pos_smpl') == $sample)
                                                                            <button type="button"
                                                                                class="btn btn-danger dropdown-toggle"
                                                                                data-bs-toggle="dropdown"
                                                                                aria-expanded="false"> Probe
                                                                                {{ $sample }} </button>
                                                                            <ul class="dropdown-menu">
                                                                                <li><a class="dropdown-item">{{ $selecetedSample->value('B_number') }}
                                                                                    </a></li>
                                                                                <li><a class="dropdown-item">{{ $selecetedSample->value('responsible_person') }}
                                                                                    </a></li>
                                                                                <li><a class="dropdown-item">{{ $selecetedSample->value('type_of_material') }}
                                                                                    </a></li>
                                                                                <li><a class="dropdown-item">{{ $selecetedSample->value('storage_date') }}
                                                                                    </a></li>
                                                                                <li>
                                                                                <li><a class="dropdown-item">Kommentar: {{ $selecetedSample->value('commentary') }}
                                                                                    </a></li>
                                                                                <li>
                                                                                    <hr class="dropdown-divider">
                                                                                </li>
                                                                                <li>
                                                                                    <form method="POST"
                                                                                        action="{{ Url('/shipped') }}">
                                                                                        @csrf
                                                                                <li>
                                                                                    <td>
                                                                                        <div class="col">
                                                                                            <input required type="text"
                                                                                                class="form-control"
                                                                                                placeholder="Verschicken nach"
                                                                                                name="address"
                                                                                                autofocus="autofocus">
                                                                                        </div>
                                                                                    </td>
                                                                                    <button type="submit"
                                                                                        class="dropdown-item"> Probe
                                                                                        verschicken
                                                                                        <input type="text"
                                                                                            value="{{ $selecetedSample->value('id') }}"name="sample_id"
                                                                                            hidden>
                                                                                    </button>
                                                                                </li>
                                                                                </form>
                                                                                </li>
                                                                                <li>
                                                                                    <form method="POST"
                                                                                        action="{{ Url('/transfer') }}">
                                                                                        @csrf
                                                                                <li>
                                                                                    <button type="submit"
                                                                                        class="dropdown-item"> Probe
                                                                                        entfernen
                                                                                        <input type="text"
                                                                                            value="{{ $selecetedSample->value('id') }}"name="sample_id"
                                                                                            hidden>
                                                                                    </button>
                                                                                </li>
                                                                                </form>
                                                                                </li>
                                                                            </ul>
                                                                        @else
                                                                            <button type="button"
                                                                                class="btn btn-success dropdown-toggle"
                                                                                data-bs-toggle="dropdown"
                                                                                aria-expanded="false"> Position
                                                                                {{ $sample }} </button>
                                                                            <ul class="dropdown-menu">
                                                                                <form method="POST"
                                                                                    action="{{ Url('newSamples/pos') }}">
                                                                                    @csrf
                                                                                    <li>
                                                                                        <button type="submit"
                                                                                            class="dropdown-item"> Probe
                                                                                            einlagern
                                                                                            <input type="text"
                                                                                                value="{{ $storagetank->tank_name }}"
                                                                                                name="tank_pos" hidden>
                                                                                            <input type="text"
                                                                                                value="{{ $insert }}"
                                                                                                name="con_pos" hidden>
                                                                                            <input type="text"
                                                                                                value="{{ $tubes }}"
                                                                                                name="insert_pos" hidden>
                                                                                            <input type="text"
                                                                                                value="{{ $sample }}"
                                                                                                name="sample_pos" hidden>
                                                                                        </button>
                                                                                    </li>
                                                                                </form>
                                                                                <li>
                                                                                    <form method="POST"
                                                                                        action="{{ Url('/restore') }}">
                                                                                        @csrf
                                                                                <li>
                                                                                    <button type="submit"
                                                                                        class="dropdown-item"> Erneut
                                                                                        einlagern
                                                                                        <input type="text"
                                                                                            value="{{ $storagetank->tank_name }}"
                                                                                            name="tank_pos" hidden>
                                                                                        <input type="text"
                                                                                            value="{{ $insert }}"
                                                                                            name="con_pos" hidden>
                                                                                        <input type="text"
                                                                                            value="{{ $tubes }}"
                                                                                            name="insert_pos" hidden>
                                                                                        <input type="text"
                                                                                            value="{{ $sample }}"
                                                                                            name="sample_pos" hidden>
                                                                                    </button>
                                                                                </li>
                                                                                </form>
                                                                                </li>
                                                                            </ul>
                                                                        @endif


                                                                    </div>
                                                                @endfor

                                                            </div>
                                                        </div>
                                                    </div>
                                                @endfor
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>

                    </div>
                </div>
            </div>
        @endforeach
    </div>

@endsection
