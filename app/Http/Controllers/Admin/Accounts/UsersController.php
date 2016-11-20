<?php 

namespace App\Http\Controllers\Admin\Accounts;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Utility\UploadFile as UploadFile;
use App\Services\Utility\InjestFile as InjestFile;
use App\Repositories\UserRepository as UserRepository;

class UsersController extends Controller
{

	/*
	|--------------------------------------------------------------------------
	| Users Controller
	|--------------------------------------------------------------------------
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(UserRepository $userRepo)
	{
		$this->repository = $userRepo;
		$this->menuTab = 'users';
	}

	/**
	 * Show the users panel
	 *
	 * @return Response
	 */
	public function index()
	{
		$users = $this->repository->paginateUsers([], 30, false);
		$roles = \App\Models\Role::all();
		// $users = $this->repository->getUsers();
		$menuTab = $this->menuTab;
		$title = 'Users';
		return response()->view('admin.accounts.users.index', compact(['users', 'title', 'roles', 'menuTab']));
	}

	/**
	 * Show the admin users panel
	 *
	 * @return Response
	 */
	public function admins()
	{
		$users = \App\Models\User::whereHas('roles', function($q){
		        	$q->where('name', '!=', env('STUDENT_LABEL', 'Student'));
		    	})->paginate();
		$roles = \App\Models\Role::all();
		$menuTab = 'admins';
		$title = 'Admins';
		return response()->view('admin.accounts.users.index', compact(['users', 'title', 'roles', 'menuTab']));
	}

	/**
	 * Show an individual user's profile
	 *
	 * @return Response
	 */
	public function show($id)
	{
		$user = $this->repository->getUser($id);
		$menuTab = $this->menuTab;
		return response()->view('admin.accounts.users.show', compact(['user', 'menuTab']));
	}

	/**
	 * Show an page to create a profile
	 *
	 * @return Response
	 */
	public function create()
	{
		$menuTab = $this->menuTab;
	    return response()->view('admin.accounts.users.create', compact(['menuTab']));
	}

	/**
	 * Store the newly created user
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
        $validator = $this->validate($request, [
            'first' => 'required|alpha',
            'last' => 'required|alpha',
            'email' => 'required|email|unique:users',
        ]);

        try
        {
        	$newUser = $this->repository->createUser($request->all());

        } catch(\Exception $exception)
        {
            $this->flashErrorAndReturnWithMessage($exception);
        }

        // returns back with success message
        flash()->success('Your user was added!');
        return redirect()->action('Admin\Accounts\UsersController@index');
	}

	/**
	 * Show an individual user's profile
	 *
	 * @return Response
	 */
	public function edit($id)
	{
		$user = $this->repository->getUser($id);
		$menuTab = $this->menuTab;
		return response()->view('admin.accounts.users.edit', compact(['user', 'menuTab']));
	}

	/**
	 * Update the newly created user
	 *
	 * @return Response
	 */
	public function update(Request $request, $id)
	{
        $validator = $this->validate($request, [
            'first' => 'required|alpha',
            'last' => 'required|alpha',
            'email' => 'required|email|unique:users,email,'.$id,
            'address' => 'sometimes|required',
            'address_2' => 'required_with:address',
            'timezone' => 'timezone',
        ]);

        try
        {
        	$updatedUser = $this->repository->updateUser($id, $request->all());

        } catch(\Exception $exception)
        {
            $this->flashErrorAndReturnWithMessage($exception);
        }

        // returns back with success message
        flash()->success('The user was updated!');
        return redirect()->action('Admin\Accounts\UsersController@edit', ['user' => $id]);
	}

	/**
	 * Show an individual user's profile
	 *
	 * @return Response
	 */
	public function editAuth($id)
	{
		$user = $this->repository->getUser($id);
		$menuTab = $this->menuTab;
		return response()->view('admin.accounts.users.authentication', compact(['user', 'menuTab']));
	}

	/**
	 * Store the newly created user
	 *
	 * @return Response
	 */
	public function updateAuth(Request $request, $id)
	{
        $validator = $this->validate($request, [
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
            'question' => 'required',
            'answer' => 'required',
        ]);

    	// send to the user repository
        try
        {
        	$updatedUser = $this->repository->updateUserAuth($id, $request->all());

        } catch(\Exception $exception)
        {
            $this->flashErrorAndReturnWithMessage($exception);
        }

        // returns back with success message
        flash()->success('Your account has been updated');
        return redirect()->action('Admin\Accounts\UsersController@editAuth', ['user' => $id]);
	}

	/**
	 * Show an individual user's profile
	 *
	 * @return Response
	 */
	public function editAvatar($id)
	{
		$user = $this->repository->getUser($id);
		$menuTab = $this->menuTab;
		return response()->view('admin.accounts.users.avatar', compact(['user', 'menuTab']));
	}

	/**
	 * Store the newly created user
	 *
	 * @return Response
	 */
	public function updateAvatar(UploadFile $upload, Request $request, $id)
	{
        $validator = $this->validate($request, [
            'avatar' => 'required|mimes:jpeg,bmp,png,gif,jpg,jpe'
        ]);

        try
        {
			try {

				$userData = [
					'img' => $upload->uploadUserImage($id, $request->avatar)
				];

			} catch (Exception $e) {
	            $this->flashErrorAndReturnWithMessage($exception);
			}

        	$updateUserAvatar = $this->repository->updateUser($id, $userData);

        } catch(\Exception $exception)
        {
            $this->flashErrorAndReturnWithMessage($exception);
        }

        // returns back with success message
        flash()->success('Your avatar was updated.');
        return redirect()->action('Admin\Accounts\UsersController@editAvatar', ['user' => $id]);
	}

	/**
	 * Show an individual user's profile
	 *
	 * @return Response
	 */
	public function importUsers()
	{
		$menuTab = $this->menuTab;
		return response()->view('admin.accounts.users.import', compact(['menuTab']));
	}

	/**
	 * Store the newly created user
	 *
	 * @return Response
	 */
	public function addMultipleUsers(UploadFile $upload, InjestFile $injestFile, Request $request)
	{
        $validator = $this->validate($request, [
            'file' => 'required|mimes:csv,txt,xls'
        ]);

        try
        {
			try {

				$filePath = $upload->uploadTemporaryFile($request->file);
				$usersData = $injestFile->injest($filePath);

			} catch (Exception $e) {
	            $this->flashErrorAndReturnWithMessage($exception);
			}

        	$uploadedUsers = $this->repository->createUsers($usersData);

        } catch(\Exception $exception)
        {
            $this->flashErrorAndReturnWithMessage($exception);
        }

        // returns back with success message
        flash()->success($uploadedUsers->count() . ' users were added.');
        return redirect()->action('Admin\Accounts\UsersController@index');
	}

	/**
	 * Archive the user
	 *
	 * @return Boolean
	 */
	public function destroy($id)
	{

        try
        {
        	$this->repository->deleteUser($id);

        } catch(\Exception $exception)
        {
			return response('There was a problem with this request', 500);
        }

        return response('Excellent!', 200);
	}

	/**
	 * Archive the users
	 *
	 * @return Boolean
	 */
	public function deleteMultipleUsers(Request $request)
	{
		return $request;

        $validator = $this->validate($request, [
            'users' => 'required',
        ]);

        try
        {
        	$this->repository->deleteUsers($request->users);

        } catch(\Exception $exception)
        {
	        return response('There was a problem with this request', 500);
        }

        return response('Excellent!', 200);
	}	

	/**
	 * Attaches Roles to the User, returns back the name of the higest role
	 *
	 * @return Boolean
	 */
	public function attachRoles(Request $request)
	{
        $validator = $this->validate($request, [
            'users' => 'required|array',
            'role' => 'required|array',
        ]);

        try
        {

        	$returnedUsers = [];

        	foreach ($request->users as $user)
        	{
				$userRecord = $this->repository->getUser($user);
				$userRecord->roles()->syncWithoutDetaching($request->role);

				$userRoleLabels = '';
				foreach ($userRecord->roles as $role)
				{
					$userRoleLabels .= makeRoleLabel($role->name, false);
				}

				$returnedUsers[] = [
					'id' => $userRecord->id,
					'roles' => $userRoleLabels,
				];
        	}

        } catch(\Exception $exception)
        {
	        return response('There was a problem with this request', 500);
        }

        return response()->json(['success' => true, 'returnedUsers' => $returnedUsers]);
	}	
}
