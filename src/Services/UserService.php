<?php
namespace Szurubooru\Services;

class UserService
{
	private $validator;
	private $userDao;
	private $passwordService;
	private $emailService;
	private $timeService;

	public function __construct(
		\Szurubooru\Validator $validator,
		\Szurubooru\Dao\UserDao $userDao,
		\Szurubooru\Services\PasswordService $passwordService,
		\Szurubooru\Services\EmailService $emailService,
		\Szurubooru\Services\TimeService $timeService)
	{
		$this->validator = $validator;
		$this->userDao = $userDao;
		$this->passwordService = $passwordService;
		$this->emailService = $emailService;
		$this->timeService = $timeService;
	}

	public function register(\Szurubooru\FormData\RegistrationFormData $formData)
	{
		$this->validator->validateUserName($formData->name);
		$this->validator->validatePassword($formData->password);
		$this->validator->validateEmail($formData->email);

		if ($this->userDao->getByName($formData->name))
			throw new \DomainException('User with this name already exists.');

		//todo: privilege checking

		$user = new \Szurubooru\Entities\User();
		$user->name = $formData->name;
		$user->email = $formData->email;
		$user->passwordHash = $this->passwordService->getHash($formData->password);
		$user->accessRank = $this->userDao->hasAnyUsers()
			? \Szurubooru\Entities\User::ACCESS_RANK_REGULAR_USER
			: \Szurubooru\Entities\User::ACCESS_RANK_ADMINISTRATOR;
		$user->registrationTime = $this->timeService->getCurrentTime();
		$user->lastLoginTime = null;

		//todo: send activation mail if necessary

		return $this->userDao->save($user);
	}
}