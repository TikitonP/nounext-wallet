<?php

namespace App\Http\Controllers\App;

use App\Models\User;
use App\Models\Account;
use App\Traits\PaginationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\AccountRequest;
use Illuminate\Validation\ValidationException;

class AccountController extends Controller
{

    use PaginationTrait;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $language
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $language)
    {
        $accounts = Auth::user()->accounts->sortByDesc('updated_at');

        $this->parginate($request, $accounts);
        return view('accounts.index', ['paginationTools' => $this->paginationTools]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('accounts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AccountRequest $request
     * @return \Illuminate\Http\Response
     * @throws ValidationException
     */
    public function store(AccountRequest $request)
    {  
        $this->accountExist($request->name);

        Auth::user()->accounts()->create($request->input());

        flash_message(
            __('general.success'), 'Compte ajouté avec succès',
            'success', 'oi oi-thumb-up'
        );

        return redirect($this->redirectTo());
    }

    /**
     * Display the specified resource.
     *
     * @param $language
     * @param Account $account
     * @return \Illuminate\Http\Response
     */
    public function show($language, Account $account)
    {
        return view('accounts.show', compact('account'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $language
     * @param Account $account
     * @return \Illuminate\Http\Response
     */
    public function edit($language, Account $account)
    { 
        return view('accounts.edit', compact('account'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AccountRequest $request
     * @param $language
     * @param Account $account
     * @return \Illuminate\Http\Response
     * @throws ValidationException
     */
    public function update(AccountRequest $request, $language, Account $account)
    {
        $this->accountExist($request->name, $account->id);

        $account->update($request->all());

        flash_message(
            __('general.success'), 'Compte modifié avec succès.',
            'success', 'oi oi-thumb-up'
        );

        return redirect($this->redirectTo());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $language
     * @param Account $account
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy($language, Account $account)
    {
        $account->delete();

        flash_message(
            'Information', 'Compte supprimé avec succès.'
        );

        return redirect($this->redirectTo());
    }

    /**
     * Check if the account already exist
     *
     * @param  string $name
     * @param int $account_id
     * @return void
     * @throws ValidationException
     */
    private function accountExist($name, $account_id = 0)
    {
        if(Auth::user()->accounts->where('name', $name)->where('id', '<>', $account_id)->count() > 0)
            throw ValidationException::withMessages([
                'name' => 'Un compte existe déjà avec ce nom',
            ])->status(423);
    }

    /**
     * Give the redirection path
     * 
     * @return Router
     */
    private function redirectTo()
    {
        return route_manager('accounts.index');
    }
}
