<div class="kpi">
    @foreach ($kpi->activeSections as $sectionIndex => $section)
        <div class="card border border-primary section-tab" data-section-page="{{ floor($sectionIndex / 3) }}"
            style="border-radius: 10px; display: none;">
            <div class="card-body {{ $section->metrics->isEmpty() ? 'empty-section' : 'metric-card' }}">
                <div class="section-card" style="margin-top: 1rem;">
                    <h4 class="card-title">
                        {{ $section->sectionName }}
                        (<span style="color: #c80f0f">{{ $section->sectionScore }}</span>)
                    </h4>
                    <p>{{ $section->sectionDescription }}</p>

                    @if ($section->metrics->isEmpty())
                        @include('partials.employee.section-form', [
                            'section' => $section,
                            'kpi' => $kpi,
                            'kpiStatus' => $kpiStatus,
                        ])
                    @else
                        @foreach ($section->metrics as $metric)
                            @include('partials.employee.metric-form', [
                                'metric' => $metric,
                                'section' => $section,
                                'kpi' => $kpi,
                                'kpiStatus' => $kpiStatus,
                            ])
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
