<?php 

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;

/**
 * @VIA\Section("Документация")
 */
class ApiDocController extends RestController
{
    /**
     * @VIA\Get(path="/api/doc/")
     */
    public function indexAction()
    {
        $content = str_replace(
            '"/api-docs', 
            '"https://vseinet.bitbucket.io/api-docs', 
            file_get_contents('https://vseinet.bitbucket.io/api-docs/index.html')
        );

        return $this->render('api.doc.html.twig', [ 
            'content' => $content,
        ]);
    }

    /**
     * @VIA\Get(path="/api/spec/")
     */
    public function specAction()
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT 
                ams.id,
                ams.name
            FROM AppBundle:ApiMethodSection ams 
        ");
        $sections = $q->getResult('ListHydrator');

        $q = $em->createQuery("
            SELECT am 
            FROM AppBundle:ApiMethod am 
            ORDER BY am.sortOrder 
        ");
        $methods = $q->getArrayResult();

        $collection = [];
        foreach ($methods as $method) {
            $section = $sections[$method['sectionId']];
            if (empty($collection[$section])) {
                $collection[$section] = [
                    'section' => $section,
                    'methods' => [],
                ];
            }
            $collection[$section]['methods'][] = [
                'accessRight' => $method['accessRight'],
                'route' => [
                    'name' => $method['name'],
                    'method' => $method['method'],
                    'path' => $method['path'],
                    'description' => $method['description'],
                ],
                'parameters' => json_decode($method['parameters'], true),
                'responses' => json_decode($method['responses'], true),  
                'since' => $method['createdAt'],  
            ];
        }

        uksort($collection, function($item1, $item2) {
            return strcasecmp($item1, $item2);
        });

        return array_values($collection);   
    }

    /**
     * @VIA\Get(path="/api/spec2/")
     */
    public function spec2Action()
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT 
                r.id, 
                r.name
            FROM AppBundle:Resource r 
        ");
        $resources = $q->getResult('ListHydrator');

        $q = $em->createQuery("
            SELECT 
                rm.resourceId,
                am.name,
                am.method,
                am.path,
                am.parameters,
                am.responses,
                am.description,
                am.accessRight,
                am.createdAt
            FROM AppBundle:ResourceMethod rm 
            INNER JOIN AppBundle:ApiMethod am WITH am.id = rm.apiMethodId
            ORDER BY am.name  
        ");
        $methods = $q->getArrayResult();

        $collection = [];
        foreach ($methods as $method) {
            $page = $resources[$method['resourceId']];
            if (empty($collection[$page])) {
                $collection[$page] = [
                    'section' => $page,
                    'methods' => [],
                    'id' => $method['resourceId'],
                ];
            }
            $collection[$page]['methods'][] = [
                'accessRight' => $method['accessRight'],
                'route' => [
                    'name' => $method['name'],
                    'method' => $method['method'],
                    'path' => $method['path'],
                    'description' => $method['description'],
                ],
                'parameters' => json_decode($method['parameters'], true),
                'responses' => json_decode($method['responses'], true),
                'since' => $method['createdAt'],
            ];
        }

        uksort($collection, function($item1, $item2) {
            return strcasecmp($item1, $item2);
        });
        
        return array_values($collection);
    }
}