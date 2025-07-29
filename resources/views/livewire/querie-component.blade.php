@section('title', 'Queries | IoT Platform')

<section class="queries" role="region" aria-label="Query Dashboard Section">

    <form class="queries__element queries__element--type-form"
        wire:submit.prevent="querie"
        role="search"
        aria-labelledby="queries-title">

        <div class="queries__form-group">
            <h2 id="queries-title" class="queries__form-title">Queries</h2>
        </div>

        <div class="queries__form-group">
            <div class="queries__form-datetime queries__form-datetime--start">
                <label for="start_date" class="queries__form-label">Start date:</label>
                <input type="date" id="start_date" wire:model.defer="startDate" class="queries__form-input" required value="2025-07-18">

                <label for="start_time" class="queries__form-label">Start time:</label>
                <input type="time" id="start_time" wire:model.defer="startTime" class="queries__form-input" required value="00:00">
            </div>

            <div class="queries__form-datetime queries__form-datetime--end">
                <label for="end_date" class="queries__form-label">End date:</label>
                <input type="date" id="end_date" wire:model.defer="endDate" class="queries__form-input" required value="2025-07-19">

                <label for="end_time" class="queries__form-label">End time:</label>
                <input type="time" id="end_time" wire:model.defer="endTime" class="queries__form-input" required value="23:59">
            </div>
        </div>

        <div class="queries__form-group">
            <button type="button" wire:click="clearForm" id="clear-form-button" class="queries__form-submit-button queries__form-submit--clear-button" aria-label="Clear query">Clear</button>
            <button type="submit" class="queries__form-submit-button queries__form-submit--consult-button" aria-label="Submit query">Consultar</button>
        </div>

    </form>


    <aside class="queries__element queries__element--type-table-wrapper" role="complementary" aria-label="Results Table">

        <table class="queries__table" role="table" aria-describedby="table-caption">
            <caption id="table-caption" class="queries__table-caption" lang="en">Records</caption>

            <thead class="queries__table-thead">
                <tr>
                    <th>Temperature</th>
                    <th>Humidity</th>
                    <th>Datetime</th>
                    <th>Threshold Temperature Type</th>
                    <th>Threshold Humidity Type</th>
                    <th>Thresholds Defined</th>
                </tr>
            </thead>

            <tbody class="queries__table-tbody">
                @foreach($querie as $record)
                    <tr>
                        <td>{{ $record->temperature }} °C</td>
                        <td>{{ $record->humidity }} %</td>
                        <td>{{ $record->measured_at }}</td>
                        <td data-threshold="{{ $record->threshold_temp_type }}">{{ $record->threshold_temp_type }}</td>
                        <td data-threshold="{{ $record->threshold_humidity_type }}">{{ $record->threshold_humidity_type }}</td>
                        <td>{{ $record->thresholds_defined }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </aside>

</section>