@php
    use App\Enums\ApprovalStatus;
    use App\Enums\FileType;
    use App\Enums\FilterValue;
    use App\Models\Document;
@endphp
<x-form.filter>
    <div class="row">
        <div class="col-12 col-xl-3">
            <x-bs::form.field id="search" name="filter[search]" type="text"
                              :from-query="true"><i class="fa fa-fw fa-search"></i> {{ __('Search term') }}</x-bs::form.field>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <x-bs::form.field id="file_type" name="filter[file_type]" type="select"
                              :options="FileType::toOptionsWithAll()"
                              :from-query="true"><i class="fa fa-fw fa-file-circle-question"></i> {{ __('File type') }}</x-bs::form.field>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <x-bs::form.field id="approval_status" name="filter[approval_status]" type="select"
                              :options="ApprovalStatus::toOptionsWithAll()"
                              :cast="FilterValue::castToIntIfNoValue()"
                              :from-query="true"><i class="fa fa-fw fa-circle-question"></i> {{ __('Approval status') }}</x-bs::form.field>
        </div>
        <div class="col-12 col-lg-6 col-xl-3">
            <x-bs::form.field name="sort" type="select"
                              :options="Document::sortOptions()->getNamesWithLabels()"
                              :from-query="true"><i class="fa fa-fw fa-sort"></i> {{ __('Sorting') }}</x-bs::form.field>
        </div>
    </div>
</x-form.filter>
