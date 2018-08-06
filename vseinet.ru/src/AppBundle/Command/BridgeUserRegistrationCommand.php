<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class BridgeUserRegistrationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setDescription('User change password.')
            ->setName('executor:user:registration')
            ->addArgument('data', InputArgument::REQUIRED, 'Data?')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $data = json_decode($input->getArgument('data'), true);
        $container = $this->getContainer();
        $connection = $container->get('doctrine')->getManager()->getConnection();

        $connection->beginTransaction();
        
        try {
            $stmt = $connection->prepare("
                INSERT INTO person (lastname, firstname, secondname, birthday)
                VALUES (:lastname, :firstname, :secondname, :birthday)
            ");
            $stmt->execute([
                'lastname' => $data['lastname'],
                'firstname' => $data['firstname'],
                'secondname' => $data['secondname'],
                'birthday' => $data['birthdate'],
            ]);
            $personId = $connection->lastInsertId();

            $stmt = $connection->prepare("
                INSERT INTO \"user\" (id, password, registered_at, person_id, geo_city_id, is_marketing_subscribed)
                VALUES (:id, :password, :registered_at, :person_id, :geo_city_id, :is_marketing_subscribed)
            ");
            $stmt->execute([
                'id' => $data['id'],
                'password' => password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 4]),
                'registered_at' => date('Y-m-d H:i:s'),
                'person_id' => $personId,
                'geo_city_id' => $data['city_id'],
                'is_marketing_subscribed' => $data['is_subscribed'] ? 't' : 'f',
            ]);

            $stmt = $connection->prepare("
                INSERT INTO user_to_acl_subrole (user_id, acl_subrole_id)
                (
                    SELECT 
                        :user_id,
                        asr.id
                    FROM acl_role ar 
                    INNER JOIN acl_subrole asr ON asr.acl_role_id = ar.id 
                    WHERE ar.code = 'CLIENT'
                )
            ");
            $stmt->execute(['user_id' => $data['id']]);

            if ($data['phone']) {
                $stmt = $connection->prepare("
                    INSERT INTO contact (contact_type_code, value, person_id, geo_city_id)
                    VALUES (:contact_type_code, :value, :person_id, :geo_city_id)
                ");
                $stmt->execute([
                    'contact_type_code' => 'mobile', 
                    'value' => $data['phone'], 
                    'person_id' => $personId, 
                    'geo_city_id' => $data['city_id'],
                ]);
            }

            if ($data['email']) {
                $stmt = $connection->prepare("
                    INSERT INTO contact (contact_type_code, value, person_id, geo_city_id)
                    VALUES (:contact_type_code, :value, :person_id, :geo_city_id)
                ");
                $stmt->execute([
                    'contact_type_code' => 'email', 
                    'value' => $data['email'], 
                    'person_id' => $personId, 
                    'geo_city_id' => $data['city_id'],
                ]);
            }

            $connection->commit();

            $output->writeln('OK! User was registered.');

        } catch (Exception $e) {
            $connection->rollback();

            $logger = new Logger('bridge');
            $this->logger->pushHandler(new StreamHandler($container->getParameter('kernel.logs_dir').'/bridge/bridge-'.date('Y-m-d').'.log', Logger::ERROR));
            $logger->error('UserRegister failure. Data :data.', ['data' => json_encode($data)]);

            $output->writeln(sprintf('User (%d) registration failure.', $data['id']));
        }
    }
}