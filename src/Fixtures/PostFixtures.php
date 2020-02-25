<?php declare(strict_types=1);

/*
 * Created by BonBonSlick
 */

namespace App\Fixtures;

use App\Entity\PostFactoryInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class self
 */
final class PostFixtures extends Fixture
{
    /**
     * @var PostFactoryInterface
     */
    private $factory;

    /**
     * self constructor.
     *
     * @param PostFactoryInterface $factory
     *
     * @todo - DI is broken, smth wrong with cache and container
     */
    public function __construct(PostFactoryInterface $factory) // does not work
//    public function __construct(EntityManagerInterface $factory) // works fine
    {
//        ERROR
//        In DefinitionErrorExceptionPass.php line 54:
//
//  Cannot autowire service "App\Fixtures\PostFixtures": argument "$factory" of method "__construct()"
// references interface "App\Entity\PostFactoryInterface" but no such service exists. Did you create a class that implements this
//  interface?
        $this->factory = $factory;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $manager->flush();
    }
}
