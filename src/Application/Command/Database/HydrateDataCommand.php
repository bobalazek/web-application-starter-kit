<?php

namespace Application\Command\Database;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class HydrateDataCommand
    extends ContainerAwareCommand
{
    protected $app;

    public function __construct(
        $name,
        \Silex\Application $app
    ) {
        parent::__construct($name);

        $this->app = $app;
    }

    protected function configure()
    {
        $this
            ->setName(
                'application:database:hydrate-data'
            )
            ->setDescription('Add an Test User to the database')
            ->addOption(
                'remove-existing-data',
                'r',
                InputOption::VALUE_NONE,
                'When the existing data should be removed'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->app;

        $removeExistingData = $input->getOption('remove-existing-data');

        if ($removeExistingData) {
            try {
                $app['db']->query('SET foreign_key_checks = 0;');

                $tables = $app['db']->getSchemaManager()->listTables();

                foreach ($tables as $table) {
                    $table = $table->getName();

                    $app['db']->query('TRUNCATE TABLE '.$table.';');
                }

                $app['db']->query('SET foreign_key_checks = 1;');

                $output->writeln('<info>All tables were successfully truncated!</info>');
            } catch (\Exception $e) {
                $output->writeln('<error>'.$e->getMessage().'</error>');
            }
        }

        /***** Roles *****/
        $roles = include APP_DIR.'/fixtures/roles.php';

        foreach ($roles as $role) {
            $roleEntity = new \Application\Entity\RoleEntity();
            $roleEntity
                ->setId($role[0])
                ->setName($role[1])
                ->setDescription($role[2])
                ->setRole($role[3])
                ->setPriority($role[4])
            ;

            $app['orm.em']->persist($roleEntity);
        }

        /*
         * We already need to flush the first time here,
         *   else the roles are not available later for the users,
         *   who want to use them.
         */
        $app['orm.em']->flush();

        /***** Users *****/
        $users = include APP_DIR.'/fixtures/users.php';

        foreach ($users as $user) {
            $userEntity = new \Application\Entity\UserEntity();
            $profileEntity = new \Application\Entity\ProfileEntity();

            // Profile
            $profileEntity
                ->setFirstName($user['profile']['firstName'])
                ->setLastName($user['profile']['lastName'])
            ;

            if (isset($user['profile']['gender'])) {
                $profileEntity
                    ->setGender($user['profile']['gender'])
                ;
            }

            if (isset($user['profile']['birthdate'])) {
                $profileEntity
                    ->setBirthdate($user['profile']['birthdate'])
                ;
            }

            // User Roles
            $userRolesCollection = new \Doctrine\Common\Collections\ArrayCollection();

            if (! empty($user['roles'])) {
                $userRoles = $user['roles'];

                foreach ($userRoles as $userRole) {
                    $roleEntity = $app['orm.em']
                        ->getRepository('Application\Entity\RoleEntity')
                        ->findOneByRole($userRole)
                    ;

                    if ($roleEntity) {
                        $userRolesCollection->add($roleEntity);
                    }
                }
            }

            // User
            $userEntity
                ->setId($user['id'])
                ->setUsername($user['username'])
                ->setEmail($user['email'])
                ->setPlainPassword(
                    $user['plainPassword'],
                    $app['security.encoder_factory']
                )
                ->setRoles($userRolesCollection)
                ->setProfile($profileEntity)
                ->enable()
            ;

            $app['orm.em']->persist($userEntity);
        }

        try {
            $app['orm.em']->flush();

            $output->writeln('<info>Data was successfully hydrated!</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>'.$e->getMessage().'</error>');
        }
    }
}
