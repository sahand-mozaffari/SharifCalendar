<?php
namespace Sharif\CalendarBundle\Security;
use Doctrine\ORM\EntityManager;
use Fp\OpenIdBundle\Model\UserManager;
use Fp\OpenIdBundle\Model\IdentityManagerInterface;
use Sharif\CalendarBundle\Entity\OpenIdIdentity;
use Sharif\CalendarBundle\Entity\User;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;

class OpenIdUserManager extends UserManager {
	public function __construct(IdentityManagerInterface $identityManager,
	        EntityManager $entityManager) {
		parent::__construct($identityManager);
		$this->entityManager = $entityManager;
	}

	/**
	 * @param string $identity
	 * An OpenID token.
	 * @param array $attributes
	 * Requested attributes
	 */
	public function createUserFromIdentity($identity,
	        array $attributes = array()) {
		global $kernel;
		if ('AppCache' == get_class($kernel)) {
			$kernel = $kernel->getKernel();
		}

		$session = $kernel->getContainer()->get('session');
		$command = $session->get('create_open_id_action');

		if($command == "create_new") {
			$user = new User($attributes['namePerson'],
			        $attributes['contact/email']);
			$openIdIdentity = new OpenIdIdentity($identity, $user);
			$user->addOpenId($openIdIdentity);

			$this->entityManager->persist($user);
			$this->entityManager->flush();
			return $user;
		} else if($command == "throw_exception") {
			throw new \Exception('Identity_not_recognized_use_signup_form');
		} else if($command == "update") {

		} else {
			throw new \Exception('Something is wrong here!');
		}
	}
}
