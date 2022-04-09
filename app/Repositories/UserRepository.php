<?php

namespace App\Repositories;

use App\Models\TenantUser;
use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;

/**
 * Class UserRepository
 * @package App\Repositories
 * @version April 4, 2022, 9:27 pm UTC
 */

class UserRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'email'
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return User::class;
    }

    public function storeCentralTenantUser($request)
    {
        $user = new TenantUser();
        $user->tenant = $request['tenant'];
        $user->email = $request['email'];
        $user->phone = $request['phone'];
        $user->domain = $request['domain'];
        $user->google_token = $request['google_token'];
        $val = $user->save();
        return  $val;
    }
}
