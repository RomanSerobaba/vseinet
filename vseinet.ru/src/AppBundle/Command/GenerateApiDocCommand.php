<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateApiDocCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setDescription('Generation of API documentaion.')
            ->setName('generate:api:doc')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $docs = $this->getContainer()->get('api.doc.generator')->generate();
        $connection = $this->getContainer()->get('doctrine')->getManager()->getConnection();

        $sections = [];
        foreach ($docs as $doc) {
            if (empty($doc['section'])) {
                // $output->writeln(sprintf('<error>For route %s section is not defined.</error>', print_r($doc, true)));
                continue;
            }
            $sections[$doc['section']] = $doc['section'];
        }

        $parameters = [];
        foreach ($sections as $section) {
            $parameters[] = $section;
            $parameters[] = '';
        }
        $placeholders = implode(',', array_fill(0, count($sections), '(?::text, ?::text)'));
        $stmt = $connection->prepare("
            WITH 
                data (name, description) AS (
                    VALUES {$placeholders}
                )
            INSERT INTO api_method_section (name, description)
            SELECT 
                name,
                description 
            FROM data 
            WHERE NOT EXISTS (
                SELECT 1 
                FROM api_method_section 
                WHERE LOWER(api_method_section.name) = LOWER(data.name)
            )
        ");
        $stmt->execute($parameters);

        $stmt = $connection->query("
            SELECT 
                LOWER(name),
                id,
                name
            FROM api_method_section
        ");
        $sections = $stmt->fetchAll(\PDO::FETCH_ASSOC | \PDO::FETCH_GROUP);
        foreach ($docs as $doc) {
            if (empty($doc['section'])) {
                continue;
            }
            $sections[mb_strtolower($doc['section'], 'UTF-8')]['docs'][] = $doc;
        }

        $parameters = [];
        foreach ($sections as $section) {
            if (empty($section['docs'])) {
                continue;
            }
            foreach ($section['docs'] as $index => $doc) {
                $parameters[] = $doc['route']['name'];
                $parameters[] = $section[0]['id'];
                $parameters[] = $doc['route']['method'];
                $parameters[] = $doc['route']['path'];
                $parameters[] = json_encode($doc['parameters']);
                $parameters[] = isset($doc['responses']) ? json_encode($doc['responses']) : '';
                $parameters[] = $doc['route']['description'];
                $parameters[] = $index + 1;
                $parts = explode('/', trim($doc['route']['path'], '/'));
                if ('api' == $parts[0] && 0 === strpos($parts[1], 'v')) {
                    $parts = array_slice($parts, 2);
                }
                array_walk($parts, function(&$part) {
                    $part = preg_replace('/(?<!^)[A-Z]/', '_$0', $part);
                    if (substr($part, 0, 1) == '{') {
                        $part = 'BY_'.trim($part, '{}');
                    }
                });
                $parameters[] = strtoupper($doc['route']['method'].'_'.implode('_', $parts));
                $parameters[] = date('Y-m-d H:i:s');
            }
        }
        $columns = [
            'name' => 'text',
            'api_method_section_id' => 'integer',
            'method' => 'text',
            'path' => 'text',
            'parameters' => 'text',
            'responses' => 'text',
            'description' => 'text',
            'sort_order' => 'integer',
            'access_right' => 'text',
            'created_at' => 'datetime',
        ];
        $placeholders = [];
        foreach ($columns as $column => $type) {
            if ('datetime' == $type) {
                $placeholders[] = "to_timestamp(?, 'YYYY-MM-DD HH24:MI:SS')";
                continue;
            }
            $placeholders[] = "?::{$type}";
        }
        $placeholders = implode(',', array_fill(0, count($parameters) / 10, '('.implode(', ', $placeholders).')'));
        $columns = implode(', ', array_keys($columns));
        $stmt = $connection->prepare("
            WITH 
                data ($columns) AS (
                    VALUES {$placeholders}
                ),
                updated AS (
                    UPDATE api_method
                    SET 
                        name = data.name,
                        api_method_section_id = data.api_method_section_id,
                        parameters = data.parameters,
                        responses = data.responses,
                        description = data.description,
                        sort_order = data.sort_order,
                        access_right = data.access_right
                    FROM data 
                    WHERE UPPER(api_method.method) = UPPER(data.method) AND LOWER(api_method.path) = LOWER(data.path) 
                    RETURNING api_method.method, api_method.path
                )
            INSERT INTO api_method ({$columns})
            SELECT {$columns}
            FROM data 
            WHERE NOT EXISTS (
                SELECT 1 
                FROM updated
                WHERE UPPER(updated.method) = UPPER(data.method) AND LOWER(updated.path) = LOWER(data.path) 
            )
        ");
        $stmt->execute($parameters);

        $stmt = $connection->prepare("
            WITH 
                data ($columns) AS (
                    VALUES {$placeholders}
                )
            DELETE FROM api_method 
            WHERE NOT EXISTS (
                SELECT 1
                FROM data
                WHERE UPPER(api_method.method) = UPPER(data.method) AND LOWER(api_method.path) = LOWER(data.path)
            )
        ");
        $stmt->execute($parameters);

        $stmt = $connection->exec("
            DELETE FROM api_method_section
            WHERE NOT EXISTS (
                SELECT 1 
                FROM api_method 
                WHERE api_method.api_method_section_id = api_method_section.id    
            )            
        ");

        if ('dev' == $this->getContainer()->getParameter('kernel.environment')) {
            // $stmt = $connection->query("
            //     SELECT 
            //         LOWER(ams.name),
            //         ams.id,
            //         ams.name
            //     FROM api_method_section ams
            //     INNER JOIN api_method am ON am.api_method_section_id = ams.id
            //     WHERE NOT EXISTS (
            //         SELECT 1
            //         FROM resource_method rm 
            //         WHERE rm.api_method_id = am.id
            //     )
            //     GROUP BY ams.id
            // ");
            // $sections = $stmt->fetchAll(\PDO::FETCH_ASSOC | \PDO::FETCH_GROUP);
            // if (!empty($sections)) {
            //     $stmt = $connection->prepare("
            //         SELECT id FROM resource WHERE LOWER(resource.name) = :name
            //     ");
            //     foreach ($sections as $name => $section) {
            //         $stmt->execute(['name' => $name]);
            //         $sections[$name]['resource_id'] = $stmt->fetchColumn();
            //     }

            //     $stmt = $connection->prepare("
            //         INSERT INTO resource (name, resource_group_id) VALUES (:name, 36) RETURNING id
            //     ");
            //     foreach ($sections as $name => $section) {
            //         if (empty($section['resource_id'])) {
            //             $stmt->execute(['name' => $section[0]['name']]);
            //             $sections[$name]['resource_id'] = $stmt->fetchColumn();
            //         }
            //     }

            //    $parameters = [];
            //    foreach ($sections as $section) {
            //        $parameters[] = $section[0]['id'];
            //        $parameters[] = $section['resource_id'];
            //    }

            //    $placeholders = implode(',', array_fill(0, count($parameters) / 2, '(?::integer, ?::integer)'));

            //    $stmt = $connection->prepare("
            //        WITH
            //            data (api_method_section_id, resource_id) AS (
            //                VALUES {$placeholders}
            //            )
            //        INSERT INTO resource_method (resource_id, api_method_id)
            //        SELECT 
            //            data.resource_id,
            //            am.id 
            //        FROM api_method am 
            //        INNER JOIN data ON data.api_method_section_id = am.api_method_section_id
            //        WHERE NOT EXISTS (
            //            SELECT 1
            //            FROM resource_method
            //            WHERE resource_method.api_method_id = am.id 
            //        )
            //    ");
            //    $stmt->execute($parameters);
            // }

            $stmt = $connection->prepare("
                INSERT INTO resource_acl_subrole_codex (acl_subrole_id, resource_id)
                SELECT 
                    :acl_subrole_id,
                    id 
                FROM resource 
                ON CONFLICT (acl_subrole_id, resource_id)
                DO NOTHING
            ");
            $stmt->execute(['acl_subrole_id' => 1]);
            $stmt->execute(['acl_subrole_id' => 22]);

            $stmt = $connection->prepare("
                INSERT INTO api_method_acl_subrole_codex (acl_subrole_id, api_method_id)
                SELECT 
                    :acl_subrole_id,
                    id 
                FROM api_method 
                ON CONFLICT (acl_subrole_id, api_method_id)
                DO NOTHING
            ");
            $stmt->execute(['acl_subrole_id' => 1]);
            $stmt->execute(['acl_subrole_id' => 22]);
        }

        $output->writeln('<info>API methods was updated.</info>');
    }
}