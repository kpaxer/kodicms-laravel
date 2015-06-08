<?php namespace KodiCMS\Users\Repository;

use KodiCMS\Users\Model\User;
use KodiCMS\CMS\Repository\BaseRepository;

class UserRepository extends BaseRepository
{
	/**
	 * @param User $model
	 */
	function __construct(User $model)
	{
		parent::__construct($model);
	}

	public function paginate($perPage = null)
	{
		return $this->model->with('roles')->paginate();
	}

	/**
	 * @param array $data
	 * @return bool
	 * @throws \KodiCMS\CMS\Exceptions\ValidationException
	 */
	public function validateOnCreate(array $data = [])
	{
		$validator = $this->validator($data, [
			'email' => 'required|email|max:255|unique:users',
			'password' => 'required|confirmed|min:6',
			'username' => 'required|max:255|min:3|unique:users'
		]);

		return $this->_validate($validator);
	}

	/**
	 * @param integer $id
	 * @param array $data
	 * @return bool
	 * @throws \KodiCMS\CMS\Exceptions\ValidationException
	 */
	public function validateOnUpdate($id, array $data = [])
	{
		$validator = $this->validator($data, [
			'email' => "required|email|max:255|unique:users,email,{$id}",
			'username' => "required|max:255|min:3|unique:users,username,{$id}"
		]);

		$validator->sometimes('password', 'required|confirmed|min:6', function ($input)
		{
			return !empty($input->password);
		});

		return $this->_validate($validator);
	}

	/**
	 * @param array $data
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function create(array $data = [])
	{
		$user =  parent::create(array_only($data, [
			'username', 'password', 'email', 'locale'
		]));

		if (isset($data['user_roles']))
		{
			$roles = $data['user_roles'];
			$user->roles()->attach($roles);
		}

		return $user;
	}

	/**
	 * @param int $id
	 * @param array $data
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function update($id, array $data = [])
	{
		if(array_key_exists('password', $data) AND empty($data['password']))
		{
			unset($data['password']);
		}

		$user = parent::update($id, array_only($data, [
			'username', 'password', 'email', 'locale'
		]));

		if (isset($data['user_roles']))
		{
			$roles = $data['user_roles'];
			$user->roles()->sync($roles);
		}

		return $user;
	}
}