<?php

namespace ServiceBundle\Command;

use AppBundle\Enum\ExtraFeeType;
use Doctrine\ORM\Query\ResultSetMapping;
use OrgBundle\Entity\OrgEmployeeExtraFee;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class GoogleApiSheetsCommand extends ContainerAwareCommand
{
    const REDIRECT_URI = 'https://vseinet.ru';
    const CLIENT_ID = '384393873372-7ljpii8oqfuah9pjg74llujrbdsbv549.apps.googleusercontent.com';
    const CLIENT_SECRET = 'Ev0BsQm3YgAhpNCAJn0EOZ_J';
//    const DOC_ID = '1sZDIs4QvDET6OJ093IrPCz6Pc4SRnBEKR_SYnUQc-c4';
    const DOC_ID = '1edUwlhx2700je9JEQegXlIM-WES3h6MUXyiL66zg-h4';

    private $_users = [];

    /**
     * Entity Manager
     *
     * @var EntityManager
     */
    private $_em;

    /**
     * @param EntityManager $em
     */
    public function setEm(EntityManager $em)
    {
        $this->_em = $em;
    }

    /**
     * @return EntityManager
     */
    public function getEm() : EntityManager
    {
        return $this->_em;
    }

    protected function configure()
    {
        $this->setDescription('Google api sheets analyse command')->setName('google:analyse');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setEm($this->getContainer()->get('doctrine.orm.entity_manager'));

        try {
            $client = new \Google_Client();

            if ($credentialsFile = $this->checkServiceAccountCredentialsFile()) {
                // set the location manually
                $client->setAuthConfig($credentialsFile);
            } else {
                $this->echoMessage('Файл авторизации не найден!');

                return;
            }

            $client->setApplicationName("Vseinet_Sheets_Analyse");
            $client->setScopes([\Google_Service_Sheets::SPREADSHEETS_READONLY,]);
            $client->setAccessType('offline');

            $sheets = new \Google_Service_Sheets($client);

            $data = [];
            $range = 'A7:V';

            /**
             * @var $range \Google_Service_Sheets_ValueRange
             */
            $range = $sheets->spreadsheets_values->get(self::DOC_ID, $range, ['majorDimension' => 'ROWS']);

            if (!empty($range->values)) {
                foreach ($range->values as $row) {
                    if (empty($row) || empty($row[2])) {
                        continue;
                    }

                    $data[] = [
                        'employer' => trim($row[2]),
                        'reason' => $row[3],
                        'col-e' => $row[4],
                        'col-f' => $row[5],
                        'col-g' => $row[6],
                        'start' => new \DateTime($row[7]),
                        'end' => !empty($row[8]) ? new \DateTime($row[8]) : '',
                        'bonus' => (int) ($row[9] ?? 0),
                        'antibonus' => (int) ($row[10] ?? 0),
                    ];
                }
            }

            $now = new \DateTime(date('d.m.Y'));

            foreach ($data as $d) {
                if ($d['start']->getTimestamp() == $now->getTimestamp()) { // сегодня
                    $userId = $this->_getUserIdByName($d['employer']);
                    if (!empty($d['end'])) {
                        if ($d['end']->getTimestamp() <= $d['start']->getTimestamp()) { //премия
                            $this->_addOrgEmployeeExtraFee(['user_id' => $userId, 'reason' => $d['reason'], 'amount' => $d['bonus'],]);
                        } else { //списание
                            $this->_addOrgEmployeeExtraFee(['user_id' => $userId, 'reason' => $d['reason'], 'amount' => $d['antibonus'],]);
                        }
                    } else { //списание
                        $this->_addOrgEmployeeExtraFee(['user_id' => $userId, 'reason' => $d['reason'], 'amount' => $d['antibonus'],]);
                    }
                }
            }
        } catch (\Exception $e) {
            echo $e->getMessage().PHP_EOL;
            echo $e->getTraceAsString();
        }
    }

    /**
     * @param array $data
     */
    private function _addOrgEmployeeExtraFee(array $data) : void
    {
        if (!empty($data['amount'])) {
            $model = new OrgEmployeeExtraFee();
            $model->setDate(new \DateTime());
            $model->setCreatedAt(new \DateTime());
            $model->setApprovedAt(new \DateTime());
            $model->setAppliedAt(new \DateTime());
            $model->setUserId($data['user_id']);
            $model->setReason($data['reason']);
            $model->setAmount($data['amount'] * 100);
            $model->setExtraFeeType(ExtraFeeType::TASK);

            $this->getEm()->persist($model);
            $this->getEm()->flush();
        }
    }

    private function _getUserIdByName($name)
    {
        if (array_key_exists($name, $this->_users)) {
            return $this->_users[$name];
        }

        $names = explode(' ', $name);
        $lastname = mb_strtolower($names[0]);
        $firstname = $secondname = '';

        if (!empty($names[1])) {
            $secondNames = explode('.', $names[1]);

            $firstname = mb_strtolower($secondNames[0] ?? '');
            $secondname = mb_strtolower($secondNames[1] ?? '');
        }

        $query = $this->getEm()->createNativeQuery('
            select
                "user".id AS user_id
            from 
                person
                INNER JOIN "user" ON person.id = "user".person_id
                INNER JOIN org_employee ON org_employee.user_id = "user".id
                INNER JOIN org_employment_history ON org_employment_history.org_employee_user_id = org_employee.user_id
            WHERE 
                LOWER(person.lastname) = :lastname
                '.($firstname ? 'AND LOWER(substring(person.firstname from 1 for 1)) = :firstname' : '').'
                '.($secondname ? 'AND LOWER(substring(person.secondname from 1 for 1)) = :secondname' : '').'
                AND org_employment_history.fired_at is null
            ORDER BY "user".id
            LIMIT 1
        ', new ResultSetMapping());
        $query->setParameter('lastname', $lastname);
        $query->setParameter('firstname', $firstname);
        $query->setParameter('secondname', $secondname);

        $rows = $query->getResult('ListAssocHydrator');
        $row = array_shift($rows);

        $userId = $row['user_id'] ?? 0;

        $this->_users[$name] = $userId;

        return $userId;
    }

    /**
     * @return bool|string
     */
    protected function checkServiceAccountCredentialsFile()
    {
        $application_creds = __DIR__ . '/../Keys/Vseinet-Sheets-757c5350b1e7.json';

        return file_exists($application_creds) ? $application_creds : false;
    }

    /**
     * @param string $message
     */
    protected function echoMessage(string $message) : void
    {
        echo '['.date('d.m.Y H:i:s').'] '.$message.PHP_EOL;
    }
}
