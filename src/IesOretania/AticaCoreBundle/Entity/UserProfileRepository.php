<?php

namespace IesOretania\AticaCoreBundle\Entity;

use AppBundle\Form\Model\ProfileElementModel;
use Doctrine\ORM\EntityRepository;

class UserProfileRepository extends EntityRepository
{
    public function getAllProfiles(Organization $organization)
    {
        $profiles = $this->getEntityManager()->getRepository('AticaCoreBundle:Profile')
            ->createQueryBuilder('p')
            ->select('p.id AS pr, el.id AS elem')
            ->leftJoin('AticaCoreBundle:Element', 'el', 'WITH', 'el.enumeration = p.enumeration')
            ->where('p.organization = :org')
            ->setParameter('org', $organization)
            ->orderBy('p.nameNeutral')
            ->getQuery()
            ->getResult();

        $profile = null;
        $userProfiles = [];

        foreach($profiles as $item) {
            $profile = $this->getEntityManager()->getRepository('AticaCoreBundle:Profile')->find($item['pr']);
            $element = $item['elem'] ? $this->getEntityManager()->getRepository('AticaCoreBundle:Element')->find($item['elem']) : null;
            $profileElement = new ProfileElementModel($profile, $element);
            $userProfiles[] = $profileElement;
        }

        return $userProfiles;
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function deleteAllFromUser(User $user)
    {
        return $this->getEntityManager()->getRepository('AticaCoreBundle:UserProfile')
            ->createQueryBuilder('up')
            ->where('up.user = :user')
            ->setParameter('user', $user)
            ->delete()
            ->getQuery()
            ->execute();
    }

    /**
     * @param User $user
     * @param ProfileElementModel[] $profileElementModels
     */
    public function addToUser(User $user, $profileElementModels)
    {
        // añadir los seleccionados
        /**
         * @var ProfileElementModel $profileElement
         */
        foreach($profileElementModels as $profileElement) {
            $userProfile = new UserProfile();
            $userProfile
                ->setUser($user)
                ->setProfile($profileElement->getProfile())
                ->setElement($profileElement->getElement());
            $this->getEntityManager()->persist($userProfile);
        }
    }

    /**
     * @param User $user
     * @param ProfileElementModel[] $profileElementModels
     */
    public function setToUser(User $user, $profileElementModels)
    {
        // borrar todos los perfiles del usuario
        $this->deleteAllFromUser($user);
        $this->addToUser($user, $profileElementModels);
    }
}
