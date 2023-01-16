<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormRequest;
use App\Models\Form;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class FormController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Form::class);

        return view('forms.form_index', [
            'forms' => Form::query()->paginate(),
        ]);
    }

    public function show(Form $form): View
    {
        $this->authorize('view', $form);

        return view('forms.form_show', [
            'form' => $form,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Form::class);

        return view('forms.form_form');
    }

    public function store(FormRequest $request): RedirectResponse
    {
        $this->authorize('create', Form::class);

        $form = new Form();
        if ($form->fillAndSave($request->validated())) {
            Session::flash('success', __('Saved successfully.'));
            return redirect(route('forms.edit', $form));
        }

        return back();
    }

    public function edit(Form $form): View
    {
        $this->authorize('update', $form);

        return view('forms.form_form', [
            'form' => $form->loadMissing([
                'bookingOptions.event',
            ]),
        ]);
    }

    public function update(Form $form, FormRequest $request): RedirectResponse
    {
        $this->authorize('update', $form);

        if ($form->fillAndSave($request->validated())) {
            Session::flash('success', __('Saved successfully.'));
        }

        return back();
    }
}
