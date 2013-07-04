<?php
namespace Sharif\CalendarBundle\Security;
use Doctrine\ORM\EntityManager;
use Fp\OpenIdBundle\Model\UserManager;
use Fp\OpenIdBundle\Model\IdentityManagerInterface;
use Sharif\CalendarBundle\Entity\OpenIdIdentity;
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
		echo "hi";
		die();
		if (false === isset($attributes['contact/email'])) {
			throw new \Exception('We need your e-mail address!');
		}

		$email = $attributes['contact/email'];
		$user = $this->entityManager->getRepository('AcmeDemoBundle:User')
		        ->findOneBy(array('id' => 2));

		if (null === $user) {
			throw new BadCredentialsException('No corresponding user! [email='.
			                                  $email.']');
		}

		$openIdIdentity = new OpenIdIdentity();
		$openIdIdentity->setId($identity);
// 		$openIdIdentity->setAttributes($attributes);
		$openIdIdentity->setUser($user);

		$this->entityManager->persist($openIdIdentity);
		$this->entityManager->flush();

		// end of example

		return $user; // you must return an UserInterface instance (or throw an exception)
	}
}
