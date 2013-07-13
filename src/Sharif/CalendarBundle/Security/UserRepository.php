<?php
namespace Sharif\CalendarBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;

class UserRepository extends EntityRepository implements UserProviderInterface {
	public function __construct($em) {
		parent::__construct($em,
		        new ClassMetaData('Sharif\CalendarBundle\Entity\User'));
	}

	/**
	 * Loads the user for the given username.
	 * This method must throw UsernameNotFoundException if the user is not
	 * found.
	 *
	 * @param string $username The username
	 *
	 * @return UserInterface
	 * @see UsernameNotFoundException
	 * @throws UsernameNotFoundException if the user is not found
	 */
	public function loadUserByUsername($username) {
		$em = $this->getEntityManager();
		$query = $em
		        ->createQuery(
		                'SELECT u FROM Sharif\CalendarBundle\Entity\User u WHERE u.username=?1')
		        ->setParameter(1, $username);
		return $query->getSingleResult();
	}

	/**
	 * Refreshes the user for the account interface.
	 *
	 * It is up to the implementation to decide if the user data should be
	 * totally reloaded (e.g. from the database), or if the UserInterface
	 * object can just be merged into some internal array of users / identity
	 * map.
	 * @param UserInterface $user
	 *
	 * @return UserInterface
	 *
	 * @throws UnsupportedUserException if the account is not supported
	 */
	public function refreshUser(UserInterface $user) {
		$em = $this->getEntityManager();
		$query = $em
		        ->createQuery(
		                'SELECT u FROM Sharif\CalendarBundle\Entity\User u WHERE u.id = ?1')
		        ->setParameter(1, $user->getId());
		$users = $query->getResult();
		return $users[0];
	}

	/**
	 * Whether this provider supports the given user class
	 *
	 * @param string $class
	 *
	 * @return Boolean
	 */
	public function supportsClass($class) {
		return $this->getEntityName() === $class
		        || is_subclass_of($class, $this->getEntityName());
	}
}
