<?php
namespace IesOretania\AticaCoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use IesOretania\AticaCoreBundle\Entity\Person;
use IesOretania\AticaCoreBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserPersonData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function load(ObjectManager $manager)
    {
        $personAdmin = new Person();
        $personAdmin
            ->setDescription('Administrador')
            ->setDisplayName('Admin')
            ->setFirstName('Admin')
            ->setLastName('Admin')
            ->setGender(Person::GENDER_UNKNOWN)
            ->setReference('admin');

        $userAdmin = new User();
        $userAdmin
            ->setEnabled(true)
            ->setGlobalAdministrator(true)
            ->setUserName('admin')
            ->setPassword($this->container->get('security.password_encoder')->encodePassword($userAdmin, 'admin'))
            ->setEmail('admin@example.com')
            ->setPerson($personAdmin);

        $manager->persist($personAdmin);
        $manager->persist($userAdmin);

        $this->addReference('admin-user', $userAdmin);

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
